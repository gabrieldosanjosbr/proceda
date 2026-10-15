<?php

namespace Proceda\Builder;

use Proceda\Destinatario;
use Proceda\Embarcador;
use Proceda\Empresa;

class EmbarcadorBuilder extends Builder
{
    protected ?Empresa $empresa = null;
    protected ?string $dataEmbarque = null;

    protected ?string $inscricaoEstadualSubstitutoTributario = null;

    protected ?string $contatoEmergencia = null;

    protected ?Empresa $localColetaNotaFiscal = null;

    /** @var Destinatario[] */
    protected array $destinatarios = [];

    public function empresa(?Empresa $empresa): static
    {
        $this->empresa = $empresa;

        return $this;
    }

    public function dataEmbarque(?string $data): static
    {
        $this->dataEmbarque = $data;

        return $this;
    }

    public function inscricaoEstadualSubstitutoTributario(?string $inscricao): static
    {
        $this->inscricaoEstadualSubstitutoTributario = $inscricao;

        return $this;
    }

    public function contatoEmergencia(?string $contato): static
    {
        $this->contatoEmergencia = $contato;

        return $this;
    }

    public function localColetaNotaFiscal(?Empresa $empresa): static
    {
        $this->localColetaNotaFiscal = $empresa;

        return $this;
    }

    public function addDestinatario(Destinatario $destinatario): static
    {
        $this->destinatarios[] = $destinatario;

        return $this;
    }

    /**
     * @param Destinatario[] $destinatarios
     */
    public function destinatarios(array $destinatarios): static
    {
        $this->destinatarios = $destinatarios;

        return $this;
    }

    public function build(): Embarcador
    {
        return new Embarcador(
            empresa: $this->empresa,
            dataEmbarque: $this->dataEmbarque,
            inscricaoEstadualSubstitutoTributario: $this->inscricaoEstadualSubstitutoTributario,
            contatoEmergencia: $this->contatoEmergencia,
            localColetaNotaFiscal: $this->localColetaNotaFiscal,
            destinatarios: $this->destinatarios,
        );
    }
}
