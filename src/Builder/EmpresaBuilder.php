<?php

namespace Proceda\Builder;

use Proceda\Empresa;
use Proceda\Unidade;

class EmpresaBuilder extends Builder
{
    protected ?string $codigo = null;
    protected ?string $nome = null;
    protected ?string $cnpj = null;
    protected ?string $inscricaoEstadual = null;
    protected ?string $inscricaoMunicipal = null;
    protected ?string $endereco = null;
    protected ?string $bairro = null;
    protected ?string $cep = null;
    protected ?string $cidade = null;
    protected ?string $uf = null;
    protected ?string $codigoMunicipio = null;
    protected ?string $numeroComunicacao = null;
    protected ?string $areaFrete = null;
    protected ?Unidade $filial = null;

    public function codigo(?string $codigo): static
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function nome(?string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function cnpj(?string $cnpj): static
    {
        $this->cnpj = $cnpj;

        return $this;
    }

    public function inscricaoEstadual(?string $inscricaoEstadual): static
    {
        $this->inscricaoEstadual = $inscricaoEstadual;

        return $this;
    }

    public function inscricaoMunicipal(?string $inscricaoMunicipal): static
    {
        $this->inscricaoMunicipal = $inscricaoMunicipal;

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

    public function cep(?string $cep): static
    {
        $this->cep = $cep;

        return $this;
    }

    public function cidade(?string $cidade): static
    {
        $this->cidade = $cidade;

        return $this;
    }

    public function uf(?string $uf): static
    {
        $this->uf = $uf;

        return $this;
    }

    public function codigoMunicipio(?string $codigoMunicipio): static
    {
        $this->codigoMunicipio = $codigoMunicipio;

        return $this;
    }

    public function numeroComunicacao(?string $numeroComunicacao): static
    {
        $this->numeroComunicacao = $numeroComunicacao;

        return $this;
    }

    public function areaFrete(?string $areaFrete): static
    {
        $this->areaFrete = $areaFrete;

        return $this;
    }

    public function filial(?Unidade $unidade): static
    {
        $this->filial = $unidade;

        return $this;
    }

    public function build(): Empresa
    {
        return new Empresa(
            codigo: $this->codigo,
            nome: $this->nome,
            cnpj: $this->cnpj,
            inscricaoEstadual: $this->inscricaoEstadual,
            inscricaoMunicipal: $this->inscricaoMunicipal,
            endereco: $this->endereco,
            bairro: $this->bairro,
            cep: $this->cep,
            cidade: $this->cidade,
            uf: $this->uf,
            areaFrete: $this->areaFrete,
            codigoMunicipio: $this->codigoMunicipio,
            numeroComunicacao: $this->numeroComunicacao,
            filial: $this->filial,
        );
    }
}
