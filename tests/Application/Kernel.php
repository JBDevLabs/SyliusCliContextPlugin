<?php

declare(strict_types=1);

namespace JbDevLabs\Tests\SyliusCliContextPlugin\Application;

use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Sylius\Bundle\CoreBundle\SyliusCoreBundle;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/var/log';
    }

    public function registerBundles(): iterable
    {
        foreach ($this->getConfigurationDirectories() as $confDir) {
            $bundlesFile = $confDir . '/bundles.php';
            if (false === is_file($bundlesFile)) {
                continue;
            }
            yield from $this->registerBundlesFromFile($bundlesFile);
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        foreach ($this->getConfigurationDirectories() as $confDir) {
            $bundlesFile = $confDir . '/bundles.php';
            if (false === is_file($bundlesFile)) {
                continue;
            }
            $container->addResource(new FileResource($bundlesFile));
        }

        $container->setParameter('container.dumper.inline_class_loader', true);

        foreach ($this->getConfigurationDirectories() as $confDir) {
            $this->loadContainerConfiguration($loader, $confDir);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        foreach ($this->getConfigurationDirectories() as $confDir) {
            $this->loadRoutesConfiguration($routes, $confDir);
        }
    }

    protected function getContainerBaseClass(): string
    {
        if ($this->isTestEnvironment()) {
            return MockerContainer::class;
        }

        return parent::getContainerBaseClass();
    }

    private function isTestEnvironment(): bool
    {
        return str_starts_with($this->getEnvironment(), 'test');
    }

    private function loadContainerConfiguration(LoaderInterface $loader, string $confDir): void
    {
        $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');
    }

    private function loadRoutesConfiguration(RoutingConfigurator $routes, string $confDir): void
    {
        $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS);
        $routes->import($confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS);
        $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS);
    }

    /**
     * @return iterable<BundleInterface>
     */
    private function registerBundlesFromFile(string $bundlesFile): iterable
    {
        $contents = require $bundlesFile;
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    /**
     * @return iterable<string>
     */
    private function getConfigurationDirectories(): iterable
    {
        yield $this->getProjectDir() . '/config';
        $syliusConfigDir = $this->getProjectDir() . '/config/sylius/' . SyliusCoreBundle::MAJOR_VERSION . '.' . SyliusCoreBundle::MINOR_VERSION;
        if (is_dir($syliusConfigDir)) {
            yield $syliusConfigDir;
        }
        $symfonyConfigDir = $this->getProjectDir() . '/config/symfony/' . BaseKernel::MAJOR_VERSION . '.' . BaseKernel::MINOR_VERSION;
        if (is_dir($symfonyConfigDir)) {
            yield $symfonyConfigDir;
        }
    }
}
