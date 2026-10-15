<?php

namespace Proceda\Cobranca;

readonly class Agente
{
    public function __construct(
        protected ?string $codigo = null,
        protected ?string $nome = null,
        protected ?string $agencia = null,
        protected ?string $digitoAgencia = null,
        protected ?string $conta = null,
        protected ?string $digitoConta = null,
    ) {}

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function getAgencia(): ?string
    {
        return $this->agencia;
    }

    public function getDigitoAgencia(): ?string
    {
        return $this->digitoAgencia;
    }

    public function getConta(): ?string
    {
        return $this->conta;
    }

    public function getDigitoConta(): ?string
    {
        return $this->digitoConta;
    }
}
