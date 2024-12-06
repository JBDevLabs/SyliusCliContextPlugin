<?php
/**
 * @copyright Macintoshplus (c) 2022
 * Added by : Macintoshplus at 02/02/2022 23:13
 */

declare(strict_types=1);

namespace JbDevLabs\Tests\SyliusCliContextPlugin\Application\Command;

use JbDevLabs\SyliusCliContextPlugin\Command\CliContextAwareInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:command-with-context')]
final class CommandWithContext extends Command implements CliContextAwareInterface
{
    protected static $defaultName = 'app:command-with-context';

    public function __construct(private readonly ChannelContextInterface $channelContext)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $channel = $this->channelContext->getChannel();
        $output->writeln('Channel name: ' . $channel->getName());
        return self::SUCCESS;
    }
}
