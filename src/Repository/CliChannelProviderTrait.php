<?php
/**
 * @copyright Macintoshplus (c) 2022
 * Added by : Macintoshplus at 02/02/2022 22:33
 */

declare(strict_types=1);

namespace JbDevLabs\SyliusCliContextPlugin\Repository;

use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;

trait CliChannelProviderTrait
{
    public function loadChannelFromCode(string $code): ?ChannelInterface
    {
        if ($this instanceof EntityRepository === false) {
            throw new \LogicException(__TRAIT__ . ' must be used only on ' . EntityRepository::class . ' class.');
        }
        $qb = $this->createQueryBuilder('o');
        $qb->andWhere('o.code = :code')
            ->setParameter('code', $code);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function loadFirstChannel(): ?ChannelInterface
    {
        if ($this instanceof EntityRepository === false) {
            throw new \LogicException(__TRAIT__ . ' must be used only on ' . EntityRepository::class . ' class.');
        }
        $qb = $this->createQueryBuilder('o');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
