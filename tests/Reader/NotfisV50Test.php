<?php

namespace Proceda\Tests\Reader;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Proceda\Builder\EventoBuilder;
use Proceda\Documento\Notfis;
use Proceda\Embarcador;
use Proceda\Embarque;
use Proceda\Fixtures\EdiPath;
use Proceda\Fixtures\Factory\TypeFactory;
use Proceda\Frete;
use Proceda\Intercambio;
use Proceda\Nfe;
use Proceda\NotaFiscal\Carga;
use Proceda\NotaFiscal\Item;
use Proceda\NotaFiscal\Volume;
use Proceda\Proceda;
use Proceda\Schema\LayoutType;
use Proceda\Schema\LayoutVersion;

class NotfisV50Test extends TestCase
{
    /**
     * @throws \Exception
     */
    #[DataProvider('fixtures')]
    public function testRead(
        string $txtFile,
        float $freteEsperado,
    ): void {
        $layout = Proceda::notfis(LayoutVersion::V50);
        $result = $layout->read(
            EdiPath::for(
                LayoutType::NOTFIS,
                $txtFile,
            )
        );

        $intercambio = $result->intercambio;
        $this->assertInstanceOf(Intercambio::class, $intercambio);
        $this->assertSame('REMETENTE TESTE LTDA', $intercambio->getRemetente());
        $this->assertCount(1, $intercambio->getDocumentos());

        $documento = $intercambio->getDocumentos()[0];
        $this->assertInstanceOf(Notfis::class, $documento);
        $this->assertSame('DOC20260000001', $documento->getIdentificacao());
        $this->assertCount(1, $documento->getEmbarcadoras());

        $embarcador = $documento->getEmbarcadoras()[0];
        $this->assertInstanceOf(Embarcador::class, $embarcador);
        $this->assertCount(1, $embarcador->getDestinatarios());
        $this->assertSame('CONTATO LOGISTICA', $embarcador->getContatoEmergencia());

        $empresa = $embarcador->getEmpresa();
        $this->assertSame('EMPRESA EMBARCADORA TESTE LTDA', $empresa->getNome());
        $this->assertSame('SP01', $empresa->getAreaFrete());

        $destinatario = $embarcador->getDestinatarios()[0];
        $this->assertSame('EMPRESA DESTINATARIO LTDA', $destinatario->getNome());
        $this->assertSame('01001000', $destinatario->getCep());
        $this->assertCount(1, $destinatario->getNotasFiscais());

        $notaFiscal = $destinatario->getNotasFiscais()[0];
        $this->assertSame('09062026', $notaFiscal->getDataEmissao());
        $this->assertSame('TABPADRAO', $notaFiscal->getFrete()->getTabela());
        $this->assertSame('PEDIDO-1234567890', $notaFiscal->getIdPedidoCliente());
        $this->assertCount(1, $notaFiscal->getItems());
        $this->assertCount(1, $notaFiscal->getEmbarques());

        $nfe = $notaFiscal->getNfe();
        $this->assertInstanceOf(Nfe::class, $nfe);

        $this->assertSame(
            '1234567890123456789012345678901234567890',
            $nfe->getChaveAcesso(),
        );

        $this->assertEquals(
            TypeFactory::get(
                'NotaFiscal/DocumentoFiscal/Saida',
            ),
            $notaFiscal->getDocumentoFiscal(),
        );

        $frete = $notaFiscal->getFrete();
        $this->assertInstanceOf(Frete::class, $frete);

        $this->assertSame($freteEsperado, $frete->getValor());

        $this->assertEquals(
            TypeFactory::get('Frete/Condicao/CIF'),
            $frete->getCondicao(),
        );

        $this->assertEquals(
            TypeFactory::get('NotaFiscal/MeioTransporte/Rodoviario'),
            $notaFiscal->getMeioTransporte(),
        );

        $carga = $notaFiscal->getCarga();
        $this->assertInstanceOf(Carga::class, $carga);

        $this->assertSame(
            '29062026',
            $carga->getDataEmbarque(),
        );

        $this->assertEquals(
            TypeFactory::get('NotaFiscal/Carga/Tipo/Fria'),
            $carga->getTipo(),
        );

        $this->assertEquals(
            TypeFactory::get(
                'NotaFiscal/Carga/Modalidade/Fechada',
            ),
            $carga->getModalidade(),
        );

        $this->assertSame(
            'PRODUTOS GERAL',
            $carga->getNatureza(),
        );

        $this->assertSame(
            'CAIXAS',
            $carga->getAcondicionamento(),
        );

        $this->assertSame(
            true,
            $carga->getPlanoCargaRapida(),
        );

        $inicioEntrega = (new EventoBuilder())
            ->tipo(
                TypeFactory::get(
                    'Evento/Tipo/InicioEntrega',
                ),
            )
            ->data('12072026')
            ->hora('1800')
            ->build();

        $this->assertEquals(
            $inicioEntrega,
            $carga->getInicioEntrega(),
        );

        $fimEntrega = (new EventoBuilder())
            ->tipo(
                TypeFactory::get(
                    'Evento/Tipo/FimEntrega',
                ),
            )
            ->data('12072026')
            ->hora('1900')
            ->build();

        $this->assertEquals(
            $fimEntrega,
            $carga->getFimEntrega(),
        );

        $this->assertEquals(
            TypeFactory::get(
                'NotaFiscal/Carga/CondicaoPeriodoEntrega/SemDataEntregaDefinida',
            ),
            $carga->getCondicaoPeriodoEntrega(),
        );

        $this->assertSame(1, $documento->getQtdDocumentos());
        $this->assertSame(100.5, $documento->getPesoBruto());
        $this->assertSame(10.5, $documento->getTotalVolumes());

        $item = $notaFiscal->getItems()[0];
        $this->assertInstanceOf(Item::class, $item);
        $this->assertSame(5.0, $item->getQuantidade());
        $this->assertSame('NOTEBOOK MODELO TESTE 15 POL', $item->getDescricao());
        $this->assertSame('ITEM000000000000001', $item->getCodigo());
        $this->assertSame('10012345678901234567', $item->getLote());
        $this->assertSame('PEDIDO-1234567890', $item->getIdPedidoCliente());

        $volume = $item->getVolume();
        $this->assertInstanceOf(Volume::class, $volume);
        $this->assertSame('MARCA TESTE', $volume->getMarca());
        $this->assertSame('VOL0001', $volume->getNumeracao());
        $this->assertSame('LACRE0001', $volume->getLacre());

        $embarque = $notaFiscal->getEmbarques()[0];
        $this->assertInstanceOf(Embarque::class, $embarque);
        $this->assertSame('ID-EMBARQUE-12345', $embarque->getIdentificacao());
    }

    public static function fixtures(): iterable
    {
        yield 'principal' => [
            'v50',
            1000.00,
        ];

        yield 'fallback' => [
            'v50-fallback',
            1000.50,
        ];
    }
}
