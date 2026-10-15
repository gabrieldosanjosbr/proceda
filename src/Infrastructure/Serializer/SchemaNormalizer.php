<?php

namespace Proceda\Infrastructure\Serializer;

use Proceda\Schema;
use Proceda\Schema\Manager;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SchemaNormalizer implements
    NormalizerInterface,
    DenormalizerInterface,
    DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function __construct(
        private readonly ?Manager $schemaManager = null,
    ) {}

    public function normalize(
        mixed $data,
        ?string $format = null,
        array $context = [],
    ): string {
        /** @var Schema $data */
        return $data->path;
    }

    public function denormalize(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): Schema {
        if ($this->schemaManager === null) {
            throw new \LogicException(
                'Manager is required to denormalize Schema.',
            );
        }

        assert(is_string($data));

        $schema = $this->schemaManager->findByPath(
            $data,
        );

        if ($schema === null) {
            throw new \UnexpectedValueException(
                sprintf(
                    'Schema "%s" not found.',
                    $data,
                ),
            );
        }

        return $schema;
    }

    public function supportsNormalization(
        mixed $data,
        ?string $format = null,
        array $context = [],
    ): bool {
        return $data instanceof Schema;
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): bool {
        return $type === Schema::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Schema::class => true,
        ];
    }
}