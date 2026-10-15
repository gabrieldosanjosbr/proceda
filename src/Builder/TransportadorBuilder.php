<?php

namespace Proceda\Builder;

use Proceda\Cobranca;
use Proceda\Conhecimento;
use Proceda\Empresa;
use Proceda\Ocorrencia;
use Proceda\Transportador;

class TransportadorBuilder extends Builder
{
    protected ?Empresa $empresa = null;

    /** @var Ocorrencia[] */
    protected array $ocorrencias = [];

    /** @var Cobranca[] */
    protected array $cobrancas = [];

    /** @var Conhecimento[] */
    protected array $conhecimentos = [];

    public function empresa(Empresa $empresa): static
    {
        $this->empresa = $empresa;

        return $this;
    }

    public function addOcorrencia(Ocorrencia $ocorrencia): static
    {
        $this->ocorrencias[] = $ocorrencia;

        return $this;
    }

    public function addCobranca(Cobranca $cobranca): static
    {
        $this->cobrancas[] = $cobranca;

        return $this;
    }

    public function addConhecimento(Conhecimento $conhecimento): static
    {
        $this->conhecimentos[] = $conhecimento;

        return $this;
    }

    public function build(): Transportador
    {
        return new Transportador(
            empresa: $this->empresa,
            ocorrencias: $this->ocorrencias,
            cobrancas: $this->cobrancas,
            conhecimentos: $this->conhecimentos,
        );
    }
}
