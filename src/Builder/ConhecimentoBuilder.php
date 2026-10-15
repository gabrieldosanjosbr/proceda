<?php

namespace Proceda\Builder;

use Proceda\Conhecimento;
use Proceda\Conhecimento\Cte;
use Proceda\Embarque;
use Proceda\Empresa;
use Proceda\NotaFiscal;
use Proceda\Type;
use Proceda\Veiculo;

class ConhecimentoBuilder extends Builder
{
    protected ?string $serie = null;
    protected ?string $numero = null;
    protected ?string $dataEmissao = null;
    protected ?Type $acaoDocumento = null;
    protected ?Type $tipo = null;
    protected ?string $contaRazao = null;
    protected ?Type $codigoIva = null;
    protected ?bool $devolucao = null;
    protected ?NotaFiscal $resumo = null;
    protected ?Empresa $contratante = null;
    protected ?Empresa $emissor = null;
    protected ?Empresa $responsavel = null;
    protected ?Veiculo $veiculo = null;
    protected ?Conhecimento $originador = null;
    protected ?Cte $cte = null;

    /** @var NotaFiscal[] */
    protected array $notasFiscais = [];

    /** @var Embarque[] */
    protected array $embarques = [];

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
    public function dataEmissao(?string $value): static
    {
        $this->dataEmissao = $value;
        return $this;
    }
    public function acaoDocumento(?Type $value): static
    {
        $this->acaoDocumento = $value;
        return $this;
    }
    public function tipo(?Type $value): static
    {
        $this->tipo = $value;
        return $this;
    }
    public function contaRazao(?string $value): static
    {
        $this->contaRazao = $value;
        return $this;
    }
    public function codigoIva(?Type $value): static
    {
        $this->codigoIva = $value;
        return $this;
    }
    public function devolucao(?bool $value): static
    {
        $this->devolucao = $value;
        return $this;
    }
    public function resumo(?NotaFiscal $value): static
    {
        $this->resumo = $value;
        return $this;
    }
    public function contratante(?Empresa $value): static
    {
        $this->contratante = $value;
        return $this;
    }
    public function emissor(?Empresa $value): static
    {
        $this->emissor = $value;
        return $this;
    }
    public function responsavel(?Empresa $value): static
    {
        $this->responsavel = $value;
        return $this;
    }
    public function veiculo(?Veiculo $value): static
    {
        $this->veiculo = $value;
        return $this;
    }
    public function originador(?Conhecimento $value): static
    {
        $this->originador = $value;
        return $this;
    }
    public function cte(?Cte $value): static
    {
        $this->cte = $value;
        return $this;
    }

    public function addNotaFiscal(NotaFiscal $notaFiscal): static
    {
        $this->notasFiscais[] = $notaFiscal;
        return $this;
    }

    public function addEmbarque(Embarque $embarque): static
    {
        $this->embarques[] = $embarque;
        return $this;
    }

    public function build(): Conhecimento
    {
        return new Conhecimento(
            serie: $this->serie,
            numero: $this->numero,
            tipo: $this->tipo,
            dataEmissao: $this->dataEmissao,
            acaoDocumento: $this->acaoDocumento,
            contaRazao: $this->contaRazao,
            codigoIva: $this->codigoIva,
            devolucao: $this->devolucao,
            resumo: $this->resumo,
            contratante: $this->contratante,
            emissor: $this->emissor,
            responsavel: $this->responsavel,
            veiculo: $this->veiculo,
            originador: $this->originador,
            cte: $this->cte,
            notasFiscais: $this->notasFiscais,
            embarques: $this->embarques,
        );
    }
}
