<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>

<h1 align="center">Cli Context Plugin</h1>

<p align="center">This plugin provide a default channel context for your Symfony Command.</p>

When Sylius load a resource implements `Sylius\Component\Resource\Model\TranslatableInterface`, a Doctrine`postLoad` event listener defines the default local and the current locale into the loaded object.
The current local is determined from the current HTTP request and the `ChannelContext`.

In HTTP context, the current channel into `ChannelContext` is defined from hostname.

In Console context (cli), we have no HTTP request and the `ChannelContext` is empty. When you load a translatable resource, the channel context will load the first channel after running the `findAll` function on `ChannelRepository`.

But the `ChannelContext` will execute the `findAll` function for each resource loaded. The more you load resource more your command consume more memory and use more CPU resources.

Know issues: [Performance issue in cli commands: channel db request after each translatable entity postLoad event](https://github.com/Sylius/Sylius/issues/9296) and [Multiple channels with php-cli is not possible](https://github.com/Sylius/Sylius/issues/9987)

This Sylius plugin allows you to load the channel into the `ChannelContext` on `ConsoleCommandEvent` event. The `findAll` function on `ChannelRepository` will never be executed by `ChannelContext` and you preserve your performances.

## Installation

Run the command `composer require jbdevlabs/sylius-cli-context-plugin dev-main`

## Extends the Sylius Channel Repository

> Note: If you have already extended the ChannelRepository, implement the interface `JbDevLabs\SyliusCliContextPlugin\Repository\CliChannelProviderInterface`
> and use the trait `JbDevLabs\SyliusCliContextPlugin\Repository\CliChannelProviderTrait` for interface implementation.
> Goto the alias configuration step.

### Implement and use trait

Make a new PHP class `ChannelRepository` in `src/Repository` like this:

```php
<?php

declare(strict_types=1);

namespace App\Repository;


use JbDevLabs\SyliusCliContextPlugin\Repository\CliChannelProviderInterface;
use JbDevLabs\SyliusCliContextPlugin\Repository\CliChannelProviderTrait;
use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository as BaseChannelRepository;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

class ChannelRepository extends BaseChannelRepository implements CliChannelProviderInterface, ChannelRepositoryInterface
{
    use CliChannelProviderTrait;
}
```

### Configure the new repository

Declare this repository on `config/packages/_sylius.yaml` file like this:

```yaml
sylius_channel:
    resources:
        channel:
            classes:
                repository: App\Repository\ChannelRepository
```

### Fix the interface alias

Declare the alias for `JbDevLabs\SyliusCliContextPlugin\Repository\CliChannelProviderInterface` to the channel repository.

```yaml
services:
  JbDevLabs\SyliusCliContextPlugin\Repository\CliChannelProviderInterface: '@sylius.repository.channel'
```

## Configuration

Add a file `jb_dev_labs_sylius_cli_context.yaml` into `config/packages` and set your configuration.

```yaml
jb_dev_labs_sylius_cli_context:

    # Sylius channel code of default channel unsed for all Command. If not set, get the first channel.
    channel_code:         null

    # Set command PHP Class namespace to load Sylius Context
    include_command:      []
```

## Usage on your command

By default, no Channel context is loaded.

To automaticaly load channel context for your command, implement the interface `JbDevLabs\SyliusCliContextPlugin\Command\CliContextAwareInterface`
on your command.

Example:

```php
<?php

declare(strict_types=1);

namespace App\Command;

use JbDevLabs\SyliusCliContextPlugin\Command\CliContextAwareInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CommandWithContext extends Command implements CliContextAwareInterface
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Your code
        return self::SUCCESS;
    }
}
```

## Usage on bundle command

You want load the Sylius Channel context when command provided by another plugin is executed ?

Add the command class namespace on your configuration to enable the Context initialisation.

```yaml
jb_dev_labs_sylius_cli_context:
    # Set command PHP Class namespace to load Sylius Context
    include_command:
        - Vendor\SyliusVendorPlugin\Command\StufCommand

```

## Contributor Installation (docker)

1. Clone this project

2. From the plugin skeleton root directory, run the following commands:

```bash
$ chmod -Rf 777 tests/Application/var
$ docker-compose up -d
$ docker-compose exec php php -d memory_limit=-1 /usr/bin/composer install
$ docker-compose exec nodejs yarn --cwd tests/Application install
$ docker-compose exec php tests/Application/bin/console doctrine:database:create --if-not-exists -vvv
$ docker-compose exec php tests/Application/bin/console doctrine:schema:create -vvv
$ docker-compose exec php tests/Application/bin/console assets:install tests/Application/public -vvv
$ docker-compose exec nodejs yarn --cwd tests/Application build
$ docker-compose exec php tests/Application/bin/console cache:warmup -vvv
$ docker-compose exec php tests/Application/bin/console sylius:fixtures:load -n
```
 
### Quality tools

```bash
$ docker-compose exec php composer validate --ansi --strict
$ docker-compose exec php vendor/bin/phpstan analyse -c phpstan.neon -l max src/
$ docker-compose exec php vendor/bin/psalm
$ docker-compose exec php vendor/bin/phpspec run --ansi -f progress --no-interaction
$ docker-compose exec php vendor/bin/phpunit --colors=always
$ docker-compose exec php vendor/bin/behat --profile docker --colors --strict -vvv --no-interaction
``` 
 __ProTip__ use `Makefile` ;)

