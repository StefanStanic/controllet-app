<?php

namespace App\Service;


use App\Entity\Account;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class DashboardService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function get_active_wallets_by_user_id($user_id)
    {
        $user = $this->em->getRepository(User::class)->find($user_id);
        return $user->getAccount();
    }

    public function get_active_categories()
    {
        $categories = $this->em->getRepository(Category::class)->findBy(
            ['active' => 1]
        );

        return $categories;
    }

    public function get_transactions_by_user_id($user_id)
    {
        $user = $this->em->getRepository(User::class)->find($user_id);
        return $user->getTransaction();
    }


    public function update_account($account_name, $account_balance, $account_id, $user_id)
    {
        $account = $this->em->getRepository(Account::class)->find($account_id);
        $date = date('Y-m-d H:i:s');

        if($account){
            $account
                ->setAccountName($account_name)
                ->setAccountBalance($account_balance)
                ->setLastUsedDate(\DateTime::createFromFormat('Y-m-d H:i:s', $date));
            $this->em->flush();

            return $account_id;
        }
    }
    public function delete_account($account_id)
    {
        $account = $this->em->getRepository(Account::class)->find($account_id);

        if ($account) {

            //set wallet to active 0 for later restoration capabilities
            $account->setActive(0);
            $this->em->flush();

            return $account_id;
        }
    }

}