<?php

namespace Proceda\Tests\Reader;

use PHPUnit\Framework\TestCase;
use Proceda\Fixtures\EdiPath;
use Proceda\Proceda;
use Proceda\Schema\LayoutType;
use Proceda\Schema\LayoutVersion;

class DoccobV50Test extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testRead()
    {
        $layout = Proceda::doccob(LayoutVersion::V50);
        $result = $layout->read(
            EdiPath::for(
                LayoutType::DOCCOB,
                LayoutVersion::V50->value,
            )
        );

        $intercambio = $result->intercambio;
        $this->assertSame('PROCEDA REMETENTE LTDA', $intercambio->getRemetente());
        $this->assertCount(1, $intercambio->getDocumentos());
    }

}
