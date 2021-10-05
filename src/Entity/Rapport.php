<?php

namespace App\Entity;

use App\Repository\RapportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RapportRepository::class)
 */
class Rapport
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $rapportDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity=MailUser::class, mappedBy="Rapports")
     */
    private $mailUsers;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->mailUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRapportDate(): ?\DateTimeInterface
    {
        return $this->rapportDate;
    }

    public function setRapportDate(\DateTimeInterface $rapportDate): self
    {
        $this->rapportDate = $rapportDate;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|MailUser[]
     */
    public function getMailUsers(): Collection
    {
        return $this->mailUsers;
    }

    public function addMailUser(MailUser $mailUser): self
    {
        if (!$this->mailUsers->contains($mailUser)) {
            $this->mailUsers[] = $mailUser;
            $mailUser->addRapport($this);
        }

        return $this;
    }

    public function removeMailUser(MailUser $mailUser): self
    {
        if ($this->mailUsers->removeElement($mailUser)) {
            $mailUser->removeRapport($this);
        }

        return $this;
    }

    public function __toString(){
        return $this->createdAt->format('d-m-Y H:i:s');
    }
}
