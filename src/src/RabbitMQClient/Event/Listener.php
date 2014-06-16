<?php

namespace RabbitMQClient\Event;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

class Listener implements ListenerAggregateInterface
{

    const PUBLISH_RABBIT_CLIENT = 'publish.rabbit.client';

    public function attach(EventManagerInterface $events)
    {

    }

    public function detach(EventManagerInterface $events)
    {

    }

}
