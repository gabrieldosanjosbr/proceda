<?php

namespace Proceda\Builder;

use Proceda\TextoLivre;

class TextoLivreBuilder extends Builder
{
    protected ?string $texto = null;

    public function texto(?string $texto): static
    {
        $this->texto = $texto;

        return $this;
    }

    public function build(): TextoLivre
    {
        return new TextoLivre(
            texto: $this->texto,
        );
    }
}
