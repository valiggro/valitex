<?php

namespace App\Repository;

use App\Entity\Einvoice;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class EinvoiceRepository extends ServiceEntityRepository
{
    public function __construct(
        private CacheInterface $cache,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Einvoice::class);
    }

    public function getSupplierMap(): array
    {
        return $this->cache->get(__METHOD__,  function (ItemInterface $item): array {
            $item->expiresAfter(3600);
            $einvoices = $this->createQueryBuilder('e')
                ->select('DISTINCT e.supplierId, e.supplierName')
                ->orderBy('e.supplierName', 'ASC')
                ->getQuery()->execute();
            $suppliers = [];
            foreach ($einvoices as $einvoice) {
                $suppliers[$einvoice['supplierId']] = $einvoice['supplierName'];
            }
            return $suppliers;
        });
    }

    public function getAll(array $where = []): Paginator
    {
        # query
        $query = $this->createQueryBuilder('e');
        if (@$where['supplierId']) {
            $query->andWhere('e.supplierId = :supplierId')->setParameter('supplierId', intVal($where['supplierId']));
        }
        if (@$where['issueDate']) {
            $query->andWhere('e.issueDate = :issueDate')->setParameter('issueDate', $where['issueDate']);
        }
        if (@$where['noteNumber']) {
            if ('yes' == $where['noteNumber']) {
                $query->andWhere('e.noteNumber > 0');
            } elseif ('no' == $where['noteNumber']) {
                $query->andWhere('e.noteNumber = 0 OR e.noteNumber IS NULL');
            }
        }
        $query->orderBy('e.id', 'DESC');
        # paginator
        $paginator = new Paginator($query);
        if (@$where['page'] && is_numeric($where['page']) && $where['page'] > 0) {
            $page = intVal($where['page']);
        } else {
            $page = 1;
        }
        $paginator->getQuery()
            ->setFirstResult(10 * max(0, ($page - 1)))
            ->setMaxResults(10);
        return $paginator;
    }
}
