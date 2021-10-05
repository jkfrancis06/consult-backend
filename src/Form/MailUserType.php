<?php

namespace App\Form;

use App\Entity\MailUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MailUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('name')
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Journalier' => MailUser::MAIL_DAILY,
                    'Hebdomadaire' => MailUser::MAIL_WEEK,
                ],
                'multiple' => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MailUser::class,
        ]);
    }
}
