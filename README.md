Apidoc Generator Bundle
=======================

:TODO:

register bundle in dev and test environment
```php
public function registerBundles()
{
    if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
        $bundles[] = new  Dwo\ApidocGeneratorBundle\DwoApidocGeneratorBundle();
    }

    return $bundles;
}
```

call dump command
```
php bin/console dwo:apidoc_generator:dump
```
