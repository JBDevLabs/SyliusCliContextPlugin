<?php
/**
 * @copyright Macintoshplus (c) 2022
 * Added by : Macintoshplus at 03/02/2022 00:03
 */

declare(strict_types=1);

namespace JbDevLabs\Tests\SyliusCliContextPlugin\Application\Command;


use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class OtherCommand extends Command
{
    protected static $defaultName = 'app:other-command';

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
