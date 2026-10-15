<?php

namespace Proceda\Tests\Writer;

use Proceda\Error\FieldError;
use Proceda\Error\FieldErrorType;
use Proceda\Error\LayoutError;
use Proceda\Error\LayoutErrorType;
use Proceda\Error\Error;
use Proceda\Fixtures\Builder\BuilderContext;
use Proceda\Fixtures\Builder\DestinatarioBuilder;
use Proceda\Fixtures\Builder\EmbarcadorBuilder;
use Proceda\Fixtures\Builder\IntercambioBuilder;
use Proceda\Fixtures\Builder\NotaFiscalBuilder;
use Proceda\Fixtures\Builder\NotfisBuilder;
use Proceda\Fixtures\Builder\OcorenBuilder;
use Proceda\Fixtures\Builder\OcorrenciaBuilder;
use Proceda\Fixtures\Builder\TransportadorBuilder;
use Proceda\Fixtures\Factory\TypeFactory;
use Proceda\Proceda;
use Proceda\Schema\LayoutVersion;
use Random\RandomException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class ValidationTest extends WriterTestCase
{
    /**
     * @throws ExceptionInterface
     * @throws \ReflectionException
     * @throws RandomException
     */
    public function testOccursLimitExceeded()
    {
        $layout = Proceda::ocoren(
            layoutVersion: LayoutVersion::V50,
            profile: 'custom'
        );

        BuilderContext::initialize(
            $layout->getSchemaManager(),
        );

        $ocorrencia = OcorrenciaBuilder::lazy();

        $transportador = TransportadorBuilder::lazy(
            fn(TransportadorBuilder $builder) =>
            $builder->withOcorrencia($ocorrencia)
        );

        $ocoren = OcorenBuilder::lazy(
            fn(OcorenBuilder $builder) =>
            $builder->withTransportador($transportador),
        );

        $intercambio = IntercambioBuilder::new()
            ->withDocumento($ocoren)
            ->withDocumento($ocoren)
            ->withDocumento($ocoren)
            ->build();

        $result = $this->write(
            $intercambio,
            $layout
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
     * @throws \ReflectionException
     * @throws ExceptionInterface
     */
    public function testInlineLimitExceeded()
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V31,
        );

        BuilderContext::initialize(
            $layout->getSchemaManager(),
        );

        $notaFiscal = NotaFiscalBuilder::lazy(
            fn(NotaFiscalBuilder $notaFiscal) =>
            $notaFiscal
                ->withEntregaCasada(NotaFiscalBuilder::lazy())
                ->withEntregaCasada(NotaFiscalBuilder::lazy())
                ->withEntregaCasada(NotaFiscalBuilder::lazy())
                ->withEntregaCasada(NotaFiscalBuilder::lazy())
                ->withEntregaCasada(NotaFiscalBuilder::lazy())
                ->withEntregaCasada(NotaFiscalBuilder::lazy()),
        );

        $destinatario = DestinatarioBuilder::lazy(
            fn(DestinatarioBuilder $destinatario) =>
            $destinatario->withNotaFiscal($notaFiscal),
        );

        $notfis = NotfisBuilder::lazy(
            fn(NotfisBuilder $notfis) =>
            $notfis->withEmbarcador(
                EmbarcadorBuilder::lazy(
                    fn(EmbarcadorBuilder $embarcador) =>
                    $embarcador->withDestinatario($destinatario),
                ),
            ),
        );

        $intercambio = IntercambioBuilder::new()
            ->withDocumento($notfis)
            ->build();

        $response = $this->write(
            $intercambio,
            $layout,
        );

        /** @var LayoutError|null $error */
        $error = $response->validation->find(
            static fn(Error $error): bool =>
                $error instanceof LayoutError
                && $error->type === LayoutErrorType::INLINE_LIMIT_EXCEEDED
                && $error->schema->path === 'nota-fiscal/entrega-casada'
                && $error->line?->number !== null,
        );

        $this->assertNotNull($error);

        $this->assertSame(
            'A coleção "nota-fiscal/entrega-casada" excedeu o limite máximo de 5 itens na mesma linha.',
            $error->message(),
        );
    }

    /**
     * @throws \ReflectionException
     * @throws ExceptionInterface
     * @throws \Exception
     */
    public function testCodigoTypeNotAllowed()
    {
        $layout = Proceda::ocoren(
            layoutVersion: LayoutVersion::V31,
        );

        BuilderContext::initialize(
            $layout->getSchemaManager(),
        );

        $ocorrencia = OcorrenciaBuilder::new()
            ->codigo(
                TypeFactory::get(
                    'Ocorrencia/Codigo/CorteCargaPista',
                ),
            )
            ->build();

        $transportador = TransportadorBuilder::lazy(
            fn(TransportadorBuilder $builder) =>
            $builder->addOcorrencia($ocorrencia),
        );

        $ocoren = OcorenBuilder::lazy(
            fn(OcorenBuilder $builder) =>
            $builder->withTransportador($transportador),
        );

        $intercambio = IntercambioBuilder::new()
            ->withDocumento($ocoren)
            ->build();

        $response = $this->write(
            $intercambio,
            $layout,
        );

        /** @var FieldError|null $typeNotAllowed */
        $typeNotAllowed = $response->validation->find(
            static fn(Error $error): bool =>
                $error instanceof FieldError
                && $error->type === FieldErrorType::TYPE_NOT_ALLOWED,
        );

        $this->assertNotNull($typeNotAllowed);

        $this->assertSame(
            'Valor "39" não permitido para o campo "codigo". (coleçao: "Ocorrencia/Codigo")',
            $typeNotAllowed->message(),
        );
    }

    /**
     * @throws \ReflectionException
     * @throws ExceptionInterface
     */
    public function testInvalidTimeIntercambio()
    {
        $layout = Proceda::conemb(
            layoutVersion: LayoutVersion::V31,
        );

        BuilderContext::initialize(
            $layout->getSchemaManager(),
        );

        $intercambio = IntercambioBuilder::new()
            ->hora('597800')
            ->build();

        $response = $this->write(
            $intercambio,
            $layout
        );

        /** @var FieldError|null $error */
        $error = $response->validation->find(
            static fn(Error $error): bool =>
                $error instanceof FieldError
                && $error->type === FieldErrorType::VALUE_EXCEEDS_MAXIMUM_LENGTH,
        );

        $this->assertNotNull($error);

        $this->assertSame(
            'Valor "597800" do campo "hora" excede o tamanho máximo permitido de 4 caracteres.',
            $error->message(),
        );
    }
}
