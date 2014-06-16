<?php

namespace RabbitClient\Tests;

use RabbitClient\Module as RabbitClientModule,
    Zend\ServiceManager\Config as ServiceConfig,
    Zend\ServiceManager\ServiceManager;

abstract class BaseModuleTest extends \PHPUnit_Framework_TestCase
{

    protected $config;
    protected $serviceManager;

    public function setUp()
    {
        $module = new RabbitClientModule();
        $config = $module->getConfig();
        $this->assertNotEmpty($config);
        $this->assertInternalType('array',$config);

        $serviceConfig = new ServiceConfig($config['service_manager']);
        $this->serviceManager = new ServiceManager($serviceConfig);

        $this->assertInstanceOf('Zend\ServiceManager\ServiceManager', $this->serviceManager);
        $module = new ApplicationModule();
        $this->assertInternalType('array', $module->getConfig());

        $this->config = $module->getConfig();

        $serviceConfig = new ServiceConfig($this->config['service_manager']);
        $this->assertInstanceOf('Zend\ServiceManager\Config', $serviceConfig);

        $this->serviceManager = new ServiceManager($serviceConfig);
        $this->assertInstanceOf('Zend\ServiceManager\ServiceManager',
            $this->serviceManager);
        $this->serviceManager->setService('config', $this->config);
        $this->serviceLocator = Bootstrap::getServiceManager();
    }

    protected function createServiceManagerForTest()
    {
        $module = new RabbitClientModule();
        $config = $module->getConfig();
        $this->assertNotEmpty($config);
        $this->assertInternalType('array', $config);

        $serviceConfig  = new ServiceConfig($config['service_manager']);
        $this->assertNotEmpty($serviceConfig);

        $serviceManager = new ServiceManager($serviceConfig);

        $this->assertInstanceOf('Zend\ServiceManager\ServiceManager', $serviceManager);

        return $serviceManager;
    }
}
