# SimpleBruteForceBundle

Very simple Symfony Bundle to count failed login attempts and block users which try too often.

### Installation

```
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

``` yml
simple_brute_force:
    limits:
        // Number of attempts before blocking.
        max_attempts: 5
        // How long the user is blocked - DateInterval duration spec format (ISO 8601)
        block_period: PT10M
        // A message to show to the user.
        error_message: Unauthorized!
        // How many failed attempts before logging an alert.
        alert_attempts: 25
```
