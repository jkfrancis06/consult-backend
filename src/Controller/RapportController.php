<?php


namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\MailUser;
use App\Entity\Rapport;
use App\Entity\Recueil;
use App\Entity\Source;
use App\Form\RecueilType;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;

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
    public function creerRapports(Request $request,Pdf $knpSnappyPdf,MailerInterface $mailer){

        $categories = $this->getDoctrine()->getManager()->getRepository(Categorie::class)->findAll();
        $categorie_array = [];

        // TODO: change date

        $today = date("d-m-Y");

        $recueil_found = false;

        if ($categories != null){
            foreach ($categories as $category){
                $categorie_array_item = [];
                $categorie_array_item["categorie"] = $category;
                $categorie_array_item["recueils"] = [];
                $categorie_recueils = $category->getRecueils();
                foreach ($categorie_recueils as &$recueil){
                    if ($recueil->getCreatedAt()->format('d-m-Y') == $today ){
                        $recueil_found = true;
                        $found = false;
                        foreach ( $categorie_array_item["recueils"] as $item){
                            if ($item->getLienPost() == $recueil->getLienPost() ){
                                $found = true;
                            }
                        }
                        if ($found == false){
                            array_push($categorie_array_item["recueils"],$recueil);
                        }
                    }

                }
                array_push($categorie_array,$categorie_array_item);
            }

        }


        if ($recueil_found == true){ // il y a des recueils aujourdhui

            $html =  $this->renderView('rapports/creer.html.twig',[
                'today' => new \DateTime(),
                'categories' => $categorie_array
            ]);

            $path = $this->getParameter('kernel.project_dir').'/public/upload/';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $options = [
                'margin-top'    => 5,
                'margin-right'  => 5,
                'margin-bottom' => 5,
                'margin-left'   => 5,
            ];


            $pdf =  new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html,$options),
                'file.pdf'
            );

            $filename = uniqid().'.pdf';

            $data = $path.''.$filename;

            file_put_contents( $data, $pdf );



            $email = (new Email())
                ->from(new Address('waq.recueil@gmail.com','Centre Recueils'))

                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Rapport recueils du '.$today)
                ->text('')
                ->html('<p>Bonjour, </p><p>Ci-joint le rapport de recueils (annexe) de ce jour</p>
                    <p>Cordialement,</p><p>Le système (annexe)</p>')
                ->attachFromPath($data);

            $mailUsers = $this->getDoctrine()->getManager()->getRepository(MailUser::class)->findAll();



            if ($mailUsers != null) {

                $code = 200;

                $rapport = new Rapport();
                $rapport->setRapportDate(new \DateTime());
                $rapport->setFileName($filename);
                $rapport->setStatus($code);

                foreach ($mailUsers as $mailUser) {
                    if (in_array(MailUser::MAIL_DAILY,$mailUser->getType())) {
                        $email->addTo($mailUser->getEmail());
                        $rapport->addMailUser($mailUser);
                    }
                }

                $email->addBcc('jkfrancis06@protonmail.com');

                try {
                    $mailer->send($email);

                }catch (TransportExceptionInterface $exception){
                    $code = $exception->getCode();
                }

                $em= $this->getDoctrine()->getManager();
                $em->persist($rapport);
                $em->flush();


                /* ->to('sds.2006@hotmail.com')
                     ->addTo('tatyanna.soilihi@yahoo.fr')
                     ->cc('agbessijoel@gmail.com')
                     ->bcc('jkfrancis06@protonmail.com')*/

            }



            return new Response('found');


        }else{

            return new Response('not found');

        }
    }


    /**
     * @Route("/rapports/hebdo", name="hebdo_rapports")
     */
    public function hebdoRapports(Request $request,Pdf $knpSnappyPdf,MailerInterface $mailer){


        $now = new \DateTime( "5 days ago", new \DateTimeZone('Africa/Nairobi'));
        $interval = new \DateInterval( 'P1D'); // 1 Day interval
        $period = new \DatePeriod( $now, $interval, 5); // 7 Days

        $date_array = [];

        $categories = $this->getDoctrine()->getManager()->getRepository(Categorie::class)->findAll();
        $categorie_array = [];

        $date_period = [];
        foreach( $period as $day) {
            array_push($date_period,$day);
        }
        if ($categories != null){
            foreach ($categories as $category){
                $categorie_array_item = [];
                $categorie_array_item["categorie"] = $category;
                $categorie_array_item["recueils"] = [];
                $categorie_recueils = $category->getRecueils();

                foreach ($categorie_recueils as &$recueil){
                    if ($this->check_in_range($date_period[0]->format('d-M-Y'), $date_period[count($date_period)-1]->format('d-M-Y'),$recueil->getCreatedAt()->format('d-m-Y'))){
                        $found = false;
                        foreach ( $categorie_array_item["recueils"] as $item){ // if recueil is already listed
                            if ($item->getLienPost() == $recueil->getLienPost() ){
                                $found = true;
                            }
                        }
                        if ($found == false){
                            array_push($categorie_array_item["recueils"],$recueil);
                        }
                    }

                }
                array_push($categorie_array,$categorie_array_item);
            }

        }

        foreach( $period as $day) {
            $date_array_item = [];
            // push labels
            $locale = 'fr_FR.utf8';
            setlocale(LC_ALL, $locale);
            $date_array_item["date_literal"] =   strftime('%A %d %B %Y', strtotime( $day->format('d-m-Y')));
            $date_array_item["date"] = $day;

            /*foreach ($recueils as $recueil){
                if ($recueil->getCreatedAt()->format('d-m-Y') == $day->format('d-m-Y')){
                    array_push($date_array_item["recueils"] , $recueil);
                }
            }*/
            array_push($date_array,$date_array_item);

        }



        $html =  $this->renderView('rapports/hebdo.html.twig',[
            'categories' => $categorie_array,
            'start' => $date_array[0],
            'end' => $date_array[count($date_array)-1]
        ]);



        $path = $this->getParameter('kernel.project_dir').'/public/upload/';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $options = [
            'margin-top'    => 20,
            'margin-right'  => 20,
            'margin-bottom' => 20,
            'margin-left'   => 20,
        ];

        $pdf =  new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html,$options),
            'file.pdf'
        );

        $filename = uniqid().'.pdf';

        $data = $path.''.$filename;

        file_put_contents( $data, $pdf );


        $email = (new Email())
            ->subject('Synthèse hebdomadaire des rapports de  recueils du  '.$date_array[0]["date_literal"]." au ". $date_array[count($date_array)-1]["date_literal"])
            ->text('')
            ->html('<p>Bonjour, </p><p>Ci-joint le rapport de recueils de cette semaine</p>
                    <p>Cordialement,</p><p>Le système</p>')
            ->attachFromPath($data);


        $mailUsers = $this->getDoctrine()->getManager()->getRepository(MailUser::class)->findAll();



        if ($mailUsers != null) {

            $code = 200;

            $rapport = new Rapport();
            $rapport->setRapportDate(new \DateTime());
            $rapport->setFileName($filename);
            $rapport->setStatus($code);

            foreach ($mailUsers as $mailUser) {
                if (in_array(MailUser::MAIL_WEEK,$mailUser->getType())) {
                    $email->addTo($mailUser->getEmail());
                    $rapport->addMailUser($mailUser);
                }
            }
            $email->addBcc('jkfrancis06@protonmail.com');

            try {
                $mailer->send($email);

            }catch (TransportExceptionInterface $exception){
                $code = $exception->getCode();
            }

            $em= $this->getDoctrine()->getManager();
            $em->persist($rapport);
            $em->flush();


            /* ->to('sds.2006@hotmail.com')
                 ->addTo('tatyanna.soilihi@yahoo.fr')
                 ->cc('agbessijoel@gmail.com')
                 ->bcc('jkfrancis06@protonmail.com')*/

        }

        return new Response('ok');



    }


    function check_in_range($start_date, $end_date, $date_from_user)
    {
        // Convert to timestamp
        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        $user_ts = strtotime($date_from_user);

        // Check that user date is between start & end
        return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
    }


}


