<?php

namespace Proceda\Tests\Reader;

use PHPUnit\Framework\TestCase;
use Proceda\Documento\Notfis;
use Proceda\Empresa;
use Proceda\Fixtures\EdiPath;
use Proceda\Fixtures\Factory\TypeFactory;
use Proceda\Frete;
use Proceda\NotaFiscal;
use Proceda\NotaFiscal\Carga;
use Proceda\Proceda;
use Proceda\Schema\LayoutType;
use Proceda\Schema\LayoutVersion;

class NotfisV31Test extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testRead()
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V31,
        );

        $result = $layout->read(
            EdiPath::for(
                LayoutType::NOTFIS,
                LayoutVersion::V31->value,
            )
        );

        $intercambio = $result->intercambio;
        $this->assertSame('EMPRESA TESTE LOGISTICA LTDA', $intercambio->getRemetente());
        $this->assertCount(2, $intercambio->getDocumentos());

        $documento = $intercambio->getDocumentos()[0];
        $this->assertInstanceOf(Notfis::class, $documento);
        $this->assertSame('NOT11112222S', $documento->getIdentificacao());
        $this->assertCount(1, $documento->getEmbarcadoras());

        $embarcadora = $documento->getEmbarcadoras()[0];
        $this->assertCount(1, $embarcadora->getDestinatarios());

        $destinatario = $embarcadora->getDestinatarios()[0];
        $this->assertCount(2, $destinatario->getNotasFiscais());
        $this->assertSame('FULANO DA SILVA', $destinatario->getNome());

        $notaFiscal = $destinatario->getNotasFiscais()[1];
        $this->assertInstanceOf(NotaFiscal::class, $notaFiscal);
        $this->assertCount(9, $notaFiscal->getItems());

        $frete = $notaFiscal->getFrete();
        $this->assertInstanceOf(Frete::class, $frete);

        $empresa = $notaFiscal->getTransportador();
        $this->assertInstanceOf(Empresa::class, $empresa);

        $this->assertSame(
            'TRANSPORTADORA EXEMPLO LTDA',
            $empresa->getNome(),
        );

        $item = $notaFiscal->getItems()[1];
        $this->assertSame(5.0, $item->getQuantidade());

        $this->assertSame(32034.89, $notaFiscal->getValor());
        $this->assertSame(0.54, $notaFiscal->getPesoBruto());

        $carga = $notaFiscal->getCarga();
        $this->assertInstanceOf(Carga::class, $carga);

        $this->assertEquals(
            TypeFactory::get('NotaFiscal/Carga/Tipo/Seca'),
            $carga->getTipo(),
        );

        $this->assertEquals(
            TypeFactory::get(
                'NotaFiscal/Carga/Modalidade/Fechada',
            ),
            $carga->getModalidade(),
        );

        $this->assertSame(
            'ELETRONICOS',
            $carga->getNatureza(),
        );

        $this->assertSame(
            'AMARRADOS',
            $carga->getAcondicionamento(),
        );

        $this->assertSame(
            'DOCA',
            $carga->getLocalDesembarque(),
        );

        $this->assertSame(
            false,
            $carga->getPlanoCargaRapida(),
        );

        $this->assertEquals(
            null,
            $carga->getInicioEntrega(),
        );

        $this->assertEquals(
            null,
            $carga->getFimEntrega(),
        );

        $this->assertEquals(
            TypeFactory::get(
                'NotaFiscal/Carga/CondicaoPeriodoEntrega/SemDataEntregaDefinida',
            ),
            $carga->getCondicaoPeriodoEntrega(),
        );
    }
}
