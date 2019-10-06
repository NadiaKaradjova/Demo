<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="covers")
 * @ORM\Entity(repositoryClass="App\Repository\CoverRepository")
 * @Vich\Uploadable
 */
class Cover implements \Serializable
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
    private $fileName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileSize;

    /**
     *
     * @var File|UploadedFile $file
     * @Vich\UploadableField(mapping="book_cover", fileNameProperty="fileName", size="fileSize", mimeType="fileType")
     * @Assert\File(
     *     mimeTypes = {"image/gif", "image/jpeg", "image/png"},
     *     mimeTypesMessage = "Please upload a valid .gif, .jpeg, .png, image"
     * )
     */
    protected $file;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;


   // private $book;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName)
    {
        $this->fileName = $fileName;
        $this->setPath('uploads/' . $fileName);
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path)
    {
        $this->path = $path;

        return $this;
    }

    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    public function setFileType(?string $fileType): self
    {
        $this->fileType = $fileType;

        return $this;
    }

    public function getFileSize(): ?string
    {
        return $this->fileSize;
    }

    public function setFileSize(?string $fileSize)
    {
        $this->fileSize = $fileSize;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }


    public function setFile(?File $file = null)
    {
        $this->file = $file;

        if ($file) {
            $this->updatedAt = new \DateTime();
        }
    }


    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize(array(
        $this->id,
        $this->path,
    ));
    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->path,
            ) = unserialize($serialized, array('allowed_classes' => false));
    }
}
