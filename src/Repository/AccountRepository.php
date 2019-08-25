<?php

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function get_total_balance($user_id)
    {

        $db = $this->createQueryBuilder('account')
            ->select('SUM(account.account_balance) as total_balance')
            ->where('account.user = :user_id')
            ->setParameter('user_id', $user_id)
            ->andWhere('account.active = 1');

        return $db
            ->getQuery()
            ->execute();
    }
}
