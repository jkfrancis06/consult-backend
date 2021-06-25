<?php


namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Rapport;
use App\Entity\Recueil;
use App\Entity\Source;
use App\Form\RecueilType;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
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

            $pdf =  new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                'file.pdf'
            );

            $filename = uniqid().'.pdf';

            $data = $path.''.$filename;

            file_put_contents( $data, $pdf );



            $email = (new Email())
                ->from('waq.recueil@gmail.com')
                ->to('jkfrancis06@gmail.com')
                ->cc('granduc2013@gmail.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Rapport recueils du '.$today)
                ->text('')
                ->html('<p>Bonjour, </p><p>Ci-joint le rapport de recueils de ce jour</p>
                    <p>Cordialement,</p><p>Le système</p>')
                ->attachFromPath($data);


            $code = 200;
            try {
                $mailer->send($email);

            }catch (TransportExceptionInterface $exception){
                $code = $exception->getCode();
            }

            $today_datetime = new \DateTime();

            $rapport = new Rapport();
            $rapport->setRapportDate(new \DateTime());
            $rapport->setFileName($filename);
            $rapport->setStatus($code);
            $em= $this->getDoctrine()->getManager();
            $em->persist($rapport);
            $em->flush();

            return new Response('found');


        }else{

            return new Response('not found');

        }






    }


}