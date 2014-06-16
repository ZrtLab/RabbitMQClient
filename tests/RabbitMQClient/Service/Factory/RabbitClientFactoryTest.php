<?php

namespace RabbitClient\Tests\Service\Factory;

use RabbitClient\Service\Factory\RabbitClientFactory,
    RabbitClient\Tests\BaseModuleTest;

class RabbitClientFactoryTest extends BaseModuleTest
{

    /**
     *
     * @covers \RabbitClient\Service\Factory\RabbitClientFactory::createService
     */
    public function testCreateService()
    {
        $serviceLocator = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $config = array(
            'rabbitMQ' => array(
                'host' => '172.30.51.66',
                'port' => '5672',
                'user' => 'guest',
                'pass' => 'guest',
                'vhost' => '/'
            )
        );

        $serviceLocator
            ->expects($this->any())
            ->method('get')
            ->with('Configuration')
            ->will($this->returnValue($config));

        $factory = new RabbitClientFactory();
        $this->assertNotEmpty($factory);

        $this->assertInstanceOf(
            'RabbitClient\Publish\Publisher',
            $factory->createService($serviceLocator)
        );
    }

}
