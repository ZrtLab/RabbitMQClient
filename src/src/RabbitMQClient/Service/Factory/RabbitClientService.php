<?php

namespace RabbitMQClient\Service\Factory;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    RabbitClient\Publish\Publisher;

class RabbitMQClientService implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Configuration');

        return new Publisher($config['rabbitMQ']);
    }
}
