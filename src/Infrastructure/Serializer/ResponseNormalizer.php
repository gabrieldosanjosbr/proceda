<?php

namespace Proceda\Infrastructure\Serializer;

use Proceda\Error\Validation;
use Proceda\Writer\Response;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ResponseNormalizer implements
    NormalizerInterface,
    DenormalizerInterface,
    NormalizerAwareInterface,
    DenormalizerAwareInterface
{
    use NormalizerAwareTrait;
    use DenormalizerAwareTrait;

    public function normalize(
        mixed   $data,
        ?string $format = null,
        array   $context = [],
    ): array
    {
        assert($data instanceof Response);

        return [
            'successful' => $data->successful,
            'content' => $data->content !== null
                ? base64_encode($data->content)
                : null,
            'encoding' => 'base64',
            'validation' => $this->normalizer->normalize(
                $data->validation,
                $format,
                $context,
            ),
            'exception' => $data->exception,
            'version' => $data->version,
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Response;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Response::class => true,
        ];
    }

    public function denormalize(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): Response {
        assert(is_array($data));

        $content = null;
        $validation = $this->denormalizer->denormalize(
            $data['validation'],
            Validation::class,
            $format,
            $context,
        );

        if ($data['content'] !== null) {
            $content = base64_decode(
                $data['content'],
                true,
            );

            if ($content === false) {
                throw new \UnexpectedValueException(
                    'Invalid Response content.',
                );
            }
        }

        return new Response(
            successful: $data['successful'],
            content: $content,
            encoding: $data['encoding'],
            validation: $validation,
            exception: $data['exception'],
            version: $data['version'],
        );
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): bool {
        return $type === Response::class;
    }
}