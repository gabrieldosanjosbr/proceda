<?php

namespace Proceda\Schema;

use Proceda\Infrastructure\YamlSource;

final class RegistroResolver
{
    /** @var array<string, Registro> */
    private array $registros = [];

    public static function yaml(
        LayoutType $type,
        LayoutVersion $version,
        ?string $profile = null,
    ): self {
        return self::fromDefinitions(
            YamlSource::file(
                type: $type,
                version: $version,
                filename: 'registros.yaml',
                profile: $profile,
            )->getDefinitions(),
        );
    }

    /**
     * @param iterable<array<int, array<string, mixed>>> $definitions
     */
    public static function fromDefinitions(
        iterable $definitions,
    ): self {
        $resolver = new self();

        foreach ($definitions as $registros) {
            foreach ($registros as $data) {
                $registro = new Registro(
                    identificacao: (string) $data['identificacao'],
                    length: (int) $data['length'],
                    parent: isset($data['parent'])
                        ? (string) $data['parent']
                        : null,
                    useParentAsContext: (bool) (
                        $data['useParentAsContext'] ?? false
                    ),
                    fallback: isset($data['fallback'])
                        ? (string) $data['fallback']
                        : null,
                    deferred: $data['deferred'] ?? false,
                    build: $data['build'] ?? true,
                    required: $data['required'],
                );

                $resolver->registros[$registro->identificacao] = $registro;
            }
        }

        return $resolver->resolveExtensions();
    }

    private function resolveExtensions(): self
    {
        foreach ($this->registros as $registro) {
            if (!$registro->useParentAsContext) {
                continue;
            }

            if ($registro->parent === null) {
                throw new \LogicException(
                    sprintf(
                        'Registro "%s" utiliza o contexto do pai, '
                        . 'mas não possui parent definido.',
                        $registro->identificacao,
                    ),
                );
            }

            $parent = $this->get($registro->parent);

            $parent->addExtension($registro);
        }

        return $this;
    }

    public function get(string $identificacao): Registro
    {
        return $this->registros[$identificacao]
            ?? throw new \LogicException(
                sprintf(
                    'Registro "%s" não foi encontrado no layout informado.',
                    $identificacao,
                ),
            );
    }

    public function getRegistros(): array
    {
        return $this->registros;
    }
}