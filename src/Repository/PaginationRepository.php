<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class PaginationRepository extends EntityRepository
{
    /**
     * Returns a Pagerfanta object encapsulating the matching paginated activities.
     *
     * @param int $page
     * @param int $items
     *
     * @return Pagerfanta
     */
    public function pagePublic($page = 1, $items = 10)
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($this->queryPublic()));
        $paginator->setMaxPerPage($items);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    /**
     * @return QueryBuilder
     */
    public function queryPublic()
    {
        $qb = $this->createQueryBuilder('cn')
            ->where('cn.public = true')
            ->orderBy('cn.createdAt', 'desc');

        return $qb;
    }

    /**
     * Returns a Pagerfanta object encapsulating the matching paginated activities.
     *
     * @param int $page
     * @param int $items
     *
     * @return Pagerfanta
     */
    public function pageAll($page = 1, $items = 10)
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($this->queryAll()));
        $paginator->setMaxPerPage($items);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    /**
     * @return QueryBuilder
     */
    public function queryAll()
    {
        $qb = $this->createQueryBuilder('cn')
            ->orderBy('cn.createdAt', 'desc');

        return $qb;
    }

    /**
     * Gets the latest community news (only visible to the public) if any.
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return mixed
     */
    public function getLatest()
    {
        return $this->createQueryBuilder('cn')
            ->where('cn.public = :public')
            ->setParameter(':public', true)
            ->orderBy('cn.createdAt', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
