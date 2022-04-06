<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'security.login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

     
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
           // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('pages/security/login.html.twig', [
            'last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/deconnexion', name: 'security.logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/inscription', name: 'security.registration', methods: ['GET', 'POST'])]
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            //recupération du mot de pass
        $plaintextPassword = $user->getPassword();
        //hachage du mot de passe
        $hashedPassword = $passwordHasher->hashPassword(
          $user,
          $plaintextPassword
        );
        //remplacement du mot de passe par le mot de passe hacher
        $user->setPassword($hashedPassword);

            $user = $form->getData();
            
            $this->addFlash(
                'success',
                'Votre compte a bien été crée.'
            );

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('security.login');

        }
        return $this->render('pages/security/registration.html.twig', [ 
            'form' => $form->createView()
        ]);
    }
}
