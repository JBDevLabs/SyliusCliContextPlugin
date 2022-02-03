<?php
/**
 * @copyright Macintoshplus (c) 2022
 * Added by : Macintoshplus at 03/02/2022 21:14
 */

declare(strict_types=1);

namespace JbDevLabs\Tests\SyliusCliContextPlugin\Application\Command;


use JbDevLabs\SyliusCliContextPlugin\CliContext\CliChannelContext;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class LoadChannelManuallyCommand extends Command
{
    protected static $defaultName = 'app:change-channel';
    private CliChannelContext $channelContext;
    private ChannelRepositoryInterface $channelRepository;

    public function __construct(CliChannelContext $channelContext, ChannelRepositoryInterface $channelRepository)
    {
        parent::__construct();
        $this->channelContext = $channelContext;
        $this->channelRepository = $channelRepository;
    }

    protected function configure()
    {
        $this->addOption('channel', 'c', InputOption::VALUE_REQUIRED, 'Channel code to use durring the running command',
            null);
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $channel = $input->getOption('channel');

        if ($channel !== null) {
            $channelObj = $this->channelRepository->findOneByCode($channel);
            if ($channelObj === null) {
                throw new \Exception('Channel with code "' . $channel . '" not found');
            }
            $channel = $channelObj;
        }

        $channels = $channel === null ? $this->channelRepository->findAll() : [$channel];

        foreach ($channels as $channel) {
            $this->channelContext->setChannel($channel);
            $output->writeln('Current channel: "' . $channel->getName() . '" - ' . $channel->getCode());
            //Your code
        }
        return self::SUCCESS;
    }
}
