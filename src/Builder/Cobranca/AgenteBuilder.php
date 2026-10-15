<?php

namespace Proceda\Builder\Cobranca;

use Proceda\Builder\Builder;
use Proceda\Cobranca\Agente;

class AgenteBuilder extends Builder
{
    protected ?string $codigo = null;
    protected ?string $nome = null;
    protected ?string $agencia = null;
    protected ?string $digitoAgencia = null;
    protected ?string $conta = null;
    protected ?string $digitoConta = null;

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

    public function agencia(?string $agencia): static
    {
        $this->agencia = $agencia;

        return $this;
    }

    public function digitoAgencia(?string $digitoAgencia): static
    {
        $this->digitoAgencia = $digitoAgencia;

        return $this;
    }

    public function conta(?string $conta): static
    {
        $this->conta = $conta;

        return $this;
    }

    public function digitoConta(?string $digitoConta): static
    {
        $this->digitoConta = $digitoConta;

        return $this;
    }

    public function build(): Agente
    {
        return new Agente(
            codigo: $this->codigo,
            nome: $this->nome,
            agencia: $this->agencia,
            digitoAgencia: $this->digitoAgencia,
            conta: $this->conta,
            digitoConta: $this->digitoConta,
        );
    }
}
