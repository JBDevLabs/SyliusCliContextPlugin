<?php
/**
 * @copyright Macintoshplus (c) 2022
 * Added by : Macintoshplus at 02/02/2022 22:01
 */

declare(strict_types=1);

namespace JbDevLabs\SyliusCliContextPlugin\CliContext;


use Sylius\Behat\Service\Setter\ChannelContextSetterInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;

final class CliChannelContext implements ChannelContextInterface, ChannelContextSetterInterface
{
    private ?ChannelInterface $channel = null;
    /**
     * @inheritDoc
     */
    public function getChannel(): ChannelInterface
    {
        if ($this->channel === null || php_sapi_name() !== 'cli') {
            throw new ChannelNotFoundException();
        }
        return $this->channel;
    }

    public function setChannel(ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }
}
