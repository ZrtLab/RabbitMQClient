<?php

namespace RabbitClient\Tests\Publisher;

use RabbitClient\Service\Factory\RabbitClientFactory,
    RabbitClient\Tests\BaseModuleTest;

class PublisherTest extends BaseModuleTest
{
    public function testServiceRabbit()
    {
        $this->assertNotEmpty($this->serviceManager);
        $this->assertInternalType('array', $this->serviceManager()->get('config'));
    }

}
