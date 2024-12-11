<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Création d'un nouvel utilisateur
        $user = new User();

        // Création du formulaire
        $form = $this->createForm(RegistrationType::class, $user);

        // Traitement de la requête
        $form->handleRequest($request);

        // Vérification si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Hachage du mot de passe
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

            // Enregistrer l'utilisateur en base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirection vers la page de connexion après l'enregistrement
            return $this->redirectToRoute('app_login');
        }

        // Rendu de la vue avec le formulaire
        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
