<?php

namespace Proceda\Infrastructure\Serializer;

use Proceda\Error\FieldError;
use Proceda\Error\LayoutError;
use Proceda\Error\Validation;
use Proceda\Error\Error;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ValidationNormalizer implements
    NormalizerInterface,
    DenormalizerInterface,
    NormalizerAwareInterface,
    DenormalizerAwareInterface
{
    use NormalizerAwareTrait;
    use DenormalizerAwareTrait;

    public function normalize(
        mixed $data,
        ?string $format = null,
        array $context = [],
    ): array {
        assert($data instanceof Validation);

        return array_map(
            function (Error $error) use ($format, $context): array {
                $type = match (true) {
                    $error instanceof FieldError => 'field',
                    $error instanceof LayoutError => 'layout',
                    default => throw new \LogicException(
                        sprintf(
                            'Unsupported validation error: %s',
                            get_debug_type($error),
                        ),
                    ),
                };

                $normalized = $this->normalizer->normalize(
                    $error,
                    $format,
                    $context,
                );

                assert(is_array($normalized));

                $normalized['_type'] = $type;

                return $normalized;
            },
            $data->errors,
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function denormalize(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): Validation {
        assert(is_array($data));

        $errors = [];

        foreach ($data as $error) {
            $errors[] = $this->denormalizer->denormalize(
                $error,
                Error::class,
                $format,
                $context,
            );
        }

        return new Validation(
            errors: $errors,
        );
    }

    public function supportsNormalization(
        mixed $data,
        ?string $format = null,
        array $context = []
    ): bool {
        return $data instanceof Validation;
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): bool {
        return $type === Validation::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Validation::class => true,
        ];
    }
}