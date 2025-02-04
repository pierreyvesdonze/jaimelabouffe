<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
        )
    {
    }

    #[Route('/enregistrement', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();

        if($this->getUser()) {
            $this->addFlash('error', 'Vous êtes déjà enregistré et connecté.');
            return $this->redirectToRoute('home');
        }
        
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            try {
                $this->em->persist($user);
                $this->em->flush();
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                // Capturer l'exception de violation de contrainte d'unicité
                // Afficher un message d'erreur approprié à l'utilisateur
                $this->addFlash('error', 'Un compte existe déjà avec cet email ');
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView()
                ]);
            }

            $this->addFlash('success', 'Votre compte a bien été créé !');

            return $this->redirectToRoute('login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/supprimer/compte', name: 'delete_account')]
    public function deleteAccount(
        SessionInterface $session,
        TokenStorageInterface $tokenStorage,
    ): Response
    {
        $user = $this->getUser();
        if(!$user) {
            return $this->redirectToRoute('login');
        }

        $tokenStorage->setToken(null);
        $session->invalidate();
        
        $this->em->remove($user);
        $this->em->flush();

        $this->addFlash('success', 'Votre compte a bien été supprimé !');

        return $this->redirectToRoute('home');
    }
}
