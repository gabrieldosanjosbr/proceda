<?php

namespace Proceda\Tests\Writer;

use PHPUnit\Framework\Attributes\DataProvider;
use Proceda\Fixtures\Scenario\ConembV31;
use Proceda\Fixtures\Scenario\DoccobV30;
use Proceda\Fixtures\Scenario\DoccobV50;
use Proceda\Fixtures\Scenario\NotfisV31;
use Proceda\Fixtures\Scenario\NotfisV50;
use Proceda\Fixtures\Scenario\OcorenV31;
use Proceda\Fixtures\Scenario\OcorenV50;
use Proceda\Layout;
use Proceda\Proceda;
use Proceda\Schema\LayoutVersion;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

final class RoundTripTest extends WriterTestCase
{
    /**
     * @throws \ReflectionException
     * @throws ExceptionInterface
     */
    #[DataProvider('scenarios')]
    public function testRoundTrip(
        Layout $layout,
        callable $scenario,
    ): void {
        $this->assertRoundTrip(
            $scenario(),
            $layout,
        );
    }

    public static function scenarios(): iterable
    {
        yield 'CONEMB v3.1' => [
            Proceda::conemb(LayoutVersion::V31),
            ConembV31::allFields(...),
        ];

        yield 'DOCCOB v3.0' => [
            Proceda::doccob(LayoutVersion::V30),
            DoccobV30::allFields(...),
        ];

        yield 'DOCCOB v5.0' => [
            Proceda::doccob(LayoutVersion::V50),
            DoccobV50::allFields(...),
        ];

        yield 'NOTFIS v3.1' => [
            Proceda::notfis(LayoutVersion::V31),
            NotfisV31::allFields(...),
        ];

        yield 'NOTFIS v5.0' => [
            Proceda::notfis(LayoutVersion::V50),
            NotfisV50::allFields(...),
        ];

        yield 'NOTFIS v5.0 - required fields' => [
            Proceda::notfis(LayoutVersion::V50),
            NotfisV50::requiredFields(...),
        ];

        yield 'OCOREN v3.1' => [
            Proceda::ocoren(LayoutVersion::V31),
            OcorenV31::allFields(...),
        ];

        yield 'OCOREN v5.0' => [
            Proceda::ocoren(LayoutVersion::V50),
            OcorenV50::allFields(...),
        ];
    }
}