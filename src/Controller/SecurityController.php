<?php

namespace App\Controller;

use App\Form\ResetPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @var Security
     */
    private $security;

    private $passwordEncoder;


    public function __construct(Security $security, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->security = $security;
        $this->passwordEncoder = $passwordEncoder;

    }

    /**
     * @Route("/login", name="app_login")
     */
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

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/account/settings", name="app_settings")
     */

    public function settings(\Symfony\Component\HttpFoundation\Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $user = $this->security->getUser();


        $form = $this->createForm(ResetPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $oldPassword = $form->get('oldPassword')->getData();

            // Si l'ancien mot de passe est bon

            if ($this->passwordEncoder->isPasswordValid($user, $oldPassword)) {

                $newEncodedPassword = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());

                $user->setPassword($newEncodedPassword);



                $em->persist($user);

                $em->flush();

                $this->addFlash('notice', 'Votre mot de passe à bien été changé !');

                return $this->redirectToRoute('app_logout');

            } else {

                $form->addError(new FormError('Ancien mot de passe incorrect'));

            }

        }


        return $this->render('security/settings.html.twig',[
            'active' => 'settings',
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
