<?php

/*
 * Based on the Symfony\Component\Routing\UriCollection
 * @link https://github.com/symfony/Routing/blob/master/UriCollection.php
 */

namespace Opale\ExternalUriGenerator;

/**
 * A UriCollection represents a set of Uri instances.
 *
 * When adding a uri at the end of the collection, an existing uri
 * with the same name is removed first. So there can only be one uri
 * with a given name.
 *
 */
class UriCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var Uri[]
     */
    private $uris = array();

    /**
     * @var array
     */
    private $resources = array();

    public function __clone()
    {
        foreach ($this->uris as $name => $uri) {
            $this->uris[$name] = clone $uri;
        }
    }

    /**
     * Gets the current UriCollection as an Iterator that includes all uris.
     *
     * It implements \IteratorAggregate.
     *
     * @see all()
     *
     * @return \ArrayIterator An \ArrayIterator object for iterating over uris
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->uris);
    }

    /**
     * Gets the number of Uris in this collection.
     *
     * @return int The number of uris
     */
    public function count()
    {
        return count($this->uris);
    }

    /**
     * Adds a uri.
     *
     * @param string $name  The uri name
     * @param Uri  $uri A Uri instance
     *
     * @api
     */
    public function add($name, Uri $uri)
    {
        unset($this->uris[$name]);

        $this->uris[$name] = $uri;
    }

    /**
     * Returns all uris in this collection.
     *
     * @return Uri[] An array of uris
     */
    public function all()
    {
        return $this->uris;
    }

    /**
     * Gets a uri by name.
     *
     * @param string $name The uri name
     *
     * @return Uri|null A Uri instance or null when not found
     */
    public function get($name)
    {
        return isset($this->uris[$name]) ? $this->uris[$name] : null;
    }

    /**
     * Removes a uri or an array of uris by name from the collection.
     *
     * @param string|array $name The uri name or an array of uri names
     */
    public function remove($name)
    {
        foreach ((array) $name as $n) {
            unset($this->uris[$n]);
        }
    }

    /**
     * Adds a uri collection at the end of the current set by appending all
     * uris of the added collection.
     *
     * @param UriCollection $collection A UriCollection instance
     *
     * @api
     */
    public function addCollection(UriCollection $collection)
    {
        // we need to remove all uris with the same names first because just replacing them
        // would not place the new uri at the end of the merged array
        foreach ($collection->all() as $name => $uri) {
            unset($this->uris[$name]);
            $this->uris[$name] = $uri;
        }

        $this->resources = array_merge($this->resources, $collection->getResources());
    }

    /**
     * Sets the host pattern on all uris.
     *
     * @param string $host      The pattern
     */
    public function setHost($host)
    {
        foreach ($this->uris as $name => $uri) {
            $this->add($name, $uri->withHost($host));
        }
    }

    /**
     * Set the scheme all child uris use.
     *
     * @param string $scheme The scheme
     */
    public function setScheme($scheme)
    {
        foreach ($this->uris as $name => $uri) {
            $this->add($name, $uri->withScheme($scheme));
        }
        
    }
}