<?php

/*
 * This file is part of the Silverback API Components Bundle Project
 *
 * (c) Daniel West <daniel@silverback.is>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Silverback\ApiComponentsBundle\Repository\Core;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;
use Silverback\ApiComponentsBundle\Entity\Core\AbstractComponent;
use Silverback\ApiComponentsBundle\Entity\Core\AbstractPageData;
use Silverback\ApiComponentsBundle\Entity\Core\Route;

/**
 * @author Daniel West <daniel@silverback.is>
 *
 * @method Route|null find($id, $lockMode = null, $lockVersion = null)
 * @method Route|null findOneBy(array $criteria, array $orderBy = null)
 * @method Route[]    findAll()
 * @method Route[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RouteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Route::class);
    }

    public function findOneByIdOrPath(string $idOrRoute)
    {
        $route = $this->findOneBy(
            [
                'path' => $idOrRoute,
            ]
        );
        if ($route) {
            return $route;
        }

        try {
            $uuid = Uuid::fromString($idOrRoute);
        } catch (InvalidUuidStringException $e) {
            return null;
        }

        return $this->find($uuid);
    }

    /**
     * @return Route[]
     */
    public function findByComponent(AbstractComponent $component): array
    {
        $queryBuilder = $this->createQueryBuilder('route');
        $queryBuilder
            ->leftJoin(
                'route.pageData',
                'pageData',
                Join::WITH,
                $queryBuilder->expr()->eq('route', 'pageData.route')
            )

            ->leftJoin(
                'pageData.page',
                'pageData_page',
                Join::WITH,
                $queryBuilder->expr()->eq('pageData_page', 'pageData.page')
            )
            ->leftJoin(
                'pageData_page.componentCollections',
                'page_data_cc'
            )
            ->leftJoin(
                'page_data_cc.componentPositions',
                'page_data_pos'
            )
            ->leftJoin(
                'page_data_pos.component',
                'page_data_component',
                Join::WITH,
                $queryBuilder->expr()->eq('page_data_pos.component', 'page_data_component')
            )

            ->leftJoin(
                'route.page',
                'page',
                Join::WITH,
                $queryBuilder->expr()->eq('route', 'page.route')
            )
            ->leftJoin(
                'page.componentCollections',
                'page_cc'
            )
            ->leftJoin(
                'page_cc.componentPositions',
                'page_pos'
            )
            ->leftJoin(
                'page_pos.component',
                'page_component',
                Join::WITH,
                $queryBuilder->expr()->eq('page_pos.component', 'page_component')
            )

            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('page_component', ':component'),
                    $queryBuilder->expr()->eq('page_data_component', ':component')
                )
            )
            ->setParameter('component', $component);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return Route[]
     */
    public function findByPageData(AbstractPageData $pageData): array
    {
        $queryBuilder = $this->createQueryBuilder('route');
        $queryBuilder
            ->leftJoin(
                'route.pageData',
                'pageData',
                Join::WITH,
                $queryBuilder->expr()->eq('route', 'pageData.route')
            )
            ->andWhere($queryBuilder->expr()->eq('pageData', ':page_data'))
            ->setParameter('page_data', $pageData);

        return $queryBuilder->getQuery()->getResult();
    }
}
