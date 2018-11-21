<?php

namespace EvozonPhp\SimpleBruteForceBundle\EventSubscriber;

use EvozonPhp\SimpleBruteForceBundle\Entity\FailedLogin;
use EvozonPhp\SimpleBruteForceBundle\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Guard\Token\GuardTokenInterface;

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

        $credentials = $this->extractCredentialsFromToken($event->getAuthenticationToken());

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

    /**
     * Extract credentials array from security token.
     *
     * @param GuardTokenInterface $token
     * @return array
     */
    private function extractCredentialsFromToken(GuardTokenInterface $token): array
    {
        $credentials = $token->getCredentials();

        // handle special use case
        if (is_a($credentials, 'Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken')) {
            /** @var $credentials \Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken */
            $credentials = $credentials->getPayload();
        }

        if (!is_array($credentials)) {
            throw new \UnexpectedValueException(
                sprintf(
                    '"%s" expected to return "%s", but returned "%s" instead.',
                    'GuardTokenInterface::getCredentials()',
                    'array',
                    gettype($credentials)
                )
            );
        }

        return $credentials;
    }
}

