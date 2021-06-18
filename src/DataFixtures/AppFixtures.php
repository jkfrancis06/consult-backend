<?php

namespace App\DataFixtures;

use App\Entity\Affaire;
use App\Entity\AffaireDepartement;
use App\Entity\AffaireUtilisateur;
use App\Entity\Departement;
use App\Entity\Tache;
use App\Entity\TacheUtilisateur;
use App\Entity\User;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use joshtronic\LoremIpsum;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AppFixtures extends Fixture
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    private $passwordEncoder;
    private $validator;


    public function __construct(ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->validator = $validator;
    }


    public function load(ObjectManager $manager)
    {

       $user = new User();
       $user->setNom('Francis');
       $user->setPrenom('Agbessi');
       $user->setUsername('admin');
        $plainPassword ='admin';
        $user->setPassword($this->passwordEncoder->encodePassword($user, $plainPassword));
        $user->setRoles(array('ROLE_USER','ROLE_ADMIN'));
        $manager->persist($user);
        $manager->flush();

    }
}
