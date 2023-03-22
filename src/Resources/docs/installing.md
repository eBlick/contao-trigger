Installation
------------

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require eblick/contao-trigger
```


#### Step 2: Enable the Bundle

**Skip this point if you are using a *Managed Edition* of Contao.**

Enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new \eBlick\ContaoTriggerBundle(),
        );

        // ...
    }

    // ...
}
```


#### Step 3: Set up a cron job (recommended)

The system gets triggered by the internal 'minutely cron job'. Disable
Contao's 'poor man cronjob' (periodic command scheduler) to make sure
the execution only happens by a *real* cron job via the `_contao/cron`
route and not during regular site visits.