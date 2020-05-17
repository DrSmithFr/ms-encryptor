<?php

declare(strict_types = 1);

namespace App\Entity;

use InvalidArgumentException;
use Ramsey\Uuid\UuidInterface;
use App\Enum\SecurityRoleEnum;
use App\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use App\Entity\Traits\BlamableTrait;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @JMS\ExclusionPolicy("all")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @codeCoverageIgnore
 */
class User implements UserInterface
{
    use IdTrait;
    use TimestampableTrait;
    use BlamableTrait;

    /**
     * @JMS\Type("string")
     * @ORM\Column(type="uuid", unique=true)
     */
    private ?UuidInterface $uuid = null;

    /**
     * @var string|null
     * @ORM\Column()
     */
    private ?string $password = null;

    /**
     * Used internally for login form
     */
    private ?string $plainPassword = null;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $salt = null;

    /**
     * @var string[]
     * @JMS\Expose()
     * @JMS\MaxDepth(1)
     * @JMS\Type("array<string>")
     * @ORM\Column(type="json", name="roles")
     */
    private array $roles = [];

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string|null The username
     */
    public function getUsername(): ?string
    {
        return $this->getUuid() ? $this->getUuid()->toString() : null;
    }

    /**
     * Removes sensitive data from the user.
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(): void
    {
        $this->setPlainPassword(null);
    }

    public function addRole(string $role): self
    {
        if (!SecurityRoleEnum::isValidValue($role)) {
            throw new InvalidArgumentException('invalid role');
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): self
    {
        if (!SecurityRoleEnum::isValidValue($role)) {
            throw new InvalidArgumentException('invalid role');
        }

        if (($key = array_search($role, $this->roles, true)) !== false) {
            array_splice($this->roles, $key, 1);
        }

        return $this;
    }

    public function getUuid(): ?UuidInterface
    {
        return $this->uuid;
    }

    public function setUuid($uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }
}
