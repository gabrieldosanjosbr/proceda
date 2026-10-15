<?php

namespace Proceda\Tests\Writer;

use Proceda\Documento\Notfis;
use Proceda\Fixtures\Scenario\NotfisV50;
use Proceda\Proceda;
use Proceda\Schema\LayoutVersion;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

final class NotfisV50FreteFallbackTest extends WriterTestCase
{
    /**
     * @throws \ReflectionException
     * @throws ExceptionInterface
     */
    public function testFreteFallback()
    {
        $expectedFrete = 500.60;
        $expectedIcms = 200.50;
        $layout = Proceda::notfis(LayoutVersion::V50);

        $response = $this->write(
            NotfisV50::freteFallback(
                $expectedFrete,
                $expectedIcms
            ),
            $layout,
        );

        $lines = preg_split(
            "/\r\n|\n|\r/",
            trim($response->content),
        );

        $registros = array_map(
            static fn(string $line): string => substr(
                $line,
                0,
                3,
            ),
            $lines,
        );

        self::assertContains(
            '506',
            $registros,
        );

        self::assertNotContains(
            '507',
            $registros,
        );

        $result = $layout->readFromString(
            $response->content,
        );

        /** @var Notfis $documento */
        $documento = $result->intercambio->getDocumentos()[0];

        $actualFrete = $documento->getEmbarcadoras()[0]
            ->getDestinatarios()[0]
            ->getNotasFiscais()[0]
            ->getFrete();

        self::assertSame(
            $expectedFrete,
            $actualFrete->getValor(),
        );

        self::assertSame(
            $expectedIcms,
            $actualFrete->getImposto()
                ->getTributos()[0]
                ->getValor(),
        );
    }
}
