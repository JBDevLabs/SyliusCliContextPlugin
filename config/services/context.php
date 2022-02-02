<?php
/**
 * @copyright Macintoshplus (c) 2022
 * Added by : Macintoshplus at 02/02/2022 22:06
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use JbDevLabs\SyliusCliContextPlugin\CliContext\CliChannelContext;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set(CliChannelContext::class)
        ->autowire()
        ->autoconfigure()
        ->tag('sylius.context.channel', ['priority' => 1024]);
};
