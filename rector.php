<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->skip([
        __DIR__ . '/var',
        __DIR__ . '/vendor',
        __DIR__ . '/migrations',
    ]);

    // PHP 8.2 features
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
        SetList::PRIVATIZATION,
        SetList::NAMING,
        SetList::INSTANCEOF,
        SetList::PHP_82,
    ]);

    // Import classes
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);

    // Remove unused imports
    $rectorConfig->removeUnusedImports();

    // PHPStan configuration
    $rectorConfig->phpstanConfig(__DIR__ . '/phpstan.dist.neon');
};
