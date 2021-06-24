<?php


namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Recueil;
use App\Entity\Source;
use App\Form\RecueilType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class RecueilController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function privatePage() : Response
    {
        $user = $this->security->getUser(); // null or UserInterface, if logged in
        // ... do whatever you want with $user
    }

    /**
     * @Route("/receuils/mes-recueils", name="receuils")
     */
    public function receuils(Request $request, PaginatorInterface $paginator){


        $params = $request->query->all();

        $startDate = "";
        $endDate = "";
        $search_sources = [];
        $search_categories = [];
        $daterange =  $request->query->get('daterange') ;


        if ( $request->query->get('daterange') != ""){
            $var = explode('-', $request->query->get('daterange'));
            $startDate = trim($var[0]);
            $endDate = trim($var[1]);
        }

        if ($request->query->get('sources') != null){
            foreach ($request->query->get('sources') as $item){
                array_push($search_sources,intval($item));
            }
        }

        if ($request->query->get('categories') != null){
            foreach ($request->query->get('categories') as $item){
                array_push($search_categories,intval($item));
            }
        }


        $user = $this->security->getUser();


        if ($request->query->get('search')){
            $receuils = $this->getDoctrine()->getManager()->getRepository(Recueil::class)->findByQuery(
                $user,
                $startDate,
                $endDate,
                $search_sources,
                $search_categories
            );
        }else{
            $receuils = $this->getDoctrine()->getManager()->getRepository(Recueil::class)->findBy([
                    'utilisateur' => $user,
                ],
                ['createdAt' => 'DESC']
            );
        }







        $categories = $this->getDoctrine()->getManager()->getRepository(Categorie::class)->findAll();
        $sources = $this->getDoctrine()->getManager()->getRepository(Source::class)->findAll();


        $paginated_receuils = $paginator->paginate(
            $receuils,
            $request->query->getInt('page', 1),
            10
        );


        return $this->render('recueil/recueil.html.twig',[
            'receuils' => $paginated_receuils,
            'active' => "receuils",
            'categories' => $categories,
            'sources' => $sources,
            'search_sources' =>$search_sources,
            'search_categories' => $search_categories,
            'daterange' => $daterange
        ]);

    }


    /**
     * @Route("/receuils/creer", name="creer_receuils")
     */
    public function creerReceuils(Request $request){

        $receuil = new Recueil();

        $form = $this->createForm(RecueilType::class, $receuil);

        $user = $this->security->getUser();



        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $receuil->setUtilisateur($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($receuil);
            $em->flush();
            $request->getSession()->getFlashBag()->add('recueil_add', 'Le recueil a été crée avec succès');
            return $this->redirectToRoute('receuils');
        }

        $receuils = $this->getDoctrine()->getManager()->getRepository(Recueil::class)->findBy([
            'utilisateur' => $user
        ]);


        return $this->render('recueil/nouveau_recueil.html.twig',[
            'receuils' => $receuils,
            'form' => $form->createView(),
            'sources' => $this->getDoctrine()->getManager()->getRepository(Source::class)->findAll(),
            'active' => "receuils"
        ]);

    }




}