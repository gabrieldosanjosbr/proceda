<?php

namespace Proceda\Error;

use Proceda\Line;
use Symfony\Component\Serializer\Attribute\DiscriminatorMap;

#[DiscriminatorMap(
    typeProperty: '_type',
    mapping: [
        'field' => FieldError::class,
        'layout' => LayoutError::class,
    ],
)]
interface Error
{
    public function message(): string;

    public function withLine(Line $line): self;
}
