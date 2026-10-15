<?php

namespace Proceda\Documento;

use Symfony\Component\Serializer\Attribute\DiscriminatorMap;

#[DiscriminatorMap(
    typeProperty: '_type',
    mapping: [
        'notfis' => Notfis::class,
        'doccob' => Doccob::class,
        'ocoren' => Ocoren::class,
        'conemb' => Conemb::class,
    ],
)]
interface DocumentoInterface {}
