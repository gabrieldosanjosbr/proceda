<?php

namespace Proceda\Tests\Reader;

use PHPUnit\Framework\TestCase;
use Proceda\Documento\Ocoren;
use Proceda\Fixtures\EdiPath;
use Proceda\Proceda;
use Proceda\Schema\LayoutType;
use Proceda\Schema\LayoutVersion;
use Proceda\Transportador;

class OcorenV31Test extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testRead()
    {
        $layout = Proceda::ocoren(LayoutVersion::V31);
        $result = $layout->read(
            EdiPath::for(
                LayoutType::OCOREN,
                LayoutVersion::V31->value,
            )
        );

        $intercambio = $result->intercambio;
        $this->assertSame('NOME DA CAIXA POSTAL DO REMETENTE', $intercambio->getRemetente());
        $this->assertCount(1, $intercambio->getDocumentos());

        $documento = $intercambio->getDocumentos()[0];
        $this->assertInstanceOf(Ocoren::class, $documento);
        $this->assertSame('OCORR030811491', $documento->getIdentificacao());

        $transportador = $documento->getTransportador();
        $this->assertInstanceOf(Transportador::class, $transportador);
        $this->assertCount(2, $transportador->getOcorrencias());
    }
}
