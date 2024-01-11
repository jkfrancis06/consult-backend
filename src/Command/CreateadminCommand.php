<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:createadmin',
    description: 'Add a short description for your command',
)]
class CreateadminCommand extends Command
{

    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;


    public function __construct(EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }


    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $helper = $this->getHelper('question');

        $user = new User();

        $firstname = $helper->ask($input, $output, new Question('Saisir votre nom: '));
        $lastname = $helper->ask($input, $output, new Question('Saisir votre prenom: '));
        $username = $helper->ask($input, $output, new Question('Saisir votre username: '));
        $email = $helper->ask($input, $output, new Question('Saisir votre addresse email: '));
        $plainPassword = $helper->ask($input, $output, new Question('Saisir votre mot de passe: '));

        $hashedPassword = $this->passwordHasher->hashPassword($user,$plainPassword);

        $user->setNom($firstname);
        $user->setPrenom($lastname);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($hashedPassword);
        $user->setRoles([
            'ROLE_USER',
            'ROLE_ADMIN'
        ]);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Utilisateur ajoute avec succes');

        return Command::SUCCESS;
    }
}
