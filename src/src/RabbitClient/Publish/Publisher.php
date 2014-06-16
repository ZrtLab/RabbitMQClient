<?php

namespace RabbitClient\Publish;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * @author L. Mayta <slovacus@gmail.com>
 */
class Publisher implements ServiceLocatorAwareInterface
{

    /**
     *
     * @var \PhpAmqpLib\Connection\AMQPConnection
     */
    protected $conection;
    protected $properties;

    /**
     *
     * @var \PhpAmqpLib\Message\AMQPMessage
     */
    protected $message;
    protected $config;

    /**
     *
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    protected $chanel = '';
    protected $queueProperties = array(false, true, false, false);
    protected $exchange = '';
    protected $queue = '';
    protected $name = "";
    protected $publisher;
    protected $configPublisher;
    protected $serviceLocator;
    protected $enabled;
    protected $enabledPublisher;
    protected $debug;

    /**
     *
     * @var type
     */
    protected $initialized = false;

    /**
     *
     * @todo upgrade code
     */
    protected $parametersPublisher = array();

    /**
     *
     * @param string $nombre
     * @param array  $config
     */
    public function __construct($config)
    {
        $this->checkRequiredOptions($config);
        $this->config = $config;
        if (isset($config['enabled'])) {
            $this->enabled = $config['enabled'];
        }
        if (isset($config['debug'])) {
            $this->debug = $config['debug'];
        }
        if (!$this->enabled) {
            return $this;
        }

        $this->conection = new AMQPConnection(
            $this->config['host'], $this->config['port'], $this->config['user'],
            $this->config['pass'], $this->config['vhost']
        );

        return $this;
    }

    public function getPublisherResource()
    {
        if (!$this->enabledPublisher) {
            return;
        }
        //->Cuando tiene dos envio por una instancia el ultimo no se envia
        //if (!$this->initialized) {
            $this->chanel = $this->conection->channel();
            $this->parametersPublisher = $this->config['publisher'][$this->getPublisherName()];
            $this->exchange = $this->parametersPublisher['exchange'];
            $this->queue = $this->parametersPublisher['queue'];
            $this->chanel->queue_declare($this->queue, false, true, false, false);
            $this->chanel->exchange_declare(
                $this->exchange, 'direct', false, true, false
            );
            $this->chanel->queue_bind($this->queue, $this->exchange);

            $this->initialized = true;
        //}

    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function getDebug()
    {
        return $this->debug;
    }

    public function setQueue($queue)
    {
        $this->queue = $queue;
    }

    public function getQueue()
    {
        return $this->queue;
    }

    public function setPublisherName($name)
    {
        $this->publisher = $name;
        $this->checkRequiredQueue();
    }

    protected function checkRequiredOptions(array $config)
    {
        $requiredOptions = array(
            'host',
            'port',
            'user',
            'pass',
            'vhost',
        );

        foreach ($requiredOptions as $value) {
            if (!array_key_exists($value, $config)) {
                throw new \Exception(
                    "No se encuentra en la configuracion el parametro {$value}"
                );
            }
        }
    }

    public function getChanel()
    {
        return $this->chanel;
    }

    public function getConection()
    {
        return $this->conection;
    }

    public function setConection($conection)
    {
        $this->conection = $conection;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function send()
    {
        if (!$this->enabledPublisher) {
            return;
        }
        $this->getPublisherResource();
        $this->chanel->basic_publish($this->message, $this->exchange);
    }

    public function close()
    {
        $this->chanel->close();
        $this->conection->close();
    }

    public function setMessage($msgBody, array $properties = array())
    {
        if (!$this->enabledPublisher) {
            return;
        }

        if (!is_null($properties)) {
            $this->properties = $properties;
        }

        $msg = new AMQPMessage($msgBody,
            array('content_type' => 'text/plain', 'delivery_mode' => 2));

        $this->message = $msg;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     *
     * @throws \Exception
     *                    @todo refactorizar
     */
    protected function checkRequiredQueue()
    {
        if (is_null($this->config['publisher'][$this->getPublisherName()])) {
            throw new \Exception(
                "no se declaro parametros para el publicador"
            );
        }
        $this->configPublisher = $this->config['publisher'][$this->getPublisherName()];
        $this->enabledPublisher = true;
        if (isset($this->configPublisher['enabled'])) {
            $this->enabledPublisher = $this->configPublisher['enabled'];
        }

        $requiredParameters = array(
            'exchange',
            'queue',
            'properties'
        );

        foreach ($requiredParameters as $value) {
            if (!array_key_exists($value, $this->configPublisher)) {
                throw new \Exception(
                    "no se encuentra en la configuracion el parametro {$value}"
                );
            }
        }
    }

    public function getPublisherName()
    {
        return $this->publisher;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

}
