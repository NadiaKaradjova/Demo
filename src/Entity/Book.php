<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Table(name="books")
 * @ORM\Entity(repositoryClass="App\Repository\BooksRepository")
 * @Vich\Uploadable
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
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Isbn()
     */
    private $ISBN;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $year;

    /**
     * @ORM\Column(type="string", length=2048)
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="Cover",cascade={"remove", "persist"})
     * @JoinColumn(name="cover_id", referencedColumnName="id")
     *
     */
    private $coverImage;

    private $inPrivateCollection = 0;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="bookCollection", cascade={"remove"})
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

    public function setName(?string $name)
    {
        $this->name = $name;
    }

    public function getISBN(): ?string
    {
        return $this->ISBN;
    }

    public function setISBN(?string $ISBN)
    {
        $this->ISBN = $ISBN;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }
    public function setYear(?string $year): void
    {
        $this->year = $year;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCoverImage(): ?Cover
    {
        return $this->coverImage;
    }

    public function setCoverImage(?Cover $coverImage): void
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
