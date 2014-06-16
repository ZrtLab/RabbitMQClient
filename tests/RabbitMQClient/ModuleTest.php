<?php

namespace RabbitClient\Tests;

use RabbitClient\Module as RabbitClientModule,
    Zend\EventManager\EventManager,
    Zend\EventManager\SharedEventManager,
    Zend\EventManager\StaticEventManager,
    RabbitClient\Module,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent;

class ModuleTest extends BaseModuleTest
{

public function testConfig()
    {
        $this->assertNotEmpty($this->config);
        $this->assertInternalType('array', $this->config);
    }

    protected function getEmptyMockForServiceManager()
    {
      $services = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
      $services->expects($this->once())
        ->method('has')
        ->with($this->equalTo('RabbitClient'))
        ->will($this->returnValue(false));

      return $services;
    }

    public function setUp()
    {
        $this->events = new EventManager();
        $this->sharedEvents = new SharedEventManager();
        $this->events->setSharedManager($this->sharedEvents);

        $this->application = new TestAsset\Application();
        $this->application->setEventManager($this->events);

        $this->event = new MvcEvent();
        $this->event->setApplication($this->application);
        $this->event->setTarget($this->application);

        $this->module = new Module();
    }

    public function tearDown()
    {
        StaticEventManager::resetInstance();
    }

    public function testConfigIsReturnedAsArray()
    {
        $module = new RabbitClientModule();
        $config = $module->getConfig();

        $this->assertInternalType('array', $config);

        $classExists = isset($config['service_manager']['factories']['RabbitClient'])
            && class_exists($config['service_manager']['factories']['RabbitClient']);

        $this->assertTrue($classExists);

    }

    public function testRegisterRabbitModule()
    {
        $serviceManager = $this->createServiceManagerForTest();
        $serviceManager->setService('Configuration',
            array(
                'rabbitMQ' => array(
                    'host' => '172.30.51.66',
                    'port' => '5672',
                    'user' => 'guest',
                    'pass' => 'guest',
                    'vhost'=> '/'
                )
            )
        );

        $services = $serviceManager->getRegisteredServices();
        $this->assertNotEmpty($services['factories']);
        $this->assertContains('rabbitclient', $services['factories']);

        $rabbitClient = $serviceManager->get('rabbitclient');
        $this->assertInstanceOf(
            'RabbitClient\Publish\Publisher', $rabbitClient
        );

    }

}
