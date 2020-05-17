<?php

namespace App\Entity\Traits;

use DateTime;
use Exception;
use JMS\Serializer\Annotation as JMS;

/**
 * @codeCoverageIgnore
 */
trait SoftDeletableTrait
{
    /**
     * @JMS\Exclude()
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $deletedAt;

    /**
     * Sets deletedAt.
     *
     * @param DateTime|null $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt(?DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function setDeleted(): self
    {
        $this->setDeletedAt(new DateTime());
        return $this;
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
    }
}
