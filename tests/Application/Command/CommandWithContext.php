<?php
/**
 * @copyright Macintoshplus (c) 2022
 * Added by : Macintoshplus at 02/02/2022 23:13
 */

declare(strict_types=1);

namespace JbDevLabs\Tests\SyliusCliContextPlugin\Application\Command;

use JbDevLabs\SyliusCliContextPlugin\Command\CliContextAwareInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CommandWithContext extends Command implements CliContextAwareInterface
{
    protected static $defaultName = 'app:command-with-context';
    private ChannelContextInterface $channelContext;

    public function __construct(ChannelContextInterface $channelContext)
    {
        parent::__construct();
        $this->channelContext = $channelContext;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $channel = $this->channelContext->getChannel();
        $output->writeln('Channel name: ' . $channel->getName());
        return self::SUCCESS;
    }
}
