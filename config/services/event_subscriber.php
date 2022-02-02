<?php
/**
 * @copyright Macintoshplus (c) 2022
 * Added by : Macintoshplus at 02/02/2022 22:46
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use JbDevLabs\SyliusCliContextPlugin\EventSubscriber\CommandDefineContextSubscriber;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set(CommandDefineContextSubscriber::class)
        ->autowire()
        ->autoconfigure()
        ->arg('$config', '%jb_dev_labs_sylius_cli_context.config%')
    ;
};
