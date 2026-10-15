<?php

namespace Proceda\Infrastructure;

use Proceda\Schema\LayoutType;
use Proceda\Schema\LayoutVersion;

final class ProfilePath
{
    public static function for(
        LayoutType $type,
        LayoutVersion $version,
        ?string $profile,
    ): ?string {
        $root = self::root($profile);

        if ($root === null) {
            return null;
        }

        return sprintf(
            '%s/%s/%s',
            $root,
            $type->value,
            $version->value,
        );
    }

    public static function root(?string $profile): ?string
    {
        if ($profile === null) {
            return null;
        }

        if (!preg_match('/\A[a-zA-Z0-9_-]+\z/', $profile)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid profile "%s".', $profile),
            );
        }

        $basePath = getenv('PROCEDA_PROFILES_DIR')
            ?: dirname(__DIR__, 2) . '/profiles';

        $path = sprintf(
            '%s/%s',
            rtrim($basePath, '/'),
            $profile,
        );

        if (!is_dir($path)) {
            throw new \RuntimeException(sprintf(
                'Profile "%s" not found at "%s".',
                $profile,
                $path,
            ));
        }

        return $path;
    }
}