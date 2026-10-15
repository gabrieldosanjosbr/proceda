<?php

namespace Proceda\Builder;

use Proceda\Destinatario;
use Proceda\NotaFiscal;
use Proceda\Type;

class DestinatarioBuilder extends Builder
{
    protected ?string $nome = null;
    protected ?string $documento = null;
    protected ?string $inscricaoEstadual = null;
    protected ?string $inscricaoSuframa = null;
    protected ?string $endereco = null;
    protected ?string $bairro = null;
    protected ?string $cidade = null;
    protected ?Type $codigoPais = null;
    protected ?string $codigoMunicipio = null;
    protected ?string $cep = null;
    protected ?string $uf = null;
    protected ?string $areaFrete = null;
    protected ?string $numeroComunicacao = null;
    protected ?Type $estabelecimentoDestino = null;
    protected ?Type $tipo = null;
    protected ?Destinatario $localEntregaNotaFiscal = null;

    /** @var NotaFiscal[] */
    protected array $notasFiscais = [];

    public function nome(?string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function documento(?string $documento): static
    {
        $this->documento = $documento;

        return $this;
    }

    public function inscricaoEstadual(?string $inscricaoEstadual): static
    {
        $this->inscricaoEstadual = $inscricaoEstadual;

        return $this;
    }

    public function inscricaoSuframa(?string $inscricaoSuframa): static
    {
        $this->inscricaoSuframa = $inscricaoSuframa;

        return $this;
    }

    public function endereco(?string $endereco): static
    {
        $this->endereco = $endereco;

        return $this;
    }

    public function bairro(?string $bairro): static
    {
        $this->bairro = $bairro;

        return $this;
    }

    public function cidade(?string $cidade): static
    {
        $this->cidade = $cidade;

        return $this;
    }

    public function codigoPais(?Type $codigoPais): static
    {
        $this->codigoPais = $codigoPais;

        return $this;
    }

    public function codigoMunicipio(?string $codigoMunicipio): static
    {
        $this->codigoMunicipio = $codigoMunicipio;

        return $this;
    }

    public function cep(?string $cep): static
    {
        $this->cep = $cep;

        return $this;
    }

    public function uf(?string $uf): static
    {
        $this->uf = $uf;

        return $this;
    }

    public function areaFrete(?string $areaFrete): static
    {
        $this->areaFrete = $areaFrete;

        return $this;
    }

    public function numeroComunicacao(?string $numeroComunicacao): static
    {
        $this->numeroComunicacao = $numeroComunicacao;

        return $this;
    }

    public function estabelecimentoDestino(?Type $tipo): static
    {
        $this->estabelecimentoDestino = $tipo;

        return $this;
    }

    public function tipo(?Type $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function localEntregaNotaFiscal(?Destinatario $localEntregaNotaFiscal): static
    {
        $this->localEntregaNotaFiscal = $localEntregaNotaFiscal;

        return $this;
    }

    public function addNotaFiscal(NotaFiscal $notaFiscal): static
    {
        $this->notasFiscais[] = $notaFiscal;

        return $this;
    }

    /**
     * @param NotaFiscal[] $notasFiscais
     */
    public function notasFiscais(array $notasFiscais): static
    {
        $this->notasFiscais = $notasFiscais;

        return $this;
    }

    public function build(): Destinatario
    {
        return new Destinatario(
            nome: $this->nome,
            documento: $this->documento,
            inscricaoEstadual: $this->inscricaoEstadual,
            inscricaoSuframa: $this->inscricaoSuframa,
            endereco: $this->endereco,
            bairro: $this->bairro,
            cidade: $this->cidade,
            codigoMunicipio: $this->codigoMunicipio,
            codigoPais: $this->codigoPais,
            cep: $this->cep,
            uf: $this->uf,
            areaFrete: $this->areaFrete,
            numeroComunicacao: $this->numeroComunicacao,
            estabelecimentoDestino: $this->estabelecimentoDestino,
            tipo: $this->tipo,
            localEntregaNotaFiscal: $this->localEntregaNotaFiscal,
            notasFiscais: $this->notasFiscais,
        );
    }
}
