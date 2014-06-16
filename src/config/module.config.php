<?php

return array(
    'service_manager'=> array(
        'factories' => array(
            'RabbitMQClient' => 'RabbitMQClient\Service\Factory\RabbitClientFactory',
        )
    ),
    'rabbitMQ' => array(
        'host' => 'localhost',
        'port' => '5672',
        'user' => 'user_name',
        'pass' => 'password',
        'vhost' => '/',
        'enabled' => true,
        'debug' => false,
        'publisher' => array(
            'queue.servicio' => array(
                'exchange' => 'name.exchange.portal',
                'queue' => 'queue',
                'properties' => array(
                    'user_id' => 'user_id',
                    'delivery_mode' => 2,
                    'content_type' => 'text/plain'
                )
            ),
        ),
    )
);
