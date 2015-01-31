<?php

namespace Opale\ExternalUriGenerator\Tests;

use Opale\ExternalUriGenerator\Tests\TestCase;
use Opale\ExternalUriGenerator\Uri;

class UriTest extends TestCase 
{
	public function testCanCreateAnUriWithAHost()
	{
		$uri = new Uri('www.example.com');
		$this->assertEquals('www.example.com', $uri);
		$uri = new Uri();
		$this->assertEquals('www.other.com', $uri->withHost('www.other.com'));
	}	
	 /**
     * @expectedException InvalidArgumentException
     */
	public function testAPathMustStartByASlash()
	{
		$uri = new Uri();
		$uri->withPath('path');
	}

	public function testAPathMustBeValidAccordingToSpecs()
	{
		$uri = new Uri();
		$tests = ['/ test', '#lmerlm', '&test=false'];
		foreach($tests as $test)
		{
			try{
				$uri->withPath($test);
				$this->fail(
					sprintf('Expected \InvalidArgumentException got nothing for %s', $test)
					);
			}catch(\InvalidArgumentException $e){}
		}
	}

	public function testAnEmptyPathRemoveThePrevious()
	{
		$uri = new Uri('www.example.com', 'http', '/test');

		$this->assertEquals('http://www.example.com/test', $uri);
		$this->assertEquals('http://www.example.com', $uri->withPath(''));
	}

	public function testASchemeMustBeInTheListOfAllowedOnes()
	{
		$uri = new Uri();
		$tests = ['smb', 'afp', 'gopher', ' ', '//'];
		foreach($tests as $test)
		{
			try{
				$uri->withScheme($test);
				$this->fail(
					sprintf('Expected \InvalidArgumentException got nothing for %s', $test)
					);
			}catch(\InvalidArgumentException $e){}
		}
	}

	public function testAnEmptySchemeRemoveThePrevious()
	{
		$uri = new Uri('www.example.com', 'http');

		$this->assertEquals('http://www.example.com', $uri);
		$this->assertEquals('www.example.com', $uri->withScheme(''));	
	}

	public function testCanSetAScheme()
	{
		$uri = new Uri('www.example.com', 'http');
		$this->assertEquals('http://www.example.com', $uri);

		$uri = new Uri('www.example.com');
		$this->assertEquals('http://www.example.com', $uri->withScheme('http'));	
	}

	public function testCanSetAPort()
	{
		$uri = new Uri('www.example.com', 'http', '', '', '', 88);
		$this->assertEquals('http://www.example.com:88', $uri);

		$uri = new Uri('www.example.com', 'http');
		$this->assertEquals('http://www.example.com:88', $uri->withPort(88));	
	}

	public function testANullPortRemoveThePrevious()
	{
		$uri = new Uri('www.example.com', 'http', '', '', '', 88);
		$this->assertEquals('http://www.example.com', $uri->withPort(null));	
	}

	public function testCanSetCredentials()
	{
		$uri = new Uri('www.example.com', 'http', '', '', '', null, 'me');
		$this->assertEquals('http://me@www.example.com', $uri);
		$uri = new Uri('www.example.com', 'http', '', '', '', null, 'me', 'password');
		$this->assertEquals('http://me:password@www.example.com', $uri);
		$uri = new Uri('www.example.com', 'http');
		$this->assertEquals('http://you:passw0rd@www.example.com', $uri->withUserInfo('you', 'passw0rd'));
	}

	public function testAnEmptyUserRemoveThePrevious()
	{
		$uri = new Uri('www.example.com', 'http', '', '', '', null, 'me');
		$this->assertEquals('http://www.example.com', $uri->withUserInfo(''));
	}

	public function testCanSetAQuery()
	{
		$uri = new Uri('www.example.com', 'http', '', 'foo=bar&test=other');
		$this->assertEquals('http://www.example.com?foo=bar&test=other', $uri);
		$uri = new Uri('www.example.com');
		$this->assertEquals('www.example.com?bar=baz&test=other', $uri->withQuery('bar=baz&test=other'));
	}

	public function testAQueryStartingByAnInterogationPointIsCleaned()
	{
		$uri = new Uri('www.example.com', 'http', '', '?foo=bar&test=other');
		$this->assertEquals('foo=bar&test=other', $uri->getQuery());
		$this->assertEquals('http://www.example.com?foo=bar&test=other', $uri);
	}

	public function canSetAFragement()
	{
		$uri = new Uri('www.example.com', 'http', '', '', '#test');
		$this->assertEquals('http://www.example.com#test', $uri);
		$uri = new Uri('www.example.com');
		$this->assertEquals('www.example.com#test', $uri->withFragment('test'));
	}

	public function testAQueryStartingByANumberSignIsCleaned()
	{
		$uri = new Uri('www.example.com', 'http', '', '', '#test');
		$this->assertEquals('test', $uri->getFragment());
		$this->assertEquals('http://www.example.com#test', $uri);
	}

	public function testUsingAWithMethodCreateANewInstance()
	{
		$uri = new Uri();
		$this->assertNotSame($uri, $uri->withHost('www.other.com'));
		$this->assertNotSame($uri, $uri->withScheme(''));
		$this->assertNotSame($uri, $uri->withUserInfo('me'));
		$this->assertNotSame($uri, $uri->withPort(33));
		$this->assertNotSame($uri, $uri->withPath('/everywhere'));
		$this->assertNotSame($uri, $uri->withQuery('foo=bar'));
		$this->assertNotSame($uri, $uri->withFragment('here'));
	}
}