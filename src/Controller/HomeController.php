<?php


namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


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



}