<?php

declare(strict_types=1);

namespace JbDevLabs\SyliusCliContextPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UnusedVariable, PossiblyUndefinedMethod, MixedMethodCall
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('jb_dev_labs_sylius_cli_context');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $arrayChildrenNode = $rootNode
            ->children();
        $arrayChildrenNode->scalarNode('channel_code')
            ->defaultNull()
            ->info('Sylius channel code of default channel unsed for all Command. If not set, get the first channel.');
        $arrayChildrenNode->arrayNode('include_command')
            ->info('Set command PHP Class namespace to load Sylius Context')
            ->scalarPrototype();
        return $treeBuilder;
    }
}
