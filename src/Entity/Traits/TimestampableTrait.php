<?php

namespace App\Entity\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @codeCoverageIgnore
 */
trait TimestampableTrait
{
    /**
     * @JMS\Groups({"timestampable"})
     * @var DateTime|null
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private ?DateTime $createdAt;

    /**
     * @JMS\Groups({"timestampable"})
     * @var DateTime|null
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private ?DateTime $updatedAt;

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return self
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     * @return self
     */
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
