<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler;

use Sylius\Bundle\ThemeBundle\Translation\Translator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class TranslatorFallbackLocalesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        try {
            $symfonyTranslator = $container->findDefinition('translator.default');
            $syliusTranslator = $container->findDefinition(Translator::class);
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        $methodCalls = array_filter($symfonyTranslator->getMethodCalls(), static function (array $methodCall): bool {
            return 'setFallbackLocales' === $methodCall[0];
        });

        foreach ($methodCalls as $methodCall) {
            $syliusTranslator->addMethodCall($methodCall[0], $methodCall[1]);
        }
    }
}
