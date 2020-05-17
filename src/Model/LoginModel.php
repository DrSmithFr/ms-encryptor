<?php

declare(strict_types=1);

namespace App\Model;

use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @SWG\Definition()
 */
class LoginModel
{
    /**
     * @var string|null
     * @SWG\Property(
     *     type="string",
     *     description="UUID of user",
     *     example="30ae2d44-087e-4ea4-8048-e1cbb2bc1016"
     * )
     * @Assert\Email()
     */
    private ?string $uuid = null;

    /**
     * @var string|null
     * @SWG\Property(
     *     type="string",
     *     description="Plaintext password"
     * )
     * @Assert\Length(min="4")
     */
    private ?string $password = null;

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string|null $uuid
     *
     * @return self
     */
    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return self
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }
}
