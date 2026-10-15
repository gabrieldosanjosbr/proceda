<?php

namespace Proceda\Infrastructure\Serializer;

use Proceda\Schema\Manager;
use Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorFromClassMetadata;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class Factory
{
    public static function create(
        ?Manager $schemaManager = null,
    ): Serializer {
        $classMetadataFactory = new ClassMetadataFactory(
            new AttributeLoader(),
        );

        $propertyInfo = new PropertyInfoExtractor(
            typeExtractors: [
                new PhpStanExtractor(),
                new ReflectionExtractor(),
            ],
        );

        $normalizers = [
            new ResponseNormalizer(),
            new ValidationNormalizer(),
            new LayoutNormalizer(),
            new BackedEnumNormalizer(),
            new ArrayDenormalizer(),
            new ObjectNormalizer(
                classMetadataFactory: $classMetadataFactory,
                propertyTypeExtractor: $propertyInfo,
                classDiscriminatorResolver:
                new ClassDiscriminatorFromClassMetadata(
                    $classMetadataFactory,
                ),
            )
        ];

        if ($schemaManager !== null) {
            $normalizers = [
                new SchemaNormalizer(
                    $schemaManager
                ),
                new FieldNormalizer(
                    $schemaManager,
                ),
                new LineNormalizer(
                    $schemaManager->getRegistroResolver(),
                ),
                ...$normalizers
            ];
        }

        return new Serializer(
            normalizers:$normalizers,
            encoders: [
                new JsonEncoder(),
            ],
        );
    }
}