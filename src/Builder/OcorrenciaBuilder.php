<?php

namespace Proceda\Builder;

use Proceda\Conhecimento;
use Proceda\Embarque;
use Proceda\Evento;
use Proceda\NotaFiscal;
use Proceda\Ocorrencia;
use Proceda\TextoLivre;
use Proceda\Type;

class OcorrenciaBuilder extends Builder
{
    protected ?Type $codigo = null;
    protected ?string $data = null;
    protected ?string $hora = null;
    protected ?Type $observacaoEntrada = null;
    protected ?Type $indicacaoEntrega = null;
    protected ?NotaFiscal $notaFiscal = null;
    protected ?NotaFiscal $devolucao = null;
    protected ?Conhecimento $conhecimento = null;
    protected ?Conhecimento $originador = null;

    /** @var TextoLivre[] */
    protected array $textosLivres = [];

    /** @var Embarque[] */
    protected array $embarques = [];

    /** @var Evento[]  */
    protected array $eventos = [];

    public function codigo(?Type $value): static
    {
        $this->codigo = $value;
        return $this;
    }
    public function data(?string $value): static
    {
        $this->data = $value;
        return $this;
    }
    public function hora(?string $value): static
    {
        $this->hora = $value;
        return $this;
    }
    public function observacaoEntrada(?Type $value): static
    {
        $this->observacaoEntrada = $value;
        return $this;
    }
    public function indicacaoEntrega(?Type $value): static
    {
        $this->indicacaoEntrega = $value;
        return $this;
    }
    public function notaFiscal(?NotaFiscal $value): static
    {
        $this->notaFiscal = $value;
        return $this;
    }
    public function devolucao(?NotaFiscal $value): static
    {
        $this->devolucao = $value;
        return $this;
    }
    public function conhecimento(?Conhecimento $value): static
    {
        $this->conhecimento = $value;
        return $this;
    }
    public function originador(?Conhecimento $value): static
    {
        $this->originador = $value;
        return $this;
    }

    public function addEmbarque(Embarque $embarque): static
    {
        $this->embarques[] = $embarque;

        return $this;
    }

    public function addTextoLivre(TextoLivre $textoLivre): static
    {
        $this->textosLivres[] = $textoLivre;

        return $this;
    }

    public function addEvento(Evento $evento): static
    {
        $this->eventos[] = $evento;

        return $this;
    }

    public function build(): Ocorrencia
    {
        return new Ocorrencia(
            codigo: $this->codigo,
            data: $this->data,
            hora: $this->hora,
            observacaoEntrada: $this->observacaoEntrada,
            indicacaoEntrega: $this->indicacaoEntrega,
            notaFiscal: $this->notaFiscal,
            devolucao: $this->devolucao,
            conhecimento: $this->conhecimento,
            originador: $this->originador,
            textosLivres: $this->textosLivres,
            embarques: $this->embarques,
            eventos: $this->eventos,
        );
    }
}
