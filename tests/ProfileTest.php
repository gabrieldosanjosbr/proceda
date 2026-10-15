<?php

namespace Proceda\Tests;

use PHPUnit\Framework\TestCase;
use Proceda\Proceda;
use Proceda\Schema\LayoutVersion;
use Proceda\Service\PropertyResolver;
use Proceda\Service\TypeResolver;

class ProfileTest extends TestCase
{
    public function testOverrideNotfisV50(): void
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
            profile: 'custom',
        );

        $root = $layout
            ->getSchemaManager()
            ->findByPath('intercambio');

        self::assertFalse($root->required);

        self::assertNotEmpty($root->fields);

        self::assertNotEmpty($root->children);

        $remetente = $root->fields['remetente'];

        self::assertSame(30, $remetente->length);

        $documento = $root->children['documento'];

        self::assertNotEmpty($documento->fields);

        self::assertNotEmpty($documento->children);

        self::assertFalse($documento->required);

        $valorTotal = $documento->fields['valorTotal'];

        self::assertSame(
            'XYZ',
            $valorTotal->registro->identificacao,
        );

        $pesoBruto = $documento->fields['pesoBruto'];

        self::assertSame(
            '519',
            $pesoBruto->registro->identificacao,
        );

        $registro = $layout->getRegistroResolver();

        self::assertFalse($registro->get('000')->required);

        self::assertFalse($registro->get('XYZ')->required);
    }

    public function testOverrideType(): void
    {
        $typeResolver = TypeResolver::yaml(
            profile: 'custom',
        );

        $freteCondicao = $typeResolver->getTypeCollection(
            'Frete/Condicao',
        );

        self::assertCount(3, $freteCondicao);

        $freteModalidade = $typeResolver->getTypeCollection(
            'Frete/Modalidade',
        );

        self::assertCount(16, $freteModalidade);

        $distribuicao = $freteModalidade->get('00');

        self::assertNotNull($distribuicao);
        self::assertSame(
            'Distribuição',
            $distribuicao->label,
        );

        $myType = $typeResolver->getTypeCollection(
            'Frete/MyType',
        );

        self::assertCount(1, $myType);

        $test = $myType->get('50');

        self::assertNotNull($test);
        self::assertSame('Test', $test->name);
        self::assertSame('Test', $test->label);
    }

    public function testOverrideProperty()
    {
        $resolver = PropertyResolver::yaml(
            profile: 'custom',
        );

        self::assertSame(
            'destinatarios',
            $resolver->collection('destinatario'),
        );

        self::assertSame(
            'embarcadores',
            $resolver->collection('embarcador'),
        );

        self::assertSame(
            'customs',
            $resolver->collection('custom'),
        );
    }

    public function testProfileDoesNotAffectDefaultLayout(): void
    {
        $custom = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
            profile: 'custom',
        );

        $default = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        self::assertSame(
            'XYZ',
            $custom->getSchemaManager()
                ->findByPath('intercambio')
                ->children['documento']
                ->fields['valorTotal']
                ->registro->identificacao,
        );

        self::assertNotSame(
            'XYZ',
            $default->getSchemaManager()
                ->findByPath('intercambio')
                ->children['documento']
                ->fields['valorTotal']
                ->registro->identificacao,
        );
    }

    public function testProfileResolvesCustomTypesAndProperties(): void
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
            profile: 'custom',
        );

        self::assertSame(
            'customs',
            $layout->getPropertyResolver()->collection('custom'),
        );

        $types = $layout->getTypeResolver()
            ->getTypeCollection('Frete/MyType');

        self::assertNotNull($types->get('50'));
    }

    public function testTypeCollectionNotFound(): void
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
            profile: 'custom',
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'TypeCollection "Frete/DoesNotExist" not found.',
        );

        $layout->getTypeResolver()->getTypeCollection(
            'Frete/DoesNotExist',
        );
    }
}