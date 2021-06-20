<?php


namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Recueil;
use App\Entity\Source;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;


class HomeController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
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
     * @Route("/", name="home")
     */
    public function home(){

        $user = $this->security->getUser();


        return $this->render('home/home.html.twig',[
            'active' => "home",
            'user' => $user
        ]);
    }

    /**
     * @Route("/xhr-get-chart", name="xhr_get_chart_data")
     */
    public function getChartData(){

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
                    'utilisateur' => $user,
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
                    'utilisateur' => $user,
                    'source' => $source
                ]);
                if ($recueils_by_source != null){
                    $tmp["recueils_count"] = sizeof($recueils_by_source);
                }
                array_push($sources_array,$tmp);

            }
        }


        $now = new \DateTime( "7 days ago", new \DateTimeZone('Africa/Nairobi'));
        $interval = new \DateInterval( 'P1D'); // 1 Day interval
        $period = new \DatePeriod( $now, $interval, 7); // 7 Days

        $date_data = [];
        foreach( $period as $day) {
            $date_data_item = [];
            $date_data_item["date"] = $day->format( 'd-m-Y');
            $date_data_item["recueils_total"] = 0 ;
            $recueils = $this->getDoctrine()->getRepository(Recueil::class)->findBy([
                'utilisateur' => $user
            ]);

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