<?php

namespace Proceda\Tests\Reader;

use PHPUnit\Framework\TestCase;
use Proceda\Error\FieldError;
use Proceda\Error\FieldErrorType;
use Proceda\Error\Error;
use Proceda\Error\LayoutError;
use Proceda\Error\LayoutErrorType;
use Proceda\Fixtures\EdiPath;
use Proceda\Line;
use Proceda\Proceda;
use Proceda\Schema\LayoutType;
use Proceda\Schema\LayoutVersion;
use Proceda\Schema\Registro;
use Random\RandomException;

class ValidationTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testInvalidTime(): void
    {
        $layout = Proceda::conemb(
            layoutVersion: LayoutVersion::V31,
        );

        $hora = '5978';

        $intercambio = "
            000PROCEDA REMETENTE LTDA             CLIENTE CONEMB V31 TESTE           290626{$hora}CONEMBV31001
        ";

        $result = $layout->readFromString($intercambio);

        $error = $result->validation->find(
            static fn(Error $error): bool =>
                $error instanceof FieldError
                && $error->type === FieldErrorType::INVALID_TIME,
        );

        $this->assertInstanceOf(FieldError::class, $error);

        $this->assertSame(
            "Horário \"{$hora}\" inválido para o campo \"hora\". Informe um valor entre 0000 e 2359, com hora e minutos válidos.",
            $error->message(),
        );
    }

    /**
     * @throws \ReflectionException
     */
    public function testMissingRequiredRemetente(): void
    {
        $layout = Proceda::conemb(
            layoutVersion: LayoutVersion::V31,
        );

        $intercambio = '
            0000000000000000000000000             CLIENTE CONEMB V31 TESTE           2906262300CONEMBV31001
        ';

        $result = $layout->readFromString($intercambio);

        $error = $result->validation->find(
            static fn(Error $error): bool =>
                $error instanceof FieldError
                && $error->type === FieldErrorType::MISSING_REQUIRED_VALUE
                && $error->field->property === 'remetente',
        );

        $this->assertInstanceOf(FieldError::class, $error);

        $this->assertSame(
            'Campo "remetente" obrigatório não preenchido.',
            $error->message(),
        );
    }

    /**
     * @throws \ReflectionException
     */
    public function testMissingRootRegistro(): void
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        $result = $layout->readFromString(
            file_get_contents(
                EdiPath::for(
                    LayoutType::NOTFIS,
                    'v50-missing-root',
                ),
            ),
        );

        $error = $result->validation->find(
            static fn (Error $error): bool =>
                $error instanceof LayoutError
                && $error->type === LayoutErrorType::MISSING_REQUIRED_REGISTRO,
        );

        $this->assertInstanceOf(LayoutError::class, $error);

        $this->assertSame(
            'Registro obrigatório "000" não encontrado.',
            $error->message(),
        );

        $this->assertSame('000', $error->registro->identificacao);
    }

    /**
     * @throws \ReflectionException
     */
    public function testMissingRequiredParentRegistro(): void
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        $result = $layout->readFromString(
            file_get_contents(
                EdiPath::for(
                    LayoutType::NOTFIS,
                    'v50-missing-505',
                ),
            ),
        );

        $error = $result->validation->find(
            static fn (Error $error): bool =>
                $error instanceof LayoutError
                && $error->type
                === LayoutErrorType::MISSING_REQUIRED_REGISTRO
                && $error->registro->identificacao === '505',
        );

        $this->assertInstanceOf(LayoutError::class, $error);

        $this->assertSame('505', $error->registro->identificacao);
        $this->assertSame('506', $error->line?->registro->identificacao);
    }

    /**
     * @throws \ReflectionException
     */
    public function testMissingRequiredExtension(): void
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        $result = $layout->readFromString(
            file_get_contents(
                EdiPath::for(
                    LayoutType::NOTFIS,
                    'v50-missing-506',
                ),
            ),
        );

        $error = $result->validation->find(
            static fn (Error $error): bool =>
                $error instanceof LayoutError
                && $error->type
                === LayoutErrorType::MISSING_REQUIRED_REGISTRO
                && $error->registro->identificacao === '506',
        );

        $this->assertInstanceOf(LayoutError::class, $error);

        $this->assertSame('506', $error->registro->identificacao);
        $this->assertSame('505', $error->line?->registro->identificacao);
    }

    /**
     * @throws \ReflectionException
     */
    public function testMissingLastRequiredRegistro(): void
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        $result = $layout->readFromString(
            file_get_contents(
                EdiPath::for(
                    LayoutType::NOTFIS,
                    'v50-missing-519',
                ),
            ),
        );

        $error = $result->validation->find(
            static fn (Error $error): bool =>
                $error instanceof LayoutError
                && $error->type
                === LayoutErrorType::MISSING_REQUIRED_REGISTRO
                && $error->registro->identificacao === '519',
        );

        $this->assertInstanceOf(LayoutError::class, $error);

        $this->assertSame('519', $error->registro->identificacao);
        $this->assertSame('500', $error->line?->registro->identificacao);
    }

    /**
     * @throws \ReflectionException
     */
    public function testMissingRequiredRegistro(): void
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        $result = $layout->readFromString(
            file_get_contents(
                EdiPath::for(
                    LayoutType::NOTFIS,
                    'v50-missing-503',
                ),
            ),
        );

        $error = $result->validation->find(
            static fn (Error $error): bool =>
                $error instanceof LayoutError
                && $error->type
                === LayoutErrorType::MISSING_REQUIRED_REGISTRO
                && $error->registro->identificacao === '503',
        );

        $this->assertInstanceOf(LayoutError::class, $error);
        $this->assertSame(
            '503',
            $error->registro->identificacao,
        );
    }

    /**
     * @throws \ReflectionException
     */
    public function testOccursLimitExceeded()
    {
        $layout = Proceda::ocoren(
            layoutVersion: LayoutVersion::V50,
            profile: 'custom'
        );

        $result = $layout->readFromString(
            file_get_contents(
                EdiPath::for(
                    LayoutType::OCOREN,
                    'v50-documento-occurs-3',
                ),
            ),
        );

        $error = $result->validation->find(
            static fn (Error $error): bool =>
                $error instanceof LayoutError
                && $error->type
                === LayoutErrorType::OCCURS_LIMIT_EXCEEDED
                && $error->registro->identificacao === '540',
        );

        $this->assertInstanceOf(LayoutError::class, $error);
        $this->assertSame(
            '540',
            $error->registro->identificacao,
        );

        $this->assertSame(
            2,
            $error->parameters['maximum'],
        );

        $this->assertSame(
            3,
            $error->parameters['actual'],
        );
    }

    /**
     * @throws RandomException
     */
    public function testWithLineOnOccursLimitExceeded(): void
    {
        $error = new LayoutError(
            type: LayoutErrorType::OCCURS_LIMIT_EXCEEDED
        );

        $line = new Line(
            content:'999',
            registro: new Registro(
                identificacao: '999',
                length: 200
            )
        );

        $updated = $error->withLine($line);

        self::assertSame($line, $updated->line);
    }
}
