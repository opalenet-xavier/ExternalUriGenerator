<?php

namespace Opale\ExternalUriGenerator\Tests;

use Opale\ExternalUriGenerator\Tests\TestCase;
use Opale\ExternalUriGenerator\DynamicUri;

class DynamicUriTest extends TestCase
{
    public function testCanReplaceValues()
    {
        $uri = new DynamicUri('www.example.com', 'http', '/is_{test}', '', '', null, '', null, ['test' => 'value']);
        $this->assertEquals('http://www.example.com/is_value', $uri);
    }

    public function testCanOverrideValues()
    {
        $uri = new DynamicUri('www.example.com', 'http', '/is_{test}/and-{other}', '', '', null, '', null, ['test' => 'value', 'other' => 'test']);
        $uri->setParameters(['test' => 'other']);
        $this->assertEquals('http://www.example.com/is_other/and-test', $uri);
    }
    
    public function testCanOverrideWithIntegerValues()
    {
        $uri = new DynamicUri('www.example.com', 'http', '/is_{0}/and-{1}', '', '', null, '', null, ['0' => 'value', '1' => 'test']);
        $uri->setParameters(['0' => 'other']);
        $this->assertEquals('http://www.example.com/is_other/and-test', $uri);
    }
}
