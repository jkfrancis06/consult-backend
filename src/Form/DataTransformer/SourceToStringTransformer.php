<?php


namespace App\Form\DataTransformer;


use App\Entity\Source;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SourceToStringTransformer implements \Symfony\Component\Form\DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function transform($value)
    {

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($value)
    {
        $recueil = $this->entityManager
            ->getRepository(Source::class)
            // query for the issue with this id
            ->findOneBy([
                'libelle' => $value
            ])
        ;

        if (null === $recueil) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $value
            ));
        }

        return $recueil;
    }
}