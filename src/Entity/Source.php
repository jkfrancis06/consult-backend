<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=SourceRepository::class)
 */
class Source
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
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sourceUsername;

    /**
     * @ORM\OneToMany(targetEntity=Recueil::class, mappedBy="source")
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

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getSourceUsername(): ?string
    {
        return $this->sourceUsername;
    }

    public function setSourceUsername(string $sourceUsername): self
    {
        $this->sourceUsername = $sourceUsername;

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
            $recueil->setSource($this);
        }

        return $this;
    }

    public function removeRecueil(Recueil $recueil): self
    {
        if ($this->recueils->removeElement($recueil)) {
            // set the owning side to null (unless already changed)
            if ($recueil->getSource() === $this) {
                $recueil->setSource(null);
            }
        }

        return $this;
    }
}
