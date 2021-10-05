<?php

namespace App\Entity;

use App\Repository\MailUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @ORM\Entity(repositoryClass=MailUserRepository::class)
 */
class MailUser
{

    const MAIL_DAILY = 1;
    const MAIL_WEEK = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastUpdate;

    /**
     * @ORM\ManyToMany(targetEntity=Rapport::class, inversedBy="mailUsers")
     */
    private $Rapports;

    /**
     * @ORM\Column(type="array")
     */
    private $type = [];



    public function __construct()
    {
        $this->Rapports = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->lastUpdate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(\DateTimeInterface $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * @return Collection|Rapport[]
     */
    public function getRapports(): Collection
    {
        return $this->Rapports;
    }

    public function addRapport(Rapport $rapport): self
    {
        if (!$this->Rapports->contains($rapport)) {
            $this->Rapports[] = $rapport;
        }

        return $this;
    }

    public function removeRapport(Rapport $rapport): self
    {
        $this->Rapports->removeElement($rapport);

        return $this;
    }

    public function getType(): ?array
    {
        return $this->type;
    }

    public function setType(array $type): self
    {
        $this->type = $type;

        return $this;
    }


}
