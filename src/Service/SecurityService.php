<?php

namespace App\Service;


use App\Entity\ActivationKeys;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class SecurityService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function activation(string $email, string $act_key)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        $activation_key = $this->em->getRepository(ActivationKeys::class)->find($user);

        if (!$activation_key) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_NOT_FOUND);

            return $response;
        } else {

            if ($activation_key->getHash() != $act_key) {
                $response = new Response();

                $response->setStatusCode(Response::HTTP_FORBIDDEN);

                return $response;
            }

            if (!$user->getActive()) {
                //set the user to be active
                $user->setActive(1);
                $this->em->persist($user);
                $this->em->flush();

                $response = new Response();

                $response->setStatusCode(Response::HTTP_OK);

                return $response;
            }
        }
    }
}