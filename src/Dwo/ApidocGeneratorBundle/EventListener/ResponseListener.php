<?php

namespace Dwo\ApidocGeneratorBundle\EventListener;

use Dwo\RequestLogger\Factory\ReqresFactory;
use Dwo\RequestLogger\Storage\StorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ResponseListener
 *
 * @author Dave Www <davewwwo@gmail.com>
 */
class ResponseListener implements EventSubscriberInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var ReqresFactory
     */
    protected $factory;

    /**
     * @param StorageInterface   $storage
     * @param ReqresFactory|null $factory
     */
    public function __construct(StorageInterface $storage, ReqresFactory $factory = null)
    {
        $this->storage = $storage;
        $this->factory = $factory ?: new ReqresFactory();
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onResponse(FilterResponseEvent $event)
    {
        /**
         * :TODO: add option "listener=true|false"
         * :TODO: add environment check (only test, or also dev|prod)
         */
        $this->storage->addEntry($this->factory->create($event->getRequest(), $event->getResponse()));
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onResponse', -10000),
        );
    }
}
