<?php

namespace RabbitMQClient\Service\Factory;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    RabbitMQClient\Publish\Publisher;

class RabbitClientFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Configuration');

        return new Publisher($config['rabbitMQ']);
    }
}
