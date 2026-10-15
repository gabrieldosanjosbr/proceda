<?php

namespace Proceda;

use Proceda\NotaFiscal\Item;

readonly class Ocorrencia
{
    public function __construct(
        protected ?Type         $codigo = null,
        protected ?string       $data = null,
        protected ?string       $hora = null,
        protected ?Type         $observacaoEntrada = null,
        protected ?Type         $indicacaoEntrega = null,
        protected ?NotaFiscal   $notaFiscal = null,
        protected ?NotaFiscal   $devolucao = null,
        protected ?Conhecimento $conhecimento = null,
        protected ?Conhecimento $originador = null,

        /** @var Item[] */
        protected array         $items = [],

        /** @var TextoLivre[] */
        protected array         $textosLivres = [],

        /** @var Embarque[] */
        protected array         $embarques = [],

        /** @var Evento[] */
        protected array         $eventos = [],
    ) {}

    public function getCodigo(): ?Type
    {
        return $this->codigo;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function getHora(): ?string
    {
        return $this->hora;
    }

    public function getObservacaoEntrada(): ?Type
    {
        return $this->observacaoEntrada;
    }

    public function getIndicacaoEntrega(): ?Type
    {
        return $this->indicacaoEntrega;
    }

    public function getNotaFiscal(): ?NotaFiscal
    {
        return $this->notaFiscal;
    }

    public function getDevolucao(): ?NotaFiscal
    {
        return $this->devolucao;
    }

    public function getConhecimento(): ?Conhecimento
    {
        return $this->conhecimento;
    }

    public function getOriginador(): ?Conhecimento
    {
        return $this->originador;
    }

    public function getTextosLivres(): array
    {
        return $this->textosLivres;
    }

    public function getEmbarques(): array
    {
        return $this->embarques;
    }

    public function getEventos(): array
    {
        return $this->eventos;
    }
}
