<?php

namespace App\Repository;

use App\Entity\EinvoiceItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EinvoiceItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EinvoiceItem::class);
    }

    public function findOneByPrice(string $supplierId, float $price): ?EinvoiceItem
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.supplierId = :supplierId')->setParameter('supplierId', $supplierId)
            ->andWhere('e.price = :price')->setParameter('price', $price)
            ->andWhere('e.price != e.sellPrice')
            ->orderBy('e.sellPrice', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    public function findOneByNameMatch(string $supplierId, float $price, string $nameMatch): ?EinvoiceItem
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.supplierId = :supplierId')->setParameter('supplierId', $supplierId)
            ->andWhere('e.price = :price')->setParameter('price', $price)
            ->andWhere('e.price != e.sellPrice')
            ->andWhere(':nameMatch LIKE CONCAT(\'%\', e.nameMatch, \'%\')')->setParameter('nameMatch', $nameMatch)
            ->orderBy('e.sellPrice', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }
}
