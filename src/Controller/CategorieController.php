<?php


namespace App\Controller;


use App\Entity\Categorie;
use App\Entity\Source;
use App\Form\CategorieType;
use App\Form\SourceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * @Route("/categories", name="categories")
     */
    public function categories(Request $request){

        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie);


        $categories = $this->getDoctrine()->getManager()->getRepository(Categorie::class)->findAll();

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();
            $request->getSession()->getFlashBag()->add('categorie_add', 'La source a été crée avec succès');
            $categories = $this->getDoctrine()->getManager()->getRepository(Categorie::class)->findAll();
        }


        return $this->render('categorie/categorie.html.twig',[
            'categories' => $categories,
            'form' => $form->createView(),
            'active' => "categories"
        ]);

    }

}