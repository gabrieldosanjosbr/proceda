<?php

namespace Proceda\Tests\Infrastructure;

use PHPUnit\Framework\TestCase;
use Proceda\Infrastructure\ProfilePath;
use Proceda\Schema\LayoutType;
use Proceda\Schema\LayoutVersion;

final class ProfilePathTest extends TestCase
{
    public function testNullProfileReturnsNull(): void
    {
        self::assertNull(ProfilePath::root(null));

        self::assertNull(ProfilePath::for(
            LayoutType::NOTFIS,
            LayoutVersion::V50,
            null,
        ));
    }

    public function testInvalidProfileNameThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid profile "../test".');

        ProfilePath::root('../test');
    }

    public function testNonexistentProfileThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches(
            '/Profile "profile-that-does-not-exist" not found/',
        );

        ProfilePath::root('profile-that-does-not-exist');
    }

    public function testResolvesProfileUsingConfiguredDirectory(): void
    {
        $directory = sys_get_temp_dir()
            . '/proceda-profiles-' . bin2hex(random_bytes(8));

        $profilePath = $directory . '/empresa-teste';

        mkdir($profilePath, 0777, true);

        $previous = getenv('PROCEDA_PROFILES_DIR');

        try {
            putenv('PROCEDA_PROFILES_DIR=' . $directory);

            self::assertSame(
                $profilePath,
                ProfilePath::root('empresa-teste'),
            );

            self::assertSame(
                $profilePath . '/notfis/v50',
                ProfilePath::for(
                    LayoutType::NOTFIS,
                    LayoutVersion::V50,
                    'empresa-teste',
                ),
            );
        } finally {
            if ($previous === false) {
                putenv('PROCEDA_PROFILES_DIR');
            } else {
                putenv('PROCEDA_PROFILES_DIR=' . $previous);
            }

            rmdir($profilePath);
            rmdir($directory);
        }
    }

    public function testUsesDefaultProfilesDirectory(): void
    {
        $previous = getenv('PROCEDA_PROFILES_DIR');

        try {
            putenv('PROCEDA_PROFILES_DIR');

            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessageMatches(
                '/Profile "profile-that-does-not-exist" not found/',
            );

            ProfilePath::root('profile-that-does-not-exist');
        } finally {
            if ($previous === false) {
                putenv('PROCEDA_PROFILES_DIR');
            } else {
                putenv('PROCEDA_PROFILES_DIR=' . $previous);
            }
        }
    }
}