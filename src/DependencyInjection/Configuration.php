<?php

declare(strict_types=1);

namespace JbDevLabs\SyliusCliContextPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('jb_dev_labs_sylius_cli_context');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('channel_code')->defaultNull()->info('Sylius channel code of default channel unsed for all Command. If not set, get the first channel.')->end()
                ->arrayNode('include_command')->info('Set command PHP Class namespace to load Sylius Context')->scalarPrototype()->end()->end()
            ->end();
        return $treeBuilder;
    }
}
