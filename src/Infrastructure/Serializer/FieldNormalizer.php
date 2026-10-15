<?php

namespace Proceda\Infrastructure\Serializer;

use Proceda\Schema\Field;
use Proceda\Schema\Manager;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class FieldNormalizer implements
    NormalizerInterface,
    DenormalizerInterface
{
    public function __construct(
        private Manager $schemaManager,
    ) {}

    public function normalize(
        mixed $data,
        ?string $format = null,
        array $context = [],
    ): array {
        assert($data instanceof Field);

        return [
            'path' => $data->path,
            'property' => $data->property,
            'label' => $data->label,
        ];
    }

    public function denormalize(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): Field {
        assert(is_array($data));

        $schema = $this->schemaManager->findByPath(
            $data['path']
        );

        return $schema->fields[$data['property']];
    }

    public function supportsNormalization(
        mixed $data,
        ?string $format = null,
        array $context = [],
    ): bool {
        return $data instanceof Field;
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): bool {
        return $type === Field::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Field::class => true,
        ];
    }
}