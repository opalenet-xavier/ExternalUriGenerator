<?php

namespace Opale\ExternalUriGenerator;

class ExternalUriGenerator
{
    /**
    * @var array $config
    */
    private $config;
    /**
    * @var array $uris
    */
    private $uris;

    /**
    * @param $config
    */
    public function __construct($config)
    {
        $this->config = $config;
        $this->uris = new UriCollection();

        foreach ($this->config as $key => $value) {
            $uri= new DynamicUri($value['host'], $value['scheme'], $value['path']);
            $this->uris->add($key, $uri);
        }
    }

    /**
    * @param string $name
    * @param array $parameters
    * @return string
    */
    public function generate($name, $parameters = [])
    {
        $uri = $this->uris->get($name);
        $uri->setParameters($parameters);

        return (string) $uri;
    }
}
