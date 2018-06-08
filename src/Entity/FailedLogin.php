<?php

namespace EvozonPhp\SimpleBruteForceBundle\Entity;

use DateTime;
use function EvozonPhp\SimpleBruteForceBundle\canonicalize;

class FailedLogin
{
    /**
     * @var int
     */
    private $id;

    /**
     * Indentifier to track (username, token, ip, etc.).
     *
     * @var string
     */
    private $identifier;

    /**
     * Number of failed login attempts.
     *
     * @var int
     */
    private $count = 0;

    /**
     * Last updated at.
     *
     * @var DateTime|null
     */
    private $updated;

    /**
     * Constructor.
     *
     * @param string|null $identifier
     */
    public function __construct(string $identifier = null)
    {
        $this->setIdentifier($identifier);
    }

    /**
     * Get Id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set Id.
     *
     * @param int $id
     *
     * @return FailedLogin
     */
    public function setId(int $id): FailedLogin
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get Identifier.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Set Identifier.
     *
     * @param string $identifier
     *
     * @return FailedLogin
     */
    public function setIdentifier(string $identifier): FailedLogin
    {
        $this->identifier = canonicalize($identifier);

        return $this;
    }

    /**
     * Get Count.
     *
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Set Count.
     *
     * @param int $count
     *
     * @return FailedLogin
     */
    public function setCount(int $count): FailedLogin
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get Updated.
     *
     * @return DateTime|null
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * Set Updated.
     *
     * @param DateTime|null $updated
     *
     * @return FailedLogin
     */
    public function setUpdated(?DateTime $updated): FailedLogin
    {
        $this->updated = $updated;

        return $this;
    }
}
