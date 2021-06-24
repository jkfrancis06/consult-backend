<?php


namespace App\Controller;

use App\Entity\Recueil;
use App\Entity\Source;
use App\Form\RecueilType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RapportController extends AbstractController
{



    /**
     * @Route("/rapports/liste", name="liste_rapports")
     */
    public function listeRapports(Request $request){

        return $this->render('rapports/liste.html.twig',[
        ]);

    }

    /**
     * @Route("/rapports/creer", name="creer_rapports")
     */
    public function creerRapports(Request $request){

        return $this->render('rapports/creer.html.twig',[
        ]);

    }

}