<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SortieRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
#[ApiResource(
    normalizationContext: ["groups" => ["sortie"]],
    denormalizationContext: ["groups" => ["sortie"]]
)]
#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["sortie"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["sortie"])]
    private ?string $nom = null;

    #[Assert\GreaterThanOrEqual('today',message: "La date de début est forcément supérieure à aujourd'hui")]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["sortie"])]
    private ?\DateTimeInterface $debutSortie = null;

    #[Assert\GreaterThanOrEqual('today',message: "La date de fin de sortie est forcément supérieure à aujourd'hui")]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["sortie"])]
    private ?DateTimeInterface $finSortie = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThanOrEqual('today',message: "La date limite est forcément supérieure à aujourd'hui")]
    #[Groups(["sortie"])]
    private ?\DateTimeInterface $dateLimiteInscription = null;


    #[ORM\Column(nullable: true)]
    #[Assert\Length(min:1,max: 1000,minMessage: "doit être supérieur à zéro",maxMessage: "doit être inférieur ou égal à 1000")]
    #[Groups(["sortie"])]
    private ?int $nombreInscriptionsMax = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(["sortie"])]
    private ?string $infosSortie = null;

    #[Assert\Length(min:1,max: 8)]
    #[ORM\Column]
    #[Groups(["sortie"])]
    private ?int $etat = null;

    #[ORM\Column(length: 150, nullable: true)]
    #[Groups(["sortie"])]
    private ?string $motifAnnulation = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["sortie"])]
    private ?Lieu $lieu = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["sortie"])]
    private ?Campus $campus = null;

    #[ORM\ManyToOne(inversedBy: 'organiseSorties')]
    #[Groups(["sortie"])]
    private ?Stagiaire $organisateur = null;

    #[ORM\ManyToMany(targetEntity: Stagiaire::class, inversedBy: 'participeSorties')]
    #[Groups(["sortie"])]
    private Collection $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
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

    public function getDebutSortie(): ?DateTimeInterface
    {
        return $this->debutSortie;
    }

    public function setDebutSortie(DateTimeInterface $debutSortie): self
    {
        $this->debutSortie = $debutSortie;
        return $this;
    }

    public function getFinSortie(): ?DateTimeInterface
    {
        return $this->finSortie;
    }

    public function setFinSortie(DateTimeInterface $finSortie): self
    {
        $this->finSortie = $finSortie;

        return $this;
    }

    public function getDateLimiteInscription(): ?DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNombreInscriptionsMax(): ?int
    {
        return $this->nombreInscriptionsMax;
    }

    public function setNombreInscriptionsMax(?int $nombreInscriptionsMax): self
    {
        $this->nombreInscriptionsMax = $nombreInscriptionsMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(?string $infosSortie): self
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(?string $motifAnnulation): self
    {
        $this->motifAnnulation = $motifAnnulation;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    public function getOrganisateur(): ?Stagiaire
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Stagiaire $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    /**
     * @return Collection<int, Stagiaire>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Stagiaire $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }

    public function removeParticipant(Stagiaire $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }
}
