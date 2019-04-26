<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\User;
use App\Form\UserType;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $em;
    private $passwordEncoder;
    private $securityService;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, SecurityService $securityService)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->securityService = $securityService;
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if(!empty($this->getUser())){
            if($this->getUser()->getId()){
                return $this->redirectToRoute('app_dashboard');
            }
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/registration", name="app_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function register(Request $request)
    {
        if(!empty($this->getUser())){
            if($this->getUser()->getId()){
                return $this->redirectToRoute('app_dashboard');
            }
        }

        $user = new User();
        $account = new Account();

        $form = $this->createForm(UserType::class, $user);

        //handle form submititon
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            //encode and set password
            $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            //add creation date
            $date = date('Y-m-d H:i:s');
            $user->setCreatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $date));
            $user->setUpdatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $date));

            //setting activation key
            $user->setActKey(sha1(uniqid()));
            $user->setActive(0);

            //set initial role
            $user->setRoles(['ROLE_USER']);

            $this->em->persist($user);
            $this->em->flush();

            //setup default account
            $account
                ->setUser($user)
                ->setAccountName('Cash Account')
                ->setAccountType('cash')
                ->setAccountBalance(0)
                ->setActive(1)
                ->setLastUsedDate(\DateTime::createFromFormat('Y-m-d H:i:s', $date))
                ;

            $this->em->persist($account);
            $this->em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'registration/registration.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/activation/{email}/{act_key}")
     * @param $email
     * @param $act_key
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function activation($email, $act_key)
    {
        if(empty($email) || empty($act_key))
        {
            return $this->redirectToRoute('app_register');
        }

        if($this->securityService->activation($email, $act_key)->getStatusCode() == 200)
        {
            return $this->redirectToRoute('app_login');
        };
    }

    /**
     * @Route("/logout", name="app_logout")
     * @throws \RuntimeException
     */
    public function logout()
    {
        throw new \RuntimeException('This should never be called directly.');
    }
}
