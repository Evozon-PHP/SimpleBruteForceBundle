<?php

namespace EvozonPhp\SimpleBruteForceBundle\EventSubscriber;

use EvozonPhp\SimpleBruteForceBundle\Entity\FailedLogin;
use EvozonPhp\SimpleBruteForceBundle\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;

/**
 * Authentication Failed Subscriber.
 *
 * @author Constantin Bejenaru <constantin.bejenaru@evozon.com>
 */
class AuthenticationFailedSubscriber implements EventSubscriberInterface
{

    private const CAN_AUTHENTICATE = 'can_authenticate';

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var AccessDecisionManagerInterface
     */
    private $accessDecisionManager;

    /**
     * Constructor.
     *
     * @param RequestStack        $requestStack
     * @param RepositoryInterface $repository
     */
    public function __construct(
        RequestStack $requestStack,
        RepositoryInterface $repository,
        AccessDecisionManagerInterface $accessDecisionManager
    ) {
        $this->requestStack = $requestStack;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
        ];
    }

    /**
     * Handle failed authentication.
     *
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        $decision = $this->accessDecisionManager->decide(
            $event->getAuthenticationToken(),
            [self::CAN_AUTHENTICATE],
            $event->getAuthenticationException()
        );

        if ($decision) {
            return;
        }

        $credentials = $event->getAuthenticationToken()->getCredentials();

        $username = $credentials['username'] ?? null;
        if (null === $username) {
            return;
        }

        $failedLogin = $this->repository->findOneByIdentifier($username);
        if (null === $failedLogin) {
            $failedLogin = new FailedLogin($username);
        }

        $failedLogin->setCount($failedLogin->getCount() + 1);
        $this->repository->persist($failedLogin);
    }
}
