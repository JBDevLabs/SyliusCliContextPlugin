<?php
/**
 * @copyright Macintoshplus (c) 2022
 * Added by : Macintoshplus at 02/02/2022 22:51
 */

declare(strict_types=1);

namespace JbDevLabs\Tests\SyliusCliContextPlugin\Application\Repository;


use JbDevLabs\SyliusCliContextPlugin\Repository\CliChannelProviderInterface;
use JbDevLabs\SyliusCliContextPlugin\Repository\CliChannelProviderTrait;
use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository as BaseChannelRepository;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

class ChannelRepository extends BaseChannelRepository implements CliChannelProviderInterface, ChannelRepositoryInterface
{
    use CliChannelProviderTrait;
}
