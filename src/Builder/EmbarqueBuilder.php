<?php

namespace Proceda\Builder;

use Proceda\Embarque;

class EmbarqueBuilder extends Builder
{
    protected ?string $identificacao = null;

    public function identificacao(?string $identificacao): static
    {
        $this->identificacao = $identificacao;

        return $this;
    }

    public function build(): Embarque
    {
        return new Embarque(
            identificacao: $this->identificacao,
        );
    }
}
