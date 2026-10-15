<?php

namespace Proceda\Tests\Collection;

use PHPUnit\Framework\TestCase;
use Proceda\Collection\TypeCollection;
use Proceda\Type;

final class TypeCollectionTest extends TestCase
{
    public function testHas(): void
    {
        $collection = new TypeCollection(
            name: 'status',
            types: [
                new Type(
                    name: 'Pendente',
                    value: '01',
                    label: 'Pendente'
                ),
                new Type(
                    name: 'Concluído',
                    value: '02',
                    label: 'Concluído'
                ),
            ],
        );

        self::assertTrue($collection->has('01'));
        self::assertTrue($collection->has('02'));
        self::assertFalse($collection->has('03'));

        // Comparação estrita: int 1 !== string '01'
        self::assertFalse($collection->has(1));
    }

    public function testGetUnknownValueReturnsNull(): void
    {
        $collection = new TypeCollection(
            name: 'status',
            types: [
                new Type(
                    name: 'Pendente',
                    value: '01',
                    label: 'Pendente'
                ),
            ],
        );

        self::assertNull($collection->get('99'));
    }

    public function testGetByNameUnknownReturnsNull(): void
    {
        $collection = new TypeCollection(
            name: 'status',
            types: [
                new Type(
                    name: 'Pendente',
                    value: '01',
                    label: 'Pendente'
                ),
                new Type(
                    name: 'Concluído',
                    value: '02',
                    label: 'Concluído'
                ),
            ],
        );

        self::assertNull(
            $collection->getByName('Inexistente'),
        );
    }
}