<?php

namespace Proceda\Builder;

use Proceda\Nfe;

class NfeBuilder extends Builder
{
    protected ?string $codigo = null;
    protected ?string $chaveAcesso = null;
    protected ?string $protocolo = null;

    public function codigo(?string $codigo): static
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function chaveAcessoComDigitoVerificador(?string $chave): static
    {
        $this->chaveAcesso = $chave;

        return $this;
    }

    public function protocolo(?string $protocolo): static
    {
        $this->protocolo = $protocolo;

        return $this;
    }

    public function build(): Nfe
    {
        return new Nfe(
            codigo: $this->codigo,
            chaveAcesso: $this->chaveAcesso,
            protocolo: $this->protocolo,
        );
    }
}
