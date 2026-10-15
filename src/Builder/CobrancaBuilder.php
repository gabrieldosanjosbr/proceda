<?php

namespace Proceda\Builder;

use Proceda\Cobranca;
use Proceda\Cobranca\Agente;
use Proceda\Conhecimento;
use Proceda\Empresa;
use Proceda\Imposto;
use Proceda\Nfe;
use Proceda\Type;

class CobrancaBuilder extends Builder
{
    protected ?string $serie = null;
    protected ?string $numero = null;
    protected ?Type $tipo = null;
    protected ?Type $documento = null;
    protected ?Empresa $emissor = null;
    protected ?string $dataEmissao = null;
    protected ?string $dataVencimento = null;
    protected ?float $valor = null;
    protected ?float $multaPorAtraso = null;
    protected ?float $valorJurosDiaAtraso = null;
    protected ?string $dataLimitePagamentoDesconto = null;
    protected ?float $valorDesconto = null;
    protected ?Type $acaoDocumento = null;
    protected ?string $identificacaoPreFaturaCliente = null;
    protected ?string $complementarPreFatura = null;
    protected ?string $cfop = null;
    protected ?Agente $agente = null;
    protected ?Nfe $nfe = null;
    protected ?Imposto $imposto = null;

    /** @var Conhecimento[] */
    protected array $conhecimentos = [];

    public function serie(?string $value): static
    {
        $this->serie = $value;
        return $this;
    }
    public function numero(?string $value): static
    {
        $this->numero = $value;
        return $this;
    }
    public function tipo(?Type $value): static
    {
        $this->tipo = $value;
        return $this;
    }
    public function documento(?Type $value): static
    {
        $this->documento = $value;
        return $this;
    }
    public function emissor(?Empresa $value): static
    {
        $this->emissor = $value;
        return $this;
    }
    public function dataEmissao(?string $value): static
    {
        $this->dataEmissao = $value;
        return $this;
    }
    public function dataVencimento(?string $value): static
    {
        $this->dataVencimento = $value;
        return $this;
    }
    public function valor(?float $value): static
    {
        $this->valor = $value;
        return $this;
    }
    public function multaPorAtraso(?float $value): static
    {
        $this->multaPorAtraso = $value;
        return $this;
    }
    public function valorJurosDiaAtraso(?float $value): static
    {
        $this->valorJurosDiaAtraso = $value;
        return $this;
    }
    public function dataLimitePagamentoDesconto(?string $value): static
    {
        $this->dataLimitePagamentoDesconto = $value;
        return $this;
    }
    public function valorDesconto(?float $value): static
    {
        $this->valorDesconto = $value;
        return $this;
    }
    public function acaoDocumento(?Type $value): static
    {
        $this->acaoDocumento = $value;
        return $this;
    }
    public function identificacaoPreFaturaCliente(?string $value): static
    {
        $this->identificacaoPreFaturaCliente = $value;
        return $this;
    }
    public function complementarPreFatura(?string $value): static
    {
        $this->complementarPreFatura = $value;
        return $this;
    }
    public function cfop(?string $value): static
    {
        $this->cfop = $value;
        return $this;
    }
    public function agente(?Agente $agente): static
    {
        $this->agente = $agente;
        return $this;
    }
    public function nfe(?Nfe $value): static
    {
        $this->nfe = $value;
        return $this;
    }
    public function imposto(?Imposto $value): static
    {
        $this->imposto = $value;
        return $this;
    }

    public function addConhecimento(Conhecimento $conhecimento): static
    {
        $this->conhecimentos[] = $conhecimento;

        return $this;
    }

    public function build(): Cobranca
    {
        return new Cobranca(
            serie: $this->serie,
            numero: $this->numero,
            tipo: $this->tipo,
            documento: $this->documento,
            dataEmissao: $this->dataEmissao,
            dataVencimento: $this->dataVencimento,
            valor: $this->valor,
            multaPorAtraso: $this->multaPorAtraso,
            valorJurosDiaAtraso: $this->valorJurosDiaAtraso,
            dataLimitePagamentoDesconto: $this->dataLimitePagamentoDesconto,
            valorDesconto: $this->valorDesconto,
            acaoDocumento: $this->acaoDocumento,
            identificacaoPreFaturaCliente: $this->identificacaoPreFaturaCliente,
            complementarPreFatura: $this->complementarPreFatura,
            cfop: $this->cfop,
            emissor: $this->emissor,
            agente: $this->agente,
            nfe: $this->nfe,
            imposto: $this->imposto,
            conhecimentos: $this->conhecimentos,
        );
    }
}
