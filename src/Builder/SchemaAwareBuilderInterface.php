<?php

namespace Proceda\Builder;

use Proceda\Schema;

interface SchemaAwareBuilderInterface
{
    public function configureFromSchema(Schema $schema): void;
}
