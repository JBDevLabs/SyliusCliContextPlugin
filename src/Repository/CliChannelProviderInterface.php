<?php
/**
 * @copyright Macintoshplus (c) 2022
 * Added by : Macintoshplus at 02/02/2022 22:32
 */

declare(strict_types=1);

namespace JbDevLabs\SyliusCliContextPlugin\Repository;

use Sylius\Component\Core\Model\ChannelInterface;

interface CliChannelProviderInterface
{
    public function loadChannelFromCode(string $code): ?ChannelInterface;

    public function loadFirstChannel(): ?ChannelInterface;
}
