<?php

namespace Proceda\Tests\Reader;

use PHPUnit\Framework\TestCase;
use Proceda\Fixtures\EdiPath;
use Proceda\Intercambio;
use Proceda\Proceda;
use Proceda\Schema\LayoutType;
use Proceda\Schema\LayoutVersion;

class DoccobV30Test extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testRead()
    {
        $layout = Proceda::doccob(LayoutVersion::V30);
        $result = $layout->read(
            EdiPath::for(
                LayoutType::DOCCOB,
                LayoutVersion::V30->value,
            )
        );

        $intercambio = $result->intercambio;
        $this->assertInstanceOf(Intercambio::class, $intercambio);
        $this->assertSame('PROCEDA REMETENTE LTDA', $intercambio->getRemetente());
        $this->assertCount(2, $intercambio->getDocumentos());
    }
}
