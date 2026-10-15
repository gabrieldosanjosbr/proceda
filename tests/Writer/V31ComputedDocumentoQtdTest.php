<?php

namespace Proceda\Tests\Writer;

use Proceda\Fixtures\Builder\BuilderContext;
use Proceda\Fixtures\Builder\DestinatarioBuilder;
use Proceda\Fixtures\Builder\EmbarcadorBuilder;
use Proceda\Fixtures\Builder\EmpresaBuilder;
use Proceda\Fixtures\Builder\IntercambioBuilder;
use Proceda\Fixtures\Builder\NotaFiscalBuilder;
use Proceda\Fixtures\Builder\NotfisBuilder;
use Proceda\Fixtures\Builder\OcorenBuilder;
use Proceda\Fixtures\Builder\OcorrenciaBuilder;
use Proceda\Fixtures\Builder\TransportadorBuilder;
use Proceda\Proceda;
use Proceda\Schema\LayoutVersion;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class V31ComputedDocumentoQtdTest extends WriterTestCase
{
    /**
     * @throws \ReflectionException
     * @throws ExceptionInterface
     */
    public function testNotfisV31ComputedDocumentoQtd()
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V31,
        );

        BuilderContext::initialize(
            $layout->getSchemaManager(),
        );

        $destinatario = DestinatarioBuilder::lazy(
            fn(DestinatarioBuilder $destinatario) =>
            $destinatario->withNotaFiscal(NotaFiscalBuilder::lazy()),
        );

        $embarcador = EmbarcadorBuilder::lazy(
            fn(EmbarcadorBuilder $embarcador) =>
            $embarcador
                ->withEmpresa(EmpresaBuilder::lazy())
                ->withDestinatario($destinatario),
        );

        $notfis = NotfisBuilder::lazy(
            fn(NotfisBuilder $notfis) =>
            $notfis
                ->qtdDocumentos(null)
                ->withEmbarcador($embarcador)
                ->withEmbarcador($embarcador),
        );

        $intercambio = IntercambioBuilder::new()
            ->withDocumento($notfis)
            ->build();

        $response = $this->write(
            $intercambio,
            $layout,
        );

        $this->assertTrue($response->successful());

        $result = $layout->readFromString(
            $response->content,
        );

        $this->assertSame(
            2,
            $result->intercambio
                ->getDocumentos()[0]
                ->getQtdDocumentos(),
        );
    }

    /**
     * @throws \ReflectionException
     * @throws ExceptionInterface
     */
    public function testOcorenV31ComputedDocumentoQtd()
    {
        $layout = Proceda::ocoren(
            layoutVersion: LayoutVersion::V31,
        );

        BuilderContext::initialize(
            $layout->getSchemaManager(),
        );

        $transportador = TransportadorBuilder::lazy(
            fn(TransportadorBuilder $builder) =>
            $builder
                ->withEmpresa(EmpresaBuilder::lazy())
                ->withOcorrencia(OcorrenciaBuilder::lazy())
                ->withOcorrencia(OcorrenciaBuilder::lazy())
                ->withOcorrencia(OcorrenciaBuilder::lazy()),
        );

        $ocoren = OcorenBuilder::lazy(
            fn(OcorenBuilder $builder) =>
            $builder
                ->qtdDocumentos(null)
                ->withTransportador($transportador),
        );

        $intercambio = IntercambioBuilder::new()
            ->withDocumento($ocoren)
            ->build();

        $response = $this->write(
            $intercambio,
            $layout,
        );

        $this->assertTrue($response->successful());

        $result = $layout->readFromString(
            $response->content,
        );

        $this->assertSame(
            3,
            $result->intercambio
                ->getDocumentos()[0]
                ->getQtdDocumentos(),
        );
    }
}
