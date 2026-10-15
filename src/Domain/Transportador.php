<?php

namespace Proceda;

readonly class Transportador
{
    public function __construct(
        protected ?Empresa $empresa = null,
        /** @var Ocorrencia[] */
        protected array   $ocorrencias = [],
        /** @var Cobranca[] */
        protected array   $cobrancas = [],
        /** @var Conhecimento[] */
        protected array   $conhecimentos = [],
    ) {}

    public function getEmpresa(): ?Empresa
    {
        return $this->empresa;
    }

    public function getOcorrencias(): array
    {
        return $this->ocorrencias;
    }

    public function getCobrancas(): array
    {
        return $this->cobrancas;
    }

    public function getConhecimentos(): array
    {
        return $this->conhecimentos;
    }
}
