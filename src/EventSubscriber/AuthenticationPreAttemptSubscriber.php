<?php

namespace EvozonPhp\SimpleBruteForceBundle\EventSubscriber;

use EvozonPhp\SimpleBruteForceBundle\Repository\RepositoryInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Pre Auth Subscriber.
 *
 * This subscriber will fire before anything else!
 *
 * @author Constantin Bejenaru <constantin.bejenaru@evozon.com>
 */
class AuthenticationPreAttemptSubscriber implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * Configuration values.
     *
     * @var array
     */
    private $config = [];

    /**
     * Request parameters to search for username.
     *
     * @var array
     */
    private $reqParameterUsername = [
        '_username',
        'username',
        'user',
    ];

    /**
     * Constructor.
     *
     * @param RepositoryInterface $repository
     * @param array               $config
     */
    public function __construct(RepositoryInterface $repository, array $config)
    {
        $this->repository = $repository;
        $this->config = $config;

        $this->logger = new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 9000],
            ],
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        foreach ($this->reqParameterUsername as $parameter) {
            $this->handleLoginAttempt(
                $event->getRequest()->get($parameter)
            );
        }
    }

    /**
     * Set configuration.
     *
     * @param array $config
     *
     * @return AuthenticationPreAttemptSubscriber
     */
    public function setConfig(array $config): AuthenticationPreAttemptSubscriber
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Set request parameters to search for username.
     *
     * @param array $reqParameterUsername
     *
     * @return AuthenticationPreAttemptSubscriber
     */
    public function setReqParameterUsername(array $reqParameterUsername): AuthenticationPreAttemptSubscriber
    {
        $this->reqParameterUsername = $reqParameterUsername;

        return $this;
    }

    /**
     * Handle login attempt.
     *
     * @param string|null $username
     *
     * @throws \Exception
     */
    protected function handleLoginAttempt(string $username = null): void
    {
        if (empty($username)) {
            return;
        }

        $maxAttempts = $this->config['limits']['max_attempts'];
        $blockPeriod = $this->config['limits']['block_period'];
        $alertAttempts = $this->config['limits']['alert_attempts'];

        $failedLogin = $this->repository->findOneByIdentifier($username);
        if (null === $failedLogin) {
            return;
        }

        if ($maxAttempts > $failedLogin->getCount()) {
            return;
        }

        $now = new \DateTimeImmutable();
        $diff = $now->sub(new \DateInterval($blockPeriod));

        if ($failedLogin->getUpdated() > $diff) {

            // increment counter and persist, even when blocked.
            $failedLogin->setCount($failedLogin->getCount() + 1);
            $this->repository->persist($failedLogin);

            if ($alertAttempts < $failedLogin->getCount()) {

                $this->logger->alert(
                    'Username "{username}" is suspicious of Brute-Force attack!',
                    [
                        'username' => $failedLogin->getIdentifier(),
                        'attempts' => $failedLogin->getCount(),
                        'last_attempt' => $failedLogin->getUpdated()->format(\DateTime::ATOM),
                    ]
                );
            }

            throw new HttpException(
                $this->config['response']['error_code'],
                $this->config['response']['error_message']
            );
        }

        return;
    }
}
