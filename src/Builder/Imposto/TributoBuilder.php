<?php

namespace Proceda\Builder\Imposto;

use Proceda\Builder\Builder;
use Proceda\Builder\SchemaAwareBuilderInterface;
use Proceda\Imposto\Tributo;
use Proceda\Schema;
use Proceda\Type;

class TributoBuilder extends Builder implements SchemaAwareBuilderInterface
{
    protected ?Type $tipo = null;
    protected ?float $baseCalculo = null;
    protected ?float $aliquota = null;
    protected ?float $valor = null;
    protected ?float $valorRetido = null;
    protected ?Type $substituicaoTributaria = null;

    public function configureFromSchema(Schema $schema): void
    {
        $this->tipo = $schema->type;
    }

    public function tipo(?Type $tipo): TributoBuilder
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function baseCalculo(?float $baseCalculo): TributoBuilder
    {
        $this->baseCalculo = $baseCalculo;

        return $this;
    }

    public function aliquota(?float $aliquota): TributoBuilder
    {
        $this->aliquota = $aliquota;

        return $this;
    }

    public function valor(?float $valor): TributoBuilder
    {
        $this->valor = $valor;

        return $this;
    }

    public function valorRetido(?float $valorRetido): TributoBuilder
    {
        $this->valorRetido = $valorRetido;

        return $this;
    }

    public function substituicaoTributaria(
        ?Type $substituicaoTributaria,
    ): TributoBuilder {
        $this->substituicaoTributaria = $substituicaoTributaria;

        return $this;
    }

    public function build(): Tributo
    {
        return new Tributo(
            tipo: $this->tipo,
            baseCalculo: $this->baseCalculo,
            aliquota: $this->aliquota,
            valor: $this->valor,
            valorRetido: $this->valorRetido,
            substituicaoTributaria: $this->substituicaoTributaria,
        );
    }
}
