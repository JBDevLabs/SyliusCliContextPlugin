<?php
/**
 * @copyright Macintoshplus (c) 2022
 * Added by : Macintoshplus at 02/02/2022 22:21
 */

declare(strict_types=1);

namespace JbDevLabs\SyliusCliContextPlugin\EventSubscriber;


use JbDevLabs\SyliusCliContextPlugin\CliContext\CliChannelContext;
use JbDevLabs\SyliusCliContextPlugin\Command\CliContextAwareInterface;
use JbDevLabs\SyliusCliContextPlugin\Repository\CliChannelProviderInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/** @psalm-suppress UnusedClass */
final class CommandDefineContextSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private readonly CliChannelContext $cliChannelContext,
        private readonly CliChannelProviderInterface $channelProvider,
        private readonly array $config
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleCommandEvent::class => ['setChannel', 10],
        ];
    }

    public function setChannel(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();
        $includeCommand = is_array($this->config['include_command']) ? $this->config['include_command'] : [];
        if (
            $command instanceof CliContextAwareInterface === false &&
            ($command === null || in_array(get_class($command), $includeCommand) === false)
        ) {
            return;
        }

        /** @var string|null $channelCode */
        $channelCode = $this->config['channel_code'] ?? null;
        $channel = $channelCode !== null ? $this->channelProvider->loadChannelFromCode($channelCode) : $this->channelProvider->loadFirstChannel();
        if ($channel === null) {
            return;
        }
        $this->cliChannelContext->setChannel($channel);
    }
}
