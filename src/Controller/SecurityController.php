<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/magic', name: 'magic')]
    public function magic(UserRepository $userRepository,
     LoginLinkHandlerInterface $loginLinkHandlerInterface,
     MailerInterface $mailer){

       $users = $userRepository->findAll();
       foreach ($users as $user) {
          $my_login_link = $loginLinkHandlerInterface->createLoginLink( $user );

          $email = new Email();
          $email->from("resetpassword@test.com")
                ->to($user->getEmail())
                ->subject("Link for Rest your password !!")
                ->html( "<h2>Your magic link is :</h2> <a href='".$my_login_link->getUrl()."'> reset password . </a>")
            ;
          $mailer->send( $email );
       }
       return new Response("well done !", 200);
    }
}
