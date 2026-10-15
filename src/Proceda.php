<?php

namespace Proceda;

use Proceda\Infrastructure\ProfilePath;
use Proceda\Infrastructure\YamlSource;
use Proceda\Schema\LayoutType;
use Proceda\Schema\LayoutVersion;
use Proceda\Schema\Manager;
use Proceda\Schema\RegistroResolver;
use Proceda\Service\PropertyResolver;
use Proceda\Service\TypeResolver;

final class Proceda
{
    public static function notfis(
        LayoutVersion     $layoutVersion,
        ?iterable         $definitions = null,
        ?RegistroResolver $registroResolver = null,
        ?PropertyResolver $propertyResolver = null,
        ?TypeResolver     $typeResolver = null,
        ?string           $profile = null,
        int               $tabReplacementSize = 1,
    ): Layout {
        return self::create(
            LayoutType::NOTFIS,
            $layoutVersion,
            $definitions,
            $registroResolver,
            $propertyResolver,
            $typeResolver,
            $profile,
            $tabReplacementSize,
        );
    }

    public static function ocoren(
        LayoutVersion     $layoutVersion,
        ?iterable         $definitions = null,
        ?RegistroResolver $registroResolver = null,
        ?PropertyResolver $propertyResolver = null,
        ?TypeResolver     $typeResolver = null,
        ?string           $profile = null,
        int               $tabReplacementSize = 1,
    ): Layout {
        return self::create(
            LayoutType::OCOREN,
            $layoutVersion,
            $definitions,
            $registroResolver,
            $propertyResolver,
            $typeResolver,
            $profile,
            $tabReplacementSize,
        );
    }

    public static function doccob(
        LayoutVersion     $layoutVersion,
        ?iterable         $definitions = null,
        ?RegistroResolver $registroResolver = null,
        ?PropertyResolver $propertyResolver = null,
        ?TypeResolver     $typeResolver = null,
        ?string           $profile = null,
        int               $tabReplacementSize = 1,
    ): Layout {
        return self::create(
            LayoutType::DOCCOB,
            $layoutVersion,
            $definitions,
            $registroResolver,
            $propertyResolver,
            $typeResolver,
            $profile,
            $tabReplacementSize,
        );
    }

    public static function conemb(
        LayoutVersion     $layoutVersion,
        ?iterable         $definitions = null,
        ?RegistroResolver $registroResolver = null,
        ?PropertyResolver $propertyResolver = null,
        ?TypeResolver     $typeResolver = null,
        ?string           $profile = null,
        int               $tabReplacementSize = 1,
    ): Layout {
        return self::create(
            LayoutType::CONEMB,
            $layoutVersion,
            $definitions,
            $registroResolver,
            $propertyResolver,
            $typeResolver,
            $profile,
            $tabReplacementSize,
        );
    }

    public static function create(
        LayoutType        $layoutType,
        LayoutVersion     $layoutVersion,
        ?iterable         $definitions = null,
        ?RegistroResolver $registroResolver = null,
        ?PropertyResolver $propertyResolver = null,
        ?TypeResolver     $typeResolver = null,
        ?string           $profile = null,
        int               $tabReplacementSize = 1,
    ): Layout {
        $charset = new Charset(
            tabReplacementSize: $tabReplacementSize,
        );

        $registroResolver ??= RegistroResolver::yaml(
            $layoutType,
            $layoutVersion,
            $profile,
        );

        $propertyResolver ??= PropertyResolver::yaml(
            $profile
        );
        $typeResolver ??= TypeResolver::yaml(
            $profile
        );

        $definitions ??= YamlSource::fromPath(
            path: sprintf(
                '%s/layout/%s/%s',
                dirname(__DIR__),
                $layoutType->value,
                $layoutVersion->value,
            ),
            overridePath: ProfilePath::for(
                $layoutType,
                $layoutVersion,
                $profile,
            ),
            exclude: ['registros.yaml'],
        )->getDefinitions();

        $schemaManager = new Manager(
            definitions: $definitions,
            layoutVersion: $layoutVersion,
            charset: $charset,
            registroResolver: $registroResolver,
            typeResolver: $typeResolver,
        );

        return new Layout(
            type: $layoutType,
            version: $layoutVersion,
            charset: $charset,
            schemaManager: $schemaManager,
            registroResolver: $registroResolver,
            propertyResolver: $propertyResolver,
            typeResolver: $typeResolver,
            profile: $profile
        );
    }
}