## Contributor quickstart Installation (legacy)

1. Clone this project

2. From the plugin skeleton root directory, run the following commands:

    ```bash
    $ (cd tests/Application && yarn install)
    $ (cd tests/Application && yarn build)
    $ (cd tests/Application && APP_ENV=test bin/console assets:install public)
    
    $ (cd tests/Application && APP_ENV=test bin/console doctrine:database:create)
    $ (cd tests/Application && APP_ENV=test bin/console doctrine:schema:create)
    ```

To be able to setup a plugin's database, remember to configure you database credentials in `tests/Application/.env` and `tests/Application/.env.test`.

## Usage

### Running plugin tests

  - PHPUnit

    ```bash
    vendor/bin/phpunit
    ```

  - PHPSpec

    ```bash
    vendor/bin/phpspec run
    ```

  - Behat (non-JS scenarios)

    ```bash
    vendor/bin/behat --strict --tags="~@javascript"
    ```

  - Behat (JS scenarios)
 
    1. [Install Symfony CLI command](https://symfony.com/download).
 
    2. Start Headless Chrome:
    
      ```bash
      google-chrome-stable --enable-automation --disable-background-networking --no-default-browser-check --no-first-run --disable-popup-blocking --disable-default-apps --allow-insecure-localhost --disable-translate --disable-extensions --no-sandbox --enable-features=Metal --headless --remote-debugging-port=9222 --window-size=2880,1800 --proxy-server='direct://' --proxy-bypass-list='*' http://127.0.0.1
      ```
    
    3. Install SSL certificates (only once needed) and run test application's webserver on `127.0.0.1:8080`:
    
      ```bash
      symfony server:ca:install
      APP_ENV=test symfony server:start --port=8080 --dir=tests/Application/public --daemon
      ```
    
    4. Run Behat:
    
      ```bash
      vendor/bin/behat --strict --tags="@javascript"
      ```
    
  - Static Analysis
  
    - Psalm
    
      ```bash
      vendor/bin/psalm
      ```
      
    - PHPStan
    
      ```bash
      vendor/bin/phpstan analyse -c phpstan.neon -l max src/  
      ```

  - Coding Standard
  
    ```bash
    vendor/bin/ecs check src
    ```

### Opening Sylius with your plugin

- Using `test` environment:

    ```bash
    (cd tests/Application && APP_ENV=test bin/console sylius:fixtures:load)
    (cd tests/Application && APP_ENV=test bin/console server:run -d public)
    ```
    
- Using `dev` environment:

    ```bash
    (cd tests/Application && APP_ENV=dev bin/console sylius:fixtures:load)
    (cd tests/Application && APP_ENV=dev bin/console server:run -d public)
    ```
