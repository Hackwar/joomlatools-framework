<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright   Copyright (C) 2007 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/joomlatools/joomlatools-framework for the canonical source repository
 */

/**
 * Http Message Headers
 *
 * Container class that handles the aggregations of HTTP headers as a collection
 *
 * @link http://tools.ietf.org/html/rfc2616#section-4.2
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Http\Message
 */
class KHttpMessageHeaders extends KObjectArray
{
    /**
     * Constructor
     *
     * @param KObjectConfig $config  An optional KObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $headers = KObjectConfig::unbox($config->headers);
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config An optional KObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'headers' => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Returns the headers.
     *
     * @return array An array of headers
     */
    public function all()
    {
        return $this->toArray();
    }

    /**
     * Returns a header value by name.
     *
     * @param string  $key      The header name
     * @param mixed   $default  The default value
     * @param Boolean $first    Whether to return the first value or all header values
     * @return string|array     The first header value if $first is true, an array of values otherwise
     */
    public function get($key, $default = null, $first = true)
    {
        $key = strtr(strtolower($key), '_', '-');

        if (!isset($this[$key]))
        {
            if (null === $default) {
                return $first ? null : array();
            }

            return $first ? $default : array($default);
        }

        if ($first) {
            return count($this->_data[$key]) ? $this->_data[$key][0] : $default;
        }

        return $this->_data[$key];
    }

    /**
     * Sets a header by name.
     *
     * @param string       $key     The key
     * @param string|array $values  The value or an array of values
     * @param boolean      $replace Whether to replace the actual value of not (true by default)
     * @return KHttpMessageHeaders
     */
    public function set($key, $values, $replace = true)
    {
        $key = strtr(strtolower($key), '_', '-');

        if ($replace === true || !isset($this[$key])) {
            $this->_data[$key] = (array) $values;
        } else {
            $this->_data[$key] = array_merge($this->_data[$key], (array) $values);
        }

        return $this;
    }

    /**
     * Adds new headers the current HTTP headers set.
     *
     * This function will not add headers that already exist.
     *
     * @param array $headers An array of HTTP headers
     * @return KHttpMessageHeaders
     */
    public function add(array $headers)
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values, false);
        }

        return $this;
    }

    /**
     * Returns true if the HTTP header is defined.
     *
     * @param string $key The HTTP header
     * @return Boolean true if the parameter exists, false otherwise
     */
    public function has($key)
    {
        return array_key_exists(strtr(strtolower($key), '_', '-'), $this->_data);
    }

    /**
     * Returns true if the given HTTP header contains the given value.
     *
     * @param string $key   The HTTP header name
     * @param string $value The HTTP value
     * @return Boolean true if the value is contained in the header, false otherwise
     */
    public function contains($key, $value)
    {
        return in_array($value, $this->get($key, null, false));
    }

    /**
     * Removes a header nu name
     *
     * @param string $key The HTTP header name
     * @return KHttpMessageHeaders
     */
    public function remove($key)
    {
        $key = strtr(strtolower($key), '_', '-');
        unset($this->_data[$key]);
        return $this;
    }

    /**
     * Clear the current HTTP headers
     *
     * @return KHttpMessageHeaders
     */
    public function clear()
    {
        $this->_data = array();
        return $this;
    }

    /**
     * Returns the headers as a string.
     *
     * @return string
     */
    public function toString()
    {
        $headers = $this->_data;
        $content = '';

        ksort($headers);

        foreach ($headers as $name => $values)
        {
            $value   = '';
            $name    = implode('-', array_map('ucfirst', explode('-', $name)));
            $results = array();

            foreach ($values as $key => $value)
            {
                if(is_numeric($key)) {
                    $results[] = $value;
                } else {
                    $results[] = $key.'='.$value;
                }

                $value = implode($results, '; ');
            }

            if ($value) {
                $content .= sprintf("%s %s\r\n", $name.':', $value);
            }
        }

        return $content;
    }

    /**
     * Get a value by key
     *
     * @param   string  $key The key name.
     * @return  string  The corresponding value.
     */
    public function offsetGet($key)
    {
        $key = strtr(strtolower($key), '_', '-');

        $result = null;
        if (isset($this->_data[$key])) {
            $result = $this->_data[$key];
        }

        return $result;
    }

    /**
     * Set a value by key
     *
     * @param   string  $key   The key name
     * @param   mixed   $value The value for the key
     * @return  void
     */
    public function offsetSet($key, $value)
    {
        $key = strtr(strtolower($key), '_', '-');

        $this->_data[$key] = $value;
    }

    /**
     * Test existence of a key
     *
     * @param  string  $key The key name
     * @return boolean
     */
    public function offsetExists($key)
    {
        $key = strtr(strtolower($key), '_', '-');

        return array_key_exists($key, $this->_data);
    }

    /**
     * Unset a key
     *
     * @param   string  $key The key name
     * @return  void
     */
    public function offsetUnset($key)
    {
        $key = strtr(strtolower($key), '_', '-');

        unset($this->_data[$key]);
    }

    /**
     * Allow PHP casting of this object
     *
     * @return string
     */
    final public function __toString()
    {
        return $this->toString();
    }
}