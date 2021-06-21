<?php


namespace App\Form\Type;


use App\Form\DataTransformer\SourceToStringTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatalistType extends \Symfony\Component\Form\AbstractType
{

    private $entityManager;
    private $transformer;


    public function __construct(SourceToStringTransformer $sourceToStringTransformer, EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->transformer = $sourceToStringTransformer;

    }

    public function getParent()
    {
        return EntityType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this->transformer);
    }

}