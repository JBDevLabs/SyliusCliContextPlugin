<?php

declare(strict_types=1);

namespace JbDevLabs\SyliusCliContextPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class JbDevLabsSyliusCliContextExtension extends Extension
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        $classes = $config['include_command'] ?? [];
        foreach ($classes as $classname) {
            if (class_exists($classname) === false) {
                throw new \InvalidArgumentException('The class name "' . $classname . '" configured on "jb_dev_labs_sylius_cli_context.include_command" key does not exists.');
            }
            if (is_subclass_of($classname, Command::class) === false) {
                throw new \InvalidArgumentException('The class name "' . $classname . '" configured on "jb_dev_labs_sylius_cli_context.include_command" key is not a subclass of ' . Command::class . '.');
            }
        }

        $container->setParameter('jb_dev_labs_sylius_cli_context.config', $config);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));

        $loader->load('services.php');
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }
}
