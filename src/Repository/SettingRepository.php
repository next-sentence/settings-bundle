<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class SettingRepository extends EntityRepository implements SettingRepositoryInterface
{
    /**
     * @param string $vendor
     * @param string $plugin
     * @param string|null $localeCode
     *
     * @return array
     */
    public function findAllByLocale(string $vendor, string $plugin, ?string $localeCode = null): array
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->andWhere('o.vendor = :vendor')
            ->andWhere('o.plugin = :plugin')
            ->setParameter('vendor', $vendor)
            ->setParameter('plugin', $plugin)
        ;

        // Manage Locale
        if (null === $localeCode) {
            $queryBuilder->andWhere('o.localeCode IS NULL');
        } else {
            $queryBuilder
                ->andWhere('o.localeCode = :localeCode')
                ->setParameter('localeCode', $localeCode)
            ;
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $vendor
     * @param string $plugin
     * @param string|null $localeCode
     *
     * @return array
     */
    public function findAllByLocaleWithDefault(string $vendor, string $plugin, ?string $localeCode = null): array
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->andWhere('o.vendor = :vendor')
            ->andWhere('o.plugin = :plugin')
            ->setParameter('vendor', $vendor)
            ->setParameter('plugin', $plugin)
        ;

        // Manage Locale
        if (null === $localeCode) {
            $queryBuilder->andWhere('o.localeCode IS NULL');
        } else {
            $queryBuilder
                ->andWhere('o.localeCode = :localeCode OR o.localeCode IS NULL')
                ->setParameter('localeCode', $localeCode)
            ;
        }

        // The order is primordial! Default first in the results, correct value last
        $queryBuilder->addSelect(<<<EXPR
            CASE WHEN
                o.localeCode IS NULL
                THEN
                    4
                ELSE
                    3
            END AS value_position
        EXPR);
        $queryBuilder->addOrderBy('value_position', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }
}
