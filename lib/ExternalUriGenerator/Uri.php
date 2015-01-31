<?php

namespace Opale\ExternalUriGenerator;

class Uri 
{
	private static $schemes = ['http' => ['port' => 80], 'https' => ['port' => 443], '' => ['port' => null]];
	private static $invalidPorts = [9, 15, 81, 82, 99, 100, 158, 300, 491, 531, 545, 625,
	 631, 782, 783, 829, 843, 888, 897, 898, 904, 911, 944, 953, 973, 981, 999, 1002, 1010];
	private static $validPortRange = [0, 1023];
	
    private $scheme; 
	private $user;
	private $password; 
	private $host; 
	private $port; 
	private $path;
	private $query; 
    private $fragment; 
	
	public function __construct(
     $host = '',
	 $scheme = '',
	 $path = '',
	 $query = '',
	 $fragment = '',
	 $port = null,
	 $user = '',
	 $password = null
	)
	{
		$this->setScheme($scheme);
		$this->setHost($host);
		$this->setPath($path);
		$this->setQuery($query);
		$this->setFragment($fragment);
		$this->setPort($port);
        $this->setUserInfo($user, $password);
	}

	/**
	* @param string $scheme
	* @throws \InvalidArgumentException
	*/
	private function setScheme($scheme)
	{
		if(in_array($scheme, array_keys(self::$schemes))){
			$this->scheme = $scheme;
		}else{
			throw new \InvalidArgumentException("Invalid scheme");
		}
	}

	/**
	* @param string $user
	* @param string|null $password
	*/
	private function setUserInfo($user, $password=null)
	{
		$this->user = $user;
		$this->password = $password;
	}

	/**
	* @param string $host
	* @throws \InvalidArgumentException
	*/
	private function setHost($host)
	{
		if('' === $host || false !== filter_var('http://' . $host, FILTER_VALIDATE_URL)){
			$this->host = $host;
		}else{
			throw new \InvalidArgumentException("Invalid or unsupported host");
		}
	}

	/**
	* @param int|null $port
	* @throws \InvalidArgumentException
	*/
	private function setPort($port)
	{
		if(null === $port)
		{
			$this->port = $port;

			return;
		}

		if(is_int($port) 
			&& $port >= self::$validPortRange[0] 
			&& $port <= self::$validPortRange[1]
			&& !in_array($port, self::$invalidPorts)
		){
			$this->port = $port;
		}else{
			throw new \InvalidArgumentException("Invalid port");
		}
	}

	/**
	* @param string $path
	* @throws \InvalidArgumentException
	*/
	private function setPath($path)
	{
		if('' === $path){
			$this->path = $path;

			return;
		}
		if(
		 false !== filter_var('http://example.com' . $path, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)
		 && '/' === $path{0}
		){
			$this->path = $path;
		}else{
			throw new \InvalidArgumentException("Invalid path");
		}
	}

	/**
	* @param string $query
	* @throws \InvalidArgumentException
	*/
	private function setQuery($query)
	{
		$query = ltrim($query, '?');
		$parameters = [];
		try{
			parse_str($query, $parameters);
            $this->query = $query;
		}catch(\Exception $e){

			return new \InvalidArgumentException('Invalid query', 0, $e);
		}
	}

	/**
	* @param string $fragment
	*/
	private function setFragment($fragment)
	{
		$fragment = ltrim($fragment, '#');
		$this->fragment = $fragment;
	}

	 /**
     * Retrieve the URI scheme.
     *
     * Implementations SHOULD restrict values to "http", "https", or an empty
     * string but MAY accommodate other schemes if required.
     *
     * If no scheme is present, this method MUST return an empty string.
     *
     * The string returned MUST omit the trailing "://" delimiter if present.
     *
     * @return string The scheme of the URI.
     */
    public function getScheme()
    {
    	return (string) $this->getScheme;
    }

    /**
     * Retrieve the authority portion of the URI.
     *
     * The authority portion of the URI is:
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * This method MUST return an empty string if no authority information is
     * present.
     *
     * @return string Authority portion of the URI, in "[user-info@]host[:port]"
     *     format.
     */
    public function getAuthority()
    {
    	$userInfo = $this->getUserInfo();
    	$port = $this->getPort();
    	$authority =  $this->getHost();
    	if($userInfo){
    		$authority = $userInfo . '@' . $authority;
    	}
    	if(null !== $port){
    		$authority .= ':' . $port;
    	}

    	return $authority;
    }

    /**
     * Retrieve the user information portion of the URI, if present.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * Implementations MUST NOT return the "@" suffix when returning this value.
     *
     * @return string User information portion of the URI, if present, in
     *     "username[:password]" format.
     */
    public function getUserInfo()
    {
    	$userInfo = $this->user;
    	if(null !== $this->password){
			$userInfo .= ':' . $this->password;
		}

		return $userInfo;
    }

    /**
     * Retrieve the host segment of the URI.
     *
     * This method MUST return a string; if no host segment is present, an
     * empty string MUST be returned.
     *
     * @return string Host segment of the URI.
     */
    public function getHost()
    {
    	return $this->host;	
    }

