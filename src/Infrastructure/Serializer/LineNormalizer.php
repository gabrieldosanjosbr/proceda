<?php

namespace Proceda\Infrastructure\Serializer;

use Proceda\Line;
use Proceda\Schema\RegistroResolver;
use Random\RandomException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class LineNormalizer implements
    NormalizerInterface,
    DenormalizerInterface
{
    public function __construct(
        private RegistroResolver $registroResolver,
    ) {}

    public function normalize(
        mixed $data,
        ?string $format = null,
        array $context = [],
    ): array {
        assert($data instanceof Line);

        return [
            'id' => $data->id,
            'content' => base64_encode($data->content),
            'registro' => $data->registro->identificacao,
            'number' => $data->number,
        ];
    }

    /**
     * @throws RandomException
     */
    public function denormalize(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): Line {
        assert(is_array($data));
        assert(is_string($data['content']));
        assert(is_string($data['registro']));

        $content = base64_decode(
            $data['content'],
            true,
        );

        if ($content === false) {
            throw new \UnexpectedValueException(
                'Invalid Line content.',
            );
        }

        $registro = $this->registroResolver->get(
            $data['registro'],
        );

        return new Line(
            content: $content,
            registro: $registro,
            number: $data['number'],
            id: $data['id'],
        );
    }

    public function supportsNormalization(
        mixed $data,
        ?string $format = null,
        array $context = [],
    ): bool {
        return $data instanceof Line;
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): bool {
        return $type === Line::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Line::class => true,
        ];
    }
}