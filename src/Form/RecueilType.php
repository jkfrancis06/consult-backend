<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Recueil;
use App\Entity\Source;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RecueilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lienPost',TextType::class, [
                'label' => 'Lien du post',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('nature',TextareaType::class, [
                'label' => 'Nature',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('contenu',TextareaType::class, [
                'label' => 'Nature',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('source', EntityType::class, [
                // looks for choices from this entity
                'class' => Source::class,

                // uses the User.username property as the visible option string
                'choice_label' => 'libelle',
                'required' => true,  // not needed since it is true by default,
                'placeholder' => 'Choisir une source'

                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('categorie', EntityType::class, [
                // looks for choices from this entity
                'class' => Categorie::class,

                // uses the User.username property as the visible option string
                'choice_label' => 'nom',
                'required' => true,  // not needed since it is true by default,
                'placeholder' => 'Choisir une categorie'

                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer'])
            ->add('cancel', ResetType::class, ['label' => 'Annuler'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recueil::class,
        ]);
    }
}
