<?php

namespace Proceda\Builder;

use Proceda\Evento;
use Proceda\Schema;
use Proceda\Type;

class EventoBuilder extends Builder implements SchemaAwareBuilderInterface
{
    protected Type $tipo;
    protected ?string $data = null;
    protected ?string $hora = null;

    public function configureFromSchema(Schema $schema): void
    {
        $this->tipo = $schema->type
            ?? throw new \LogicException(
                sprintf(
                    'Type not resolved for schema "%s".',
                    $schema->path,
                ),
            );
    }

    public function tipo(Type $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function data(?string $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function hora(?string $hora): static
    {
        $this->hora = $hora;

        return $this;
    }

    public function build(): Evento
    {
        return new Evento(
            tipo: $this->tipo,
            data: $this->data,
            hora: $this->hora,
        );
    }
}
