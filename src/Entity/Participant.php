<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
    * @Assert\NotBlank(message="Le nom ne peut pas être vide.")
    * @Assert\Length(
    * min=3,
    * max=50,
    * minMessage="Le nom doit comporter au moins 3 caractères.",
    * maxMessage="Le nom ne peut pas dépasser 50 caractères."
    * )
    */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @Assert\NotBlank(message="L'email ne peut pas être vide.")
     * @Assert\Email(
     *     message="L'adresse email '{{ value }}' n'est pas une adresse valide."
     * )
     */
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    private ?event $event = null;

    #[ORM\Column(length: 255)]
    private ?string $adress = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getEvent(): ?event
    {
        return $this->event;
    }

    public function setEvent(?event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->adress;
    }

    public function setAddress(string $adress): static
    {
        $this->adress = $adress;

        return $this;
    }
}
