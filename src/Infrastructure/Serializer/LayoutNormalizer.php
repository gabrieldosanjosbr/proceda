<?php

namespace Proceda\Infrastructure\Serializer;

use Proceda\Layout;
use Proceda\Proceda;
use Proceda\Schema\LayoutType;
use Proceda\Schema\LayoutVersion;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class LayoutNormalizer implements
    NormalizerInterface,
    DenormalizerInterface
{
    public function normalize(
        mixed $data,
        ?string $format = null,
        array $context = [],
    ): array {
        if (!$data instanceof Layout) {
            throw new \InvalidArgumentException(
                'Expected instance of ' . Layout::class,
            );
        }

        return [
            'type' => $data->type->value,
            'version' => $data->version->value,
            'profile' => $data->profile,
        ];
    }

    public function supportsNormalization(
        mixed $data,
        ?string $format = null,
        array $context = [],
    ): bool {
        return $data instanceof Layout;
    }

    public function denormalize(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): Layout {
        if (!is_array($data)) {
            throw new \InvalidArgumentException(
                'Invalid Layout payload.',
            );
        }

        return Proceda::create(
            layoutType: LayoutType::from($data['type']),
            layoutVersion: LayoutVersion::from($data['version']),
            profile: $data['profile']
        );
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): bool {
        return $type === Layout::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Layout::class => true,
        ];
    }
}