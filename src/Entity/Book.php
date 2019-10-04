<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="books")
 * @ORM\Entity(repositoryClass="App\Repository\BooksRepository")
 */
class Book
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ISBN;

    /**
     * @ORM\Column(type="integer")
     */
    private $year;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $coverImage;

    private $inPrivateCollection = 0;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="bookCollection")
     * @ORM\JoinTable(
     *     name="user_book",
     *     joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

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

    public function getISBN(): ?string
    {
        return $this->ISBN;
    }

    public function setISBN(string $ISBN): self
    {
        $this->ISBN = $ISBN;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year): void
    {
        $this->year = $year;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getCoverImage()
    {
        return $this->coverImage;
    }

    /**
     * @param mixed $coverImage
     */
    public function setCoverImage($coverImage): void
    {
        $this->coverImage = $coverImage;
    }

    /**
     * @return mixed
     */
    public function getInPrivateCollection()
    {
       return $this->inPrivateCollection;
    }

    public function setInPrivateCollection()
    {
        $this->inPrivateCollection = 1;
    }

//    /**
//     * @param mixed $inPrivateCollection
//     */
//    public function setInPrivateCollection($inPrivateCollection): void
//    {
//        $this->inPrivateCollection = $inPrivateCollection;
//    }

    public function getUsers()
    {
        return $this->users;
    }

    public function addUsers(User $user){
        $this->users->add($user);
    }

    public function removeUsers(User $user){
        $this->users->removeElement($user);
    }

}
