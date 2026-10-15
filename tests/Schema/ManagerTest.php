<?php

namespace Proceda\Tests\Schema;

use PHPUnit\Framework\TestCase;
use Proceda\Proceda;
use Proceda\Schema\LayoutVersion;

final class ManagerTest extends TestCase
{
    public function testGetGraph(): void
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        $manager = $layout->getSchemaManager();
        $root = $manager->getRootSchema();

        $graph = $manager->getGraph();

        self::assertArrayHasKey('nodes', $graph);
        self::assertArrayHasKey('edges', $graph);

        self::assertSame(
            [
                'id' => $root->path,
                'label' => $root->label,
            ],
            $graph['nodes'][$root->path],
        );

        self::assertNotEmpty($graph['edges']);

        foreach ($root->children as $child) {
            self::assertSame(
                [
                    'from' => $root->path,
                    'to' => $child->path,
                    'label' => $child->label,
                ],
                self::findEdge(
                    $graph['edges'],
                    $root->path,
                    $child->path,
                ),
            );
        }
    }

    private static function findEdge(
        array $edges,
        string $from,
        string $to,
    ): ?array {
        foreach ($edges as $edge) {
            if (
                $edge['from'] === $from
                && $edge['to'] === $to
            ) {
                return $edge;
            }
        }

        return null;
    }
}