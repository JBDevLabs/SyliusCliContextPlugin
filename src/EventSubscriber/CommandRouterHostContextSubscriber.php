<?php
/**
 * @copyright Macintoshplus (c) 2022
 * Added by : Macintoshplus at 03/02/2022 12:53
 */

declare(strict_types=1);

namespace JbDevLabs\SyliusCliContextPlugin\EventSubscriber;


use JbDevLabs\SyliusCliContextPlugin\CliContext\CliChannelContext;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Work only if the CliChannelContext has a channel defined.
 * @psalm-suppress UnusedClass
 */
final class CommandRouterHostContextSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private readonly CliChannelContext $channelContext,
        private readonly RouterInterface $router
    )
    {
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleCommandEvent::class => ['setHostFromChannel', 9],
        ];
    }

    public function setHostFromChannel(): void
    {
        try {
            $channel = $this->channelContext->getChannel();
            $this->router->getContext()->setHost($channel->getHostname() ?? 'localhost');
        } catch (ChannelNotFoundException $exception) {
            // Do nothing
        }
    }
}
