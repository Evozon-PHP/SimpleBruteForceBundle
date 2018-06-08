<?php

namespace EvozonPhp\SimpleBruteForceBundle\Repository;

use EvozonPhp\SimpleBruteForceBundle\Entity\FailedLogin;

/**
 * Failed Login attempts repository interface.
 *
 * @author Constantin Bejenaru <constantin.bejenaru@evozon.com>
 */
interface RepositoryInterface
{
    /**
     * Find failed login by identifier.
     *
     * @param string $identifier
     *
     * @return null|FailedLogin
     */
    public function findOneByIdentifier(string $identifier): ?FailedLogin;

    /**
     * Get number of failed attempts.
     *
     * @param string $identifier
     *
     * @return int
     */
    public function getCountAttempts(string $identifier): int;

    /**
     * Get last failed attempt datetime.
     *
     * @param string $identifier
     *
     * @return \DateTime|null
     */
    public function getLastAttempt(string $identifier): ?\DateTime;

    /**
     * Clear failed attempts.
     *
     * Note: https://martinfowler.com/eaaCatalog/repository.html
     *       "Objects can be added to and removed from the Repository..."
     *
     * @param string $identifier
     *
     * @return mixed
     */
    public function clearAttempts(string $identifier);

    /**
     * Persist a record.
     *
     * Note: https://martinfowler.com/eaaCatalog/repository.html
     *       "Objects can be added to and removed from the Repository..."
     *
     * @param FailedLogin $failedLogin
     * @param bool        $sync
     */
    public function persist(FailedLogin $failedLogin, bool $sync = true);
}
