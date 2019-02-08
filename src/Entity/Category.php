<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * Validation : unicité du nom
 * @UniqueEntity(fields={"name"}, message="La catégorie existe déjà")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * validation : non vide
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * valisation : nombre de caractères
     * @Assert\Length(max="20", min="2",
     *                maxMessage="Le nom ne doit pas comporter plus de {{ limit }} caractères",
     *                minMessage="Le nom doit comporter au moins {{ limit }} caractères")
     */
    private $name;

    public function getId(): ?int
    {
        return $this->id;
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
    
    public function __toString()
    {
        return $this->getName();
    }
}
