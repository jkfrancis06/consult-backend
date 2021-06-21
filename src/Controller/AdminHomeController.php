<?php


namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Recueil;
use App\Entity\Source;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class AdminHomeController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    /**
     * @Route("/admin", name="admin_home")
     */
    public function categories(Request $request){

        return $this->render('admin/home/dashboard.html.twig',[
            'active' => "admin_home"
        ]);
    }




    /**
     * @Route("/xhr-admin-get-chart", name="xhr_admin_et_chart_data")
     */
    public function getAdminChartData(){

        $user = $this->security->getUser();

        $categorie_array = [];

        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();

        if ($categories != null){
            foreach ($categories as $category){
                $tmp = [];
                $tmp["id"] = $category->getId();
                $tmp["nom"] = $category->getNom();
                $tmp["color"] = "#".$this->stringToColorCode($category->getNom());
                $tmp["recueils_count"] = 0;
                $recueils_by_categorie = $this->getDoctrine()->getRepository(Recueil::class)->findBy([
                    'categorie' => $category
                ]);
                if ($recueils_by_categorie != null){
                    $tmp["recueils_count"] = sizeof($recueils_by_categorie);
                }
                array_push($categorie_array,$tmp);

            }
        }

        $sources_array = [];

        $sources = $this->getDoctrine()->getRepository(Source::class)->findAll();

        if ($sources != null){
            foreach ($sources as $source){
                $tmp = [];
                $tmp["id"] = $source->getId();
                $tmp["libelle"] = $source->getLibelle();
                $tmp["color"] = "#".$this->stringToColorCode($source->getLibelle());
                $tmp["sourceUsername"] = $source->getSourceUsername();
                $tmp["recueils_count"] = 0;
                $recueils_by_source = $this->getDoctrine()->getRepository(Recueil::class)->findBy([
                    'source' => $source
                ]);
                if ($recueils_by_source != null){
                    $tmp["recueils_count"] = sizeof($recueils_by_source);
                }
                array_push($sources_array,$tmp);

            }
        }

        $now = new \DateTime( "30 days ago", new \DateTimeZone('Africa/Nairobi'));
        $interval = new \DateInterval( 'P1D'); // 1 Day interval
        $period = new \DatePeriod( $now, $interval, 30); // 7 Days

        $date_data = [];
        foreach( $period as $day) {
            $date_data_item = [];
            $date_data_item["date"] = $day->format( 'd-m-Y');
            $date_data_item["recueils_total"] = 0 ;
            $recueils = $this->getDoctrine()->getRepository(Recueil::class)->findAll();

            if ($recueils != null){
                foreach ($recueils as $recueil){
                    if ($recueil->getCreatedAt()->format('d-m-Y') == $day->format( 'd-m-Y')){
                        $date_data_item["recueils_total"] ++;
                    }
                }
            }
            array_push($date_data,$date_data_item);
        }



        return new JsonResponse([
            'categorie_chart' => $categorie_array,
            'source_chart' => $sources_array,
            'date_chart' => $date_data,
        ],200);

    }


    function stringToColorCode($str) {
        $code = dechex(crc32($str));
        $code = substr($code, 0, 6);
        return substr(md5($str), 0, 6);
    }



}