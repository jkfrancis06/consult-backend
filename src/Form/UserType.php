<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',TextType::class, array(
                'required' => true
            ))
            ->add('password', PasswordType::class, array(
                'required' => true
            ))
            ->add('confirmPassword', PasswordType::class, array(
                'mapped' => false,
                'required' => true
            ))
            ->add('nom',TextType::class, array(
                'required' => true
            ))
            ->add('prenom',TextType::class, array(
                'required' => true
            ))
            ->add('roleUtilisateur',ChoiceType::class, array(
                'required' => true,
                'mapped' => false,
                'choices'  => [
                    'Utilisateur simple' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'attr' => array(
                    'class' => 'form-select'
                )
            ))
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer'])
            ->add('cancel', ResetType::class, ['label' => 'Annuler'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