    /**
     * Retrieve the port segment of the URI.
     *
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard port
     * used with the current scheme, this method SHOULD return null.
     *
     * If no port is present, and no scheme is present, this method MUST return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return null|int The port for the URI.
     */
    public function getPort()
    {
    	if(null !== $this->port 
    		&& '' !== $this->scheme 
    		&& $this->port !== self::$schemes[$this->scheme]['port']
    	){
    		
    		return $this->port;
    	}

    	return null;
    }

    /**
     * Retrieve the path segment of the URI.
     *
     * This method MUST return a string; if no path is present it MUST return
     * an empty string.
     *
     * @return string The path segment of the URI.
     */
    public function getPath()
    {
    	return $this->path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * This method MUST return a string; if no query string is present, it MUST
     * return an empty string.
     *
     * The string returned MUST omit the leading "?" character.
     *
     * @return string The URI query string.
     */
    public function getQuery()
    {
    	return $this->query;
    }

    /**
     * Retrieve the fragment segment of the URI.
     *
     * This method MUST return a string; if no fragment is present, it MUST
     * return an empty string.
     *
     * The string returned MUST omit the leading "#" character.
     *
     * @return string The URI fragment.
     */
    public function getFragment()
    {
    	return $this->fragment;
    }

    /**
     * Create a new instance with the specified scheme.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified scheme. If the scheme
     * provided includes the "://" delimiter, it MUST be removed.
     *
     * Implementations SHOULD restrict values to "http", "https", or an empty
     * string but MAY accommodate other schemes if required.
     *
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     * @return self A new instance with the specified scheme.
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme)
    {
    	return $this->configureNewInstance(['scheme' => $scheme]);  
    }

    /**
     * Create a new instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string $user User name to use for authority.
     * @param null|string $password Password associated with $user.
     * @return self A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
    	return $this->configureNewInstance(['user' => $user, 'password' => $password]);  
    }

    /**
     * Create a new instance with the specified host.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host Hostname to use with the new instance.
     * @return self A new instance with the specified host.
     * @throws \InvalidArgumentException for invalid hostnames.
     */
    public function withHost($host)
    {
    	return $this->configureNewInstance(['host' => $host]);  
    }

    /**
     * Create a new instance with the specified port.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified port.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param null|int $port Port to use with the new instance; a null value
     *     removes the port information.
     * @return self A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
    	return $this->configureNewInstance(['port' => $port]);  
    }

    /**
     * Create a new instance with the specified path.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified path.
     *
     * The path MUST be prefixed with "/"; if not, the implementation MAY
     * provide the prefix itself.
     *
     * An empty path value is equivalent to removing the path.
     *
     * @param string $path The path to use with the new instance.
     * @return self A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
    	return $this->configureNewInstance(['path' => $path]); 
    }

    /**
     * Create a new instance with the specified query string.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified query string.
     *
     * If the query string is prefixed by "?", that character MUST be removed.
     * Additionally, the query string SHOULD be parseable by parse_str() in
     * order to be valid.
     *
     * An empty query string value is equivalent to removing the query string.
     *
     * @param string $query The query string to use with the new instance.
     * @return self A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query)
    {
    	return $this->configureNewInstance(['query' => $query]); 
    }

    /**
     * Create a new instance with the specified URI fragment.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified URI fragment.
     *
     * If the fragment is prefixed by "#", that character MUST be removed.
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The URI fragment to use with the new instance.
     * @return self A new instance with the specified URI fragment.
     */
    public function withFragment($fragment)
    {
    	return $this->configureNewInstance(['fragment' => $fragment]); 
    }

    /**
    * @param array $configuration
    */
    private function configureNewInstance($configuration)
    {
        $parameters = array_merge([
         'scheme' => $this->scheme,
         'host' => $this->host,
         'path' =>$this->path,
         'query' => $this->query,
         'fragment' => $this->fragment,
         'port' => $this->port,
         'user' => $this->user,
         'password' => $this->password
        ], $configuration); 

        return new self (
         $parameters['host'],
         $parameters['scheme'],
         $parameters['path'],
         $parameters['query'],
         $parameters['fragment'],
         $parameters['port'],
         $parameters['user'],
         $parameters['password']
        );
    }

    /**
     * Return the string representation of the URI.
     *
     * Concatenates the various segments of the URI, using the appropriate
     * delimiters:
     *
     * - If a scheme is present, "://" MUST append the value.
     * - If the authority information is present, that value will be
     *   contatenated.
     * - If a path is present, it MUST be prefixed by a "/" character.
     * - If a query string is present, it MUST be prefixed by a "?" character.
     * - If a URI fragment is present, it MUST be prefixed by a "#" character.
     *
     * @return string
     */
    public function __toString()
    {
    	$uri = $this->getAuthority();
    	if('' !== $this->scheme){
    		$uri = $this->scheme . '://' . $uri;
    	}
    	if('' !== $this->path){
    		$uri .= $this->path;
    	}
    	if('' !== $this->query){
    		$uri .= '?' . $this->query;
    	}
    	if('' !== $this->fragment){
    		$uri .= '#' . $this->fragment;
    	}

        return $uri;
    }
}
