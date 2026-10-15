<?php

namespace Proceda\Tests\Error;

use PHPUnit\Framework\TestCase;
use Proceda\Error\Error;
use Proceda\Error\FieldError;
use Proceda\Error\LayoutError;
use Proceda\Error\LayoutErrorType;
use Proceda\Error\Validation;

final class ValidationTest extends TestCase
{
    public function testFindReturnsNullWhenNoErrorMatches(): void
    {
        $error = new LayoutError(
            type: LayoutErrorType::OCCURS_LIMIT_EXCEEDED,
        );

        $validation = new Validation([$error]);

        $result = $validation->find(
            static fn (Error $error): bool =>
                $error instanceof FieldError,
        );

        self::assertNull($result);
    }
}