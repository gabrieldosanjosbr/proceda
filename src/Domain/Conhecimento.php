<?php

namespace Proceda;

use Proceda\Conhecimento\Cte;

readonly class Conhecimento
{
    public function __construct(
        protected ?string       $serie = null,
        protected ?string       $numero = null,
        protected ?Type         $tipo = null,
        protected ?string       $dataEmissao = null,
        protected ?Type         $acaoDocumento = null,
        protected ?string       $contaRazao = null,
        protected ?Type         $codigoIva = null,
        protected ?bool         $devolucao = null,
        protected ?NotaFiscal   $resumo = null,
        protected ?Empresa      $contratante = null,
        protected ?Empresa      $emissor = null,
        protected ?Empresa      $responsavel = null,
        protected ?Veiculo      $veiculo = null,
        protected ?Conhecimento $originador = null,
        protected ?Cte          $cte = null,
        /** @var list<NotaFiscal> */
        protected array         $notasFiscais = [],
        /** @var list<Embarque>  */
        protected array         $embarques = [],
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

    public function getDataEmissao(): ?string
    {
        return $this->dataEmissao;
    }

    public function getAcaoDocumento(): ?Type
    {
        return $this->acaoDocumento;
    }

    public function getContaRazao(): ?string
    {
        return $this->contaRazao;
    }

    public function getCodigoIva(): ?Type
    {
        return $this->codigoIva;
    }

    public function getDevolucao(): ?bool
    {
        return $this->devolucao;
    }

    public function getResumo(): ?NotaFiscal
    {
        return $this->resumo;
    }

    public function getContratante(): ?Empresa
    {
        return $this->contratante;
    }

    public function getEmissor(): ?Empresa
    {
        return $this->emissor;
    }

    public function getResponsavel(): ?Empresa
    {
        return $this->responsavel;
    }

    public function getVeiculo(): ?Veiculo
    {
        return $this->veiculo;
    }

    public function getOriginador(): ?Conhecimento
    {
        return $this->originador;
    }

    public function getCte(): ?Cte
    {
        return $this->cte;
    }

    public function getNotasFiscais(): array
    {
        return $this->notasFiscais;
    }

    public function getEmbarques(): array
    {
        return $this->embarques;
    }
}
