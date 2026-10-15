<?php

namespace Proceda;

final readonly class Charset
{
    private const string UTF8 = 'UTF-8';
    private const string ISO = 'ISO-8859-1';

    public function __construct(
        private int $tabReplacementSize = 1,
    ) {
        if ($this->tabReplacementSize < 1) {
            throw new \InvalidArgumentException(
                'Tab replacement size must be greater than zero.',
            );
        }
    }

    public function toIso(string $text): string
    {
        $text = str_replace(
            "\t",
            str_repeat(' ', $this->tabReplacementSize),
            $text,
        );

        if (!$this->isMultibyte($text)) {
            return $text;
        }

        return mb_convert_encoding(
            $text,
            self::ISO,
            self::UTF8,
        );
    }

    public function toUtf8(string $text): string
    {
        return mb_convert_encoding(
            $text,
            self::UTF8,
            self::ISO,
        );
    }

    private function isMultibyte(string $string): bool
    {
        return strlen($string) !== mb_strlen(
            $string,
            self::UTF8,
        );
    }
}
