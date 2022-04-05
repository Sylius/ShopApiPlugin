<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/vendor/sylius-labs/coding-standard/ecs.php');

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
        __DIR__ . '/spec',
        __DIR__ . '/tests'
    ]);
    $parameters->set(Option::SKIP, [
        __DIR__.'/tests/Application',
        'SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff.MissingVariable' => null,
        'PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer' => __DIR__.'/spec/'
    ]);

    $services = $containerConfigurator->services();
    $services->set(HeaderCommentFixer::class)
        ->call('configure', [['comment_type' => 'PHPDoc', 'location' => 'after_open', 'header' => 'This file is part of the Sylius package.

 (c) Paweł Jędrzejewski

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.']]);
};
