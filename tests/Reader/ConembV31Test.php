<?php

namespace Proceda\Tests\Reader;

use PHPUnit\Framework\TestCase;
use Proceda\Documento\Conemb;
use Proceda\Fixtures\EdiPath;
use Proceda\Proceda;
use Proceda\Schema\LayoutType;
use Proceda\Schema\LayoutVersion;
use Proceda\Transportador;

class ConembV31Test extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testRead()
    {
        $layout = Proceda::conemb(LayoutVersion::V31);
        $result = $layout->read(
            EdiPath::for(
                LayoutType::CONEMB,
                LayoutVersion::V31->value,
            )
        );

        $intercambio = $result->intercambio;
        $this->assertSame('PROCEDA REMETENTE LTDA', $intercambio->getRemetente());
        $this->assertCount(2, $intercambio->getDocumentos());

        $documento = $intercambio->getDocumentos()[0];
        $this->assertInstanceOf(Conemb::class, $documento);

        $transportador = $documento->getTransportador();
        $this->assertInstanceOf(Transportador::class, $transportador);

        $this->assertCount(1, $transportador->getConhecimentos());
    }
}
