<?php

namespace Opale\ExternalUriGenerator;

use Opale\ExternalUriGenerator\Uri;

class DynamicUri extends Uri
{
	
	private $parameters = []; 
    private $defaults = []; 

    public function __construct(
     $host = '',
     $scheme = '',
     $path = '',
     $query = '',
     $fragment = '',
     $port = null,
     $user = '',
     $password = null,
     $defaults = []
    )
    {
        parent::__construct($host, $scheme, $path, $query, $fragment, $port, $user, $password);
        $this->setDefaults($defaults);
    }

    /**
    * @param array $defaults
    */
    private function setDefaults($defaults)
    {
        $this->defaults = $defaults;
    }

	/**
	* @param array $parameters
	*/
	public function setParameters($parameters)
	{
		$this->parameters = $parameters;
	}
    /**
     * Return the string representation of the URI.
     * @return string
     */
    public function __toString()
    {
    	$uri = parent::__toString();
        $replacements = [];
        $parameters = array_merge($this->defaults, $this->parameters);
        foreach ($parameters as $key => $value) {
            $replacements['{' . $key .'}'] = $value;
        }

        return strtr($uri, $replacements);
    }
}
