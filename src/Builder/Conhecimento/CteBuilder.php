<?php

namespace Proceda\Builder\Conhecimento;

use Proceda\Builder\Builder;
use Proceda\Conhecimento\Cte;

class CteBuilder extends Builder
{
    protected ?string $modelo = null;
    protected ?string $chaveAcesso = null;
    protected ?string $protocolo  = null;

    public function modelo(string $value): static
    {
        $this->modelo = $value;
        return $this;
    }
    public function chaveAcesso(string $value): static
    {
        $this->chaveAcesso = $value;
        return $this;
    }
    public function protocolo(string $value): static
    {
        $this->protocolo = $value;
        return $this;
    }

    public function build(): Cte
    {
        return new Cte(
            modelo: $this->modelo,
            chaveAcesso: $this->chaveAcesso,
            protocolo: $this->protocolo,
        );
    }
}
