# SimpleBruteForceBundle

Very simple Symfony Bundle to count failed login attempts and block users which try too often.

### Installation

``` bash
composer require evozon-php/simple-bruteforce-bundle
```

### Register bundle

``` php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            ...
            new EvozonPhp\SimpleBruteForceBundle\SimpleBruteForceBundle(),
            ...
        ];
        return $bundles;
    }
}
```

### Configuration

``` yaml
simple_brute_force:
    limits:
        // Number of attempts before blocking.
        max_attempts: 5
        // How long the user is blocked - DateInterval duration spec format (ISO 8601)
        block_period: PT10M
        // How many failed attempts before logging an alert.
        alert_attempts: 25
    response:
        // HTTP response code once user is blocked.
        error_code: 403
        // HTTP response message once user is blocked.
        error_message: Forbidden
```

### Customize blocking

Symfony will dispatch a `security.authentication.failure` event via it's Security component. We listen on that event (`AuthenticationFailedSubscriber::onAuthenticationFailure()`) and use [voters](https://symfony.com/doc/current/security/voters.html) to decide if we increment the number of failed login attempts for the user.
To add your own voter, simply tag it with `simple_brute_force.security.voter`.

``` yaml
app.security.2fa_voter:
    class: App\Security\CustomVoter
    tags:
        - { name: simple_brute_force.security.voter }
```

### Todo

* Create multiple adapters to store failed logins: Redis, Memcached, file, etc. Main benefits would be to skip DB altogether.
* Send and format response content according to `Accept` request header.
* Add unit tests
