<?php

namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $user_id
     * @param $first_name
     * @param $last_name
     * @param $date_of_birth
     * @param $company
     * @param $city
     * @param $country
     * @return bool
     */
    public function update_profile_by_user_id($user_id, $first_name, $last_name, $date_of_birth, $company, $city, $country)
    {
        $user = $this->em->getRepository(User::class)->find($user_id);
        $date = date('Y-m-d H:i:s');

        $date_of_birth = date('Y-m-d', strtotime($date_of_birth));

        if($user){
            $user
                ->setFirstName($first_name)
                ->setLastName($last_name)
                ->setDateOfBirth(\DateTime::createFromFormat('Y-m-d', $date_of_birth))
                ->setCompany($company)
                ->setCity($city)
                ->setCountry($country)
                ->setUpdatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $date))
            ;

            $this->em->flush();
            return $user_id;
        }else{
            return false;
        }
    }
}