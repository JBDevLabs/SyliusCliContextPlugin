<?php
/**
 * @copyright Macintoshplus (c) 2022
 * Added by : Macintoshplus at 02/02/2022 23:13
 */

declare(strict_types=1);

namespace JbDevLabs\Tests\SyliusCliContextPlugin\Application\Command;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:command-without-context')]
final class CommandWithoutContext extends Command
{
    protected static $defaultName = 'app:command-without-context';

    public function __construct(private readonly ChannelContextInterface $channelContext)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $channel = $this->channelContext->getChannel();
            $output->writeln('Channel name: '.$channel->getName());
        } catch (ChannelNotFoundException $exception) {
            $output->writeln('<error> Error: ' . $exception->getMessage() . ' </error>');
        }
        return self::SUCCESS;
    }

}
