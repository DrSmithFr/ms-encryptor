<?php

namespace App\Entity;

use SplFileInfo;
use Ramsey\Uuid\UuidInterface;
use App\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use App\Entity\Traits\BlamableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Entity\Traits\SoftDeletableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity()
 * @ORM\Table(name="medias")
 * @codeCoverageIgnore
 * @JMS\ExclusionPolicy("all")
 * @codeCoverageIgnore
 */
class Media
{
    use IdTrait;
    use BlamableTrait;
    use TimestampableTrait;
    use SoftDeletableTrait;

    /**
     * @JMS\Type("string")
     * @ORM\Column(type="uuid", unique=true)
     */
    private ?UuidInterface $uuid = null;

    /**
     * @ORM\Column(name="content_type", type="string", length=255, nullable=true)
     * @JMS\Expose()
     */
    protected ?string $contentType = null;

    /**
     * @ORM\Column(name="size", type="integer", nullable=true)
     * @JMS\Expose()
     */
    protected ?int $size = null;

    /**
     * @JMS\Type("string")
     * @JMS\Expose()
     * @ORM\Column(type="string", type="string", length=255)
     */
    protected ?string $extension = null;

    /**
     * This is just a temporary file holder, for file uploads through a form.
     *
     * @var UploadedFile|File|SplFileInfo|null
     * @Assert\File(maxSize="3000000")
     */
    protected ?SplFileInfo $file = null;

    public function getUuid(): ?UuidInterface
    {
        return $this->uuid;
    }

    public function setUuid(?UuidInterface $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setContentType(?string $contentType): self
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;
        return $this;
    }

    public function setFile(?SplFileInfo $file): self
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return UploadedFile|File|SplFileInfo|null
     */
    public function getFile(): ?SplFileInfo
    {
        return $this->file;
    }
}
