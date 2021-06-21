<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 * @UniqueEntity(fields={"nom"}, message="Une categorie de ce nom existe deja !!")
 */
class Categorie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Recueil::class, mappedBy="categorie")
     */
    private $recueils;

    public function __construct()
    {
        $this->recueils = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Recueil[]
     */
    public function getRecueils(): Collection
    {
        return $this->recueils;
    }

    public function addRecueil(Recueil $recueil): self
    {
        if (!$this->recueils->contains($recueil)) {
            $this->recueils[] = $recueil;
            $recueil->setCategorie($this);
        }

        return $this;
    }

    public function removeRecueil(Recueil $recueil): self
    {
        if ($this->recueils->removeElement($recueil)) {
            // set the owning side to null (unless already changed)
            if ($recueil->getCategorie() === $this) {
                $recueil->setCategorie(null);
            }
        }

        return $this;
    }
}
