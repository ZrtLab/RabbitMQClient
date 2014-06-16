<?php

namespace RabbitMQClient;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\ModuleRouteListener;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{

    public function onBootstrap(MvcEvent $event)
    {
        $eventManager = $event->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $serviceManager = $event->getApplication()->getServiceManager();

        $sharedManager = $event->getApplication()->getEventManager()->getSharedManager();
        $sharedManager->attach('*', Event\Listener::PUBLISH_RABBIT_CLIENT,
                function ($event) use ($serviceManager) {
                    $client = $serviceManager->get('RabbitMQClient');
                    if (!$client->getEnabled()) {
                        return false;
                    }
                    $publish = $event->getparam('publisher', null);

                    if (is_null($publish)) {
                        throw new \Exception("No se Definio el Publisher");
                    }

                    $params = $event->getParams();
                    unset($params['publisher']);

                    if (empty($params['replace'])) {
                        unset($params['replace']);
                    }

                    $client->setPublisherName($publish);
                    $client->setMessage(json_encode($params));
                    $client->send();
                    if ($client->getDebug()) {
                        $client->close();
                    }
                }
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            )
        );
    }

}
