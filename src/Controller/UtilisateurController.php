<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\RecueilType;
use App\Form\UserType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class UtilisateurController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{


    /**
     * @var Security
     */
    private $security;

    private $passwordEncoder;


    public function __construct(Security $security, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->security = $security;
        $this->passwordEncoder = $passwordEncoder;

    }

    /**
     * @Route("/admin/user/create", name="add_user")
     */
    public function addUser(Request $request){

        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $password = $form->get('password')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();
            if ($password != $confirmPassword){
                $form->addError(new FormError('Les mot de passes ne sont pas identiques'));

            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(array($form->get('roleUtilisateur')->getData()));
            $user->setPassword($this->passwordEncoder->encodePassword($user, $form->get('password')->getData()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('home');

        }

        return $this->render('user/add.html.twig',[
            'active' => "user",
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/list", name="list_user")
     */
    public function listUser(Request $request,  PaginatorInterface $paginator){

        $users = $this->getDoctrine()->getManager()->getRepository(User::class)->findAll();

        $paginated_users = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('user/user_list.html.twig',[
            'active' => "user",
            'users' => $paginated_users,
        ]);

    }


}