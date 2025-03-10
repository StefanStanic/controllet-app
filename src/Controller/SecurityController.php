<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\ActivationKeys;
use App\Entity\User;
use App\Form\UserType;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    private $swift_mailer;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, SecurityService $securityService, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->securityService = $securityService;
        $this->swift_mailer = $mailer;
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {

        $activated = $request->get('activated');

        if(!empty($this->getUser())){
            if($this->getUser()->getId()){
                return $this->redirectToRoute('app_dashboard');
            }
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'activated' => $activated]);
    }

    /**
     * @Route("/registration", name="app_register")
     * @param Request $request
     * @return RedirectResponse|Response
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
        $activationKeys = new ActivationKeys();

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
            $user->setActive(0);

            //set initial role
            $user->setRoles(['ROLE_USER']);

            $this->em->persist($user);
            $this->em->flush();

            //set activation key
            $activationKeys->setHash(sha1(uniqid()));
            $activationKeys->setUser($user);

            $this->em->persist($activationKeys);
            $this->em->flush();

            //base url path
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() ;

            //send email confirmation
            $message = (new \Swift_Message('Confirm your registration on Controllet'))
                ->setFrom('no-reply@controllet.com')
                ->setTo('vts.stefan.stanic@gmail.com')
                ->setBody(
                    $this->renderView(
                        'email/confirm_account.html.twig',
                        [
                            'email' => $user->getEmail(),
                            'activation_link' => $baseurl  . '/activation/' . $user->getEmail() . '/' . $activationKeys->getHash()
                        ]
                    ),
                    'text/html'
                );

            $this->swift_mailer->send($message);

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'registration/registration.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/resetPassword", name="app_reset_password")
     */
    public function reset_password()
    {
        return $this->render('reset_password/reset_password.html.twig');
    }

    /**
     * @Route("/resetPasswordPost")
     * @param Request $request
     * @return Response
     */
    public function reset_password_post(Request $request)
    {
        $email = $request->get('email');

        if(!$email){
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Missing parameters'
                )
            ), Response::HTTP_UNPROCESSABLE_ENTITY);
            return $response;
        }



    }

    /**
     * @Route("/activation/{email}/{act_key}")
     * @param $email
     * @param $act_key
     * @return RedirectResponse|Response
     */
    public function activation($email, $act_key)
    {
        if(empty($email) || empty($act_key))
        {
            return $this->redirectToRoute('app_register');
        }

        if($this->securityService->activation($email, $act_key)->getStatusCode() == 200)
        {
            $redirect = $this->generateUrl('app_login', ['activated' => 'true']);
            return $this->redirect($redirect);
        };
    }

    /**
     * @Route("/logout", name="app_logout")
     * @throws RuntimeException
     */
    public function logout()
    {
        throw new RuntimeException('This should never be called directly.');
    }
}
