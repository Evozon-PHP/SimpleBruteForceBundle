<?php

namespace EvozonPhp\SimpleBruteForceBundle\Repository;

use Doctrine\ORM\EntityRepository as Repository;
use EvozonPhp\SimpleBruteForceBundle\Entity\FailedLogin;
use function EvozonPhp\SimpleBruteForceBundle\canonicalize;

/**
 * Doctrine ORM failed login repository.
 *
 * @author Constantin Bejenaru <constantin.bejenaru@evozon.com>
 */
class FailedLoginRepository extends Repository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByIdentifier(string $identifier): ?FailedLogin
    {
        return $this->findOneBy(['identifier' => canonicalize($identifier)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCountAttempts(string $identifier): int
    {
        $failedLogin = $this->findOneByIdentifier($identifier);
        if (null === $failedLogin) {
            return 0;
        }

        return $failedLogin->getCount();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastAttempt(string $identifier): ?\DateTime
    {
        $failedLogin = $this->findOneByIdentifier($identifier);
        if (null === $failedLogin) {
            return null;
        }

        return $failedLogin->getUpdated();
    }

    /**
     * {@inheritdoc}
     */
    public function clearAttempts(string $identifier)
    {
        return $this->getEntityManager()
            ->createQuery(
                'DELETE FROM '.$this->getClassMetadata()->name.' fl WHERE fl.identifier = :identifier'
            )
            ->execute(['identifier' => canonicalize($identifier)]);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(FailedLogin $failedLogin, bool $sync = true)
    {
        $this->_em->persist($failedLogin);

        if ($sync) {
            $this->_em->flush($failedLogin);
        }
    }
}
