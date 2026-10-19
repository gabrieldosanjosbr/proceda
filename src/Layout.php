<?php

namespace Proceda;

use Proceda\Schema\LayoutType;
use Proceda\Schema\LayoutVersion;
use Proceda\Schema\Manager;
use Proceda\Schema\RegistroResolver;
use Proceda\Service\BuilderRegistry;
use Proceda\Service\PropertyResolver;
use Proceda\Service\TypeResolver;
use Random\RandomException;

final readonly class Layout
{
    public function __construct(
        public LayoutType        $type,
        public LayoutVersion     $version,

        private Charset          $charset,
        private Manager          $schemaManager,
        private RegistroResolver $registroResolver,
        private PropertyResolver $propertyResolver,
        private TypeResolver     $typeResolver,

        public ?string           $profile = null,
    ) {}

    /**
     * @throws \ReflectionException|RandomException
     */
    public function read(string $filename): Result
    {
        $reader = new Reader(
            $this->schemaManager,
            new BuilderRegistry(),
            $this->typeResolver,
        );

        foreach ($this->readFile($filename) as $lineNumber => $content) {
            $line = $this->createLine($content, $lineNumber);

            if ($line === null) {
                continue;
            }

            $reader->read($line);
        }

        $reader->validateRequiredRegistros();

        return $reader->getResult();
    }


    /**
     * @throws \ReflectionException|RandomException
     */
    public function readFromString(string $contents): Result
    {
        $reader = new Reader(
            $this->schemaManager,
            new BuilderRegistry(),
            $this->typeResolver
        );

        $lines = preg_split(
            "/\r\n|\n|\r/",
            trim($contents),
        );

        foreach (array_reverse($lines, true) as $index => $content) {
            $line = $this->createLine($content, $index + 1);

            if ($line === null) {
                continue;
            }

            $reader->read($line);
        }

        $reader->validateRequiredRegistros();

        return $reader->getResult();
    }

    public function getSchemaManager(): Manager
    {
        return $this->schemaManager;
    }

    public function getRegistroResolver(): RegistroResolver
    {
        return $this->registroResolver;
    }

    public function getPropertyResolver(): PropertyResolver
    {
        return $this->propertyResolver;
    }

    public function getTypeResolver(): TypeResolver
    {
        return $this->typeResolver;
    }

    /**
     * Reads a text file from bottom to top.
     *
     * Child layouts are processed before their parents so builders can
     * assemble immutable object graphs from the leaves upward.
     */
    private function readFile(string $filename): \Generator
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new \RuntimeException(
                sprintf('File "%s" is not readable.', $filename),
            );
        }

        $handle = fopen($filename, 'rb');

        if ($handle === false) {
            throw new \RuntimeException(
                sprintf('Unable to open file "%s".', $filename),
            );
        }

        fseek($handle, 0, SEEK_END);

        $size = ftell($handle);

        if ($size === 0) {
            fclose($handle);

            return;
        }

        /*
         * Descobre quantidade de linhas físicas.
         */
        $file = new \SplFileObject($filename, 'r');
        $file->seek(PHP_INT_MAX);

        $lineNumber = $file->key() + 1;

        /*
         * SplFileObject considera a linha vazia depois do último
         * newline. Se o arquivo termina com newline, removemos ela.
         */
        fseek($handle, -1, SEEK_END);
        $lastChar = fgetc($handle);

        if ($lastChar === "\n" || $lastChar === "\r") {
            $lineNumber--;
        }

        fseek($handle, 0, SEEK_END);

        $pos = $size;
        $buffer = '';
        $chunkSize = 4096;
        $firstChunk = true;

        while ($pos > 0) {
            $readSize = min($pos, $chunkSize);
            $pos -= $readSize;

            fseek($handle, $pos);

            $chunk = fread($handle, $readSize);

            if ($chunk === false) {
                break;
            }

            $buffer = $chunk . $buffer;

            $lines = preg_split(
                '/\r\n|\r|\n/',
                $buffer,
            );

            $buffer = array_shift($lines);

            /*
             * Se o arquivo termina em newline, preg_split()
             * cria um elemento vazio no final.
             */
            if ($firstChunk && end($lines) === '') {
                array_pop($lines);
            }

            $firstChunk = false;

            for ($i = count($lines) - 1; $i >= 0; $i--) {
                yield $lineNumber-- => $lines[$i];
            }
        }

        if ($buffer !== '') {
            yield $lineNumber => $buffer;
        }

        fclose($handle);
    }

    /**
     * @throws RandomException
     */
    private function createLine(string $content, int $number): ?Line
    {
        $identificacao = trim(
            substr($content, 0, 3)
        );

        if ($identificacao === '') {
            return null;
        }

        $registro = $this->registroResolver->get(
            substr($content, 0, 3),
        );

        return new Line(
            content: $this->charset->toIso($content),
            registro: $registro,
            number: $number,
        );
    }
}
