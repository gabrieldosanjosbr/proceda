<?php

namespace Proceda;

use Proceda\Cobranca\Agente;

readonly class Cobranca
{
    public function __construct(
        protected ?string  $serie = null,
        protected ?string  $numero = null,
        protected ?Type    $tipo = null,
        protected ?Type    $documento = null,
        protected ?string  $dataEmissao = null,
        protected ?string  $dataVencimento = null,
        protected ?float   $valor = null,
        protected ?float   $multaPorAtraso = null,
        protected ?float   $valorJurosDiaAtraso = null,
        protected ?string  $dataLimitePagamentoDesconto = null,
        protected ?float   $valorDesconto = null,
        protected ?Type    $acaoDocumento = null,
        protected ?string  $identificacaoPreFaturaCliente = null,
        protected ?string  $complementarPreFatura = null,
        protected ?string  $cfop = null,
        protected ?Empresa $emissor = null,
        protected ?Agente  $agente = null,
        protected ?Nfe     $nfe = null,
        protected ?Imposto $imposto = null,

        /** @var Conhecimento[]  */
        protected array    $conhecimentos = [],
    ) {}

    public function getSerie(): ?string
    {
        return $this->serie;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function getTipo(): ?Type
    {
        return $this->tipo;
    }

    public function getDocumento(): ?Type
    {
        return $this->documento;
    }

    public function getDataEmissao(): ?string
    {
        return $this->dataEmissao;
    }

    public function getDataVencimento(): ?string
    {
        return $this->dataVencimento;
    }

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function getMultaPorAtraso(): ?float
    {
        return $this->multaPorAtraso;
    }

    public function getValorJurosDiaAtraso(): ?float
    {
        return $this->valorJurosDiaAtraso;
    }

    public function getDataLimitePagamentoDesconto(): ?string
    {
        return $this->dataLimitePagamentoDesconto;
    }

    public function getValorDesconto(): ?float
    {
        return $this->valorDesconto;
    }

    public function getAcaoDocumento(): ?Type
    {
        return $this->acaoDocumento;
    }

    public function getIdentificacaoPreFaturaCliente(): ?string
    {
        return $this->identificacaoPreFaturaCliente;
    }

    public function getComplementarPreFatura(): ?string
    {
        return $this->complementarPreFatura;
    }

    public function getCfop(): ?string
    {
        return $this->cfop;
    }

    public function getEmissor(): ?Empresa
    {
        return $this->emissor;
    }

    public function getAgente(): ?Agente
    {
        return $this->agente;
    }

    public function getNfe(): ?Nfe
    {
        return $this->nfe;
    }

    public function getImposto(): ?Imposto
    {
        return $this->imposto;
    }

    public function getConhecimentos(): array
    {
        return $this->conhecimentos;
    }
}
