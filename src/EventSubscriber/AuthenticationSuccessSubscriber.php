<?php

namespace EvozonPhp\SimpleBruteForceBundle\EventSubscriber;

use EvozonPhp\SimpleBruteForceBundle\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Authentication Success Subscriber.
 *
 * @author Constantin Bejenaru <constantin.bejenaru@evozon.com>
 */
class AuthenticationSuccessSubscriber implements EventSubscriberInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }

    /**
     * @param AuthenticationEvent $event
     */
    public function onAuthenticationSuccess(AuthenticationEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        switch (true) {
            case $user instanceof UserInterface:
                $username = (string) $user->getUsername();
                break;

            case method_exists($user, '__toString'):
                $username = (string) $user;
                break;

            case is_string($user):
                $username = $user;
                break;

            // don't know what to do
            default:
                return;
        }

        $this->repository->clearAttempts($username);
    }
}
