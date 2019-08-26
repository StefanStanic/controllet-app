<?php


namespace App\Service;


use App\Entity\Account;
use App\Entity\Bills;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class AdminService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return mixed
     */
    public function get_user_count()
    {
        return $this->em->getRepository(User::class)->get_user_count();
    }

    public function get_all_users()
    {
        return $this->em->getRepository(User::class)->findAll();
    }

    public function change_user_status($user_id, $active)
    {
        $user = $this->em->getRepository(User::class)->find($user_id);

        if($user){
            $user->setActive($active);
            $this->em->persist($user);
            $this->em->flush();

            return $user_id;

        }

        return false;
    }

    /**
     * @return mixed
     */
    public function get_account_count()
    {
        return $this->em->getRepository(Account::class)->get_account_count();
    }

    /**
     * @return mixed
     */
    public function get_bill_count()
    {
        return $this->em->getRepository(Bills::class)->get_bill_count();
    }

    /**
     * @return mixed
     */
    public function get_transaction_count()
    {
        return $this->em->getRepository(Transaction::class)->get_transaction_count();
    }

    public function get_total_cashflow_by_category($type)
    {
        return $this->em->getRepository(Transaction::class)->get_total_cashflow_by_category($type);
    }
}