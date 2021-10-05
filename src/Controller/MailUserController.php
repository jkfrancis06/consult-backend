<?php

namespace App\Controller;

use App\Entity\MailUser;
use App\Form\MailUserType;
use App\Repository\MailUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;


/**
 * @Route("/admin/mail/user")
 */
class MailUserController extends AbstractController
{


    /**
     * @Route("/", name="mail_user_index", methods= {"GET"})
     */
    public function index(MailUserRepository $mailUserRepository): Response
    {
        return $this->render('mail_user/index.html.twig', [
            'mail_users' => $mailUserRepository->findAll(),
            'active' => 'mail_user_index'
        ]);
    }

    /**
     * @Route("/new", name="mail_user_new", methods= {"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $mailUser = new MailUser();
        $form = $this->createForm(MailUserType::class, $mailUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mailUser);
            $entityManager->flush();

            return $this->redirectToRoute('mail_user_index');
        }

        return $this->render('mail_user/new.html.twig', [
            'mail_user' => $mailUser,
            'form' => $form->createView(),
            'active' => 'mail_user_index'
        ]);
    }

    /**
     * @Route("/{id}", name="mail_user_show", methods= {"GET"})
     */
    public function show(MailUser $mailUser): Response
    {
        return $this->render('mail_user/show.html.twig', [
            'mail_user' => $mailUser,
            'active' => 'mail_user_index'
        ]);
    }

    /**
     * @Route("/{id}/edit", name="mail_user_edit", methods= {"GET", "POST"})
     */
    public function edit(Request $request, MailUser $mailUser): Response
    {
        $form = $this->createForm(MailUserType::class, $mailUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mailUser->setLastUpdate(new \DateTime())  ;
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('mail_user_index');
        }

        return $this->render('mail_user/edit.html.twig', [
            'mail_user' => $mailUser,
            'form' => $form->createView(),
            'active' => 'mail_user_index'
        ]);
    }

    /**
     * @Route("/{id}", name="mail_user_delete", methods= {"POST"})
     */
    public function delete(Request $request, MailUser $mailUser): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mailUser->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mailUser);
            $entityManager->flush();
        }

        return $this->redirectToRoute('mail_user_index');
    }
}
