<?php


namespace App\Controller;

use App\Entity\Recueil;
use App\Entity\Source;
use App\Form\RecueilType;
use App\Form\SourceType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class SourceController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * @Route("/sources", name="sources")
     */
    public function sources(Request $request, PaginatorInterface $paginator){

        $source = new Source();

        $form = $this->createForm(SourceType::class, $source);


        $sources = $this->getDoctrine()->getManager()->getRepository(Source::class)->findAll();

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($source);
            $em->flush();
            $request->getSession()->getFlashBag()->add('sources_add', 'La source a été crée avec succès');
            $sources = $this->getDoctrine()->getManager()->getRepository(Source::class)->findAll();
        }



        $pagination = $paginator->paginate(
            $sources, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );


            return $this->render('source/source.html.twig',[
            'sources' => $pagination,
            'form' => $form->createView(),
            'active' => "sources"
        ]);

    }

}