<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Metadata\Get;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\LivreRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'titre' => 'exact', 'langue' => 'exact'])]
#[ORM\Entity(repositoryClass: LivreRepository::class)]
#[ApiResource(
    operations: [new GetCollection(), new Get(uriTemplate: '/livre/{id}/')],
    normalizationContext: ['groups' => ['livre:read', 'categorie:read', 'auteur:read']],
)]
#[ApiFilter(SearchFilter::class,properties: ['titre' => 'partial', 'langue' => 'exact', 'categories.id' => 'exact',
                            'auteurs.nom' => 'exact', 'auteurs.prenom' => 'exact', 'auteurs.nationalite' => 'exact',
                            'auteurs.id' => 'exact', 'disponible' => 'exact']
)]
#[ApiFilter(DateFilter::class, properties: ['date_sortie', 'auteurs.date_naissance', 'auteurs.date_deces'])]
class Livre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['livre:read', 'livre:id'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['livre:read'])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['livre:read'])]
    private ?\DateTimeInterface $date_sortie = null;

    #[ORM\Column(length: 255)]
    #[Groups(['livre:read'])]
    private ?string $langue = null;

    #[ORM\Column(length: 255)]
    #[Groups(['livre:read'])]
    private ?string $photo_couverture = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups(['livre:read'])]
    private bool $disponible = true;

    #[ORM\ManyToMany(targetEntity: Auteur::class, inversedBy: 'livres')]
    #[Groups(['livre:read'])]
    private Collection $auteurs;

    #[ORM\ManyToMany(targetEntity: Categorie::class, mappedBy: 'livres')]
    #[Groups(['livre:read'])]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'livre', targetEntity: Emprunt::class)]
    private Collection $emprunts;

    #[ORM\OneToOne(mappedBy: 'livre', cascade: ['persist', 'remove'])]
    private ?Reservation $reservation = null;

    public function __construct()
    {
        $this->auteurs = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->emprunts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->date_sortie;
    }

    public function setDateSortie(\DateTimeInterface $date_sortie): static
    {
        $this->date_sortie = $date_sortie;

        return $this;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(string $langue): static
    {
        $this->langue = $langue;

        return $this;
    }

    public function getPhotoCouverture(): ?string
    {
        return $this->photo_couverture;
    }

    public function setPhotoCouverture(string $photo_couverture): static
    {
        $this->photo_couverture = $photo_couverture;

        return $this;
    }

    public function getDisponible(): bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): static
    {
        $this->disponible = $disponible;

        return $this;
    }

    /**
     * @return Collection<int, Auteur>
     */
    public function getAuteurs(): Collection
    {
        return $this->auteurs;
    }

    public function addAuteur(Auteur $auteur): static
    {
        if (!$this->auteurs->contains($auteur)) {
            $this->auteurs->add($auteur);
        }

        return $this;
    }

    public function removeAuteur(Auteur $auteur): static
    {
        $this->auteurs->removeElement($auteur);

        return $this;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addLivre($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeLivre($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Emprunt>
     */
    public function getEmprunts(): Collection
    {
        return $this->emprunts;
    }

    public function addEmprunt(Emprunt $emprunt): static
    {
        if (!$this->emprunts->contains($emprunt)) {
            $this->emprunts->add($emprunt);
            $emprunt->setLivre($this);
        }

        return $this;
    }

    public function removeEmprunt(Emprunt $emprunt): static
    {
        if ($this->emprunts->removeElement($emprunt)) {
            if ($emprunt->getLivre() === $this) {
                $emprunt->setLivre(null);
            }
        }

        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(Reservation $reservation): static
    {
        if ($reservation->getLivre() !== $this) {
            $reservation->setLivre($this);
        }

        $this->reservation = $reservation;

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservation === $reservation) {
            $this->reservation = null;
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->titre;
    }

    public static function getLivreByTitre(string $titre, LivreRepository $livreRepository): ?Livre
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('titre', $titre))
            ->setMaxResults(1);

        $livre = $livreRepository->matching($criteria)->first();

        return $livre instanceof Livre ? $livre : null;
    }
}
