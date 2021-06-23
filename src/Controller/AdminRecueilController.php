<?php


namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Recueil;
use App\Entity\Source;
use App\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class AdminRecueilController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
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
     * @Route("/admin/receuils", name="admin_receuils")
     */
    public function adminReceuils(Request $request, PaginatorInterface $paginator){



        $startDate = "";
        $endDate = "";
        $search_sources = [];
        $search_categories = [];
        $search_users = [];
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

        if ($request->query->get('users') != null){
            foreach ($request->query->get('users') as $item){
                array_push($search_users,intval($item));
            }
        }



        if ($request->query->get('search')){
            $receuils = $this->getDoctrine()->getManager()->getRepository(Recueil::class)->adminFindByQuery(
                $search_users,
                $startDate,
                $endDate,
                $search_sources,
                $search_categories
            );
        }else{
            $receuils = $this->getDoctrine()->getManager()->getRepository(Recueil::class)->findAll();
        }

        $categories = $this->getDoctrine()->getManager()->getRepository(Categorie::class)->findAll();
        $sources = $this->getDoctrine()->getManager()->getRepository(Source::class)->findAll();


        $paginated_receuils = $paginator->paginate(
            $receuils,
            $request->query->getInt('page', 1),
            10
        );


        return $this->render('admin/recueils/recueil.html.twig',[
            'receuils' => $paginated_receuils,
            'active' => "admin_receuils",
            'categories' => $categories,
            'sources' => $sources,
            'users' => $this->getDoctrine()->getManager()->getRepository(User::class)->findAll(),
            'search_sources' =>$search_sources,
            'search_users' =>$search_users,
            'search_categories' => $search_categories,
            'daterange' => $daterange
        ]);

    }

}