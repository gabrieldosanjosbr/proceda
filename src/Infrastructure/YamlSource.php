<?php

namespace Proceda\Infrastructure;

use Proceda\Schema\LayoutType;
use Proceda\Schema\LayoutVersion;
use Symfony\Component\Yaml\Yaml;

final class YamlSource
{
    /** @var array<int, array> */
    private array $definitions = [];

    private function __construct(
        string $path,
        ?string $overridePath = null,
        array $exclude = [],
    ) {
        $this->load($path, $exclude);

        if (
            $overridePath !== null
            && file_exists($overridePath)
        ) {
            $this->load($overridePath, $exclude);
        }
    }

    public static function fromPath(
        string $path,
        ?string $overridePath = null,
        array $exclude = [],
    ): self {
        return new self(
            $path,
            $overridePath,
            $exclude,
        );
    }

    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    private function load(string $path, array $exclude = []): void
    {
        if (is_file($path)) {
            $this->definitions[] = Yaml::parseFile($path);
            return;
        }

        if (!is_dir($path)) {
            throw new \LogicException(sprintf(
                'YAML source "%s" does not exist.',
                $path,
            ));
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $path,
                \FilesystemIterator::SKIP_DOTS,
            ),
        );

        $files = [];

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (
                !$file->isFile()
                || $file->getExtension() !== 'yaml'
                || in_array($file->getFilename(), $exclude, true)
            ) {
                continue;
            }

            $files[] = $file->getPathname();
        }

        sort($files, SORT_STRING);

        foreach ($files as $file) {
            $this->definitions[] = Yaml::parseFile($file);
        }
    }

    public static function file(
        LayoutType $type,
        LayoutVersion $version,
        string $filename,
        ?string $profile = null,
    ): self {
        $path = sprintf(
            '%s/layout/%s/%s/%s',
            dirname(__DIR__, 2),
            $type->value,
            $version->value,
            $filename,
        );

        $profilePath = ProfilePath::for(
            $type,
            $version,
            $profile,
        );

        return self::fromPath(
            path: $path,
            overridePath: $profilePath === null
                ? null
                : sprintf('%s/%s', $profilePath, $filename),
        );
    }
}
