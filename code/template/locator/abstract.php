<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Component Template Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Locator
 */
abstract class TemplateLocatorAbstract extends Object implements TemplateLocatorInterface
{
    /**
     * The locator name
     *
     * @var string
     */
    protected static $_name = '';

    /**
     * Found locations map
     *
     * @var array
     */
    protected $_locations;

    /**
     * The base path
     *
     * @var string
     */
    protected $_base_path;

    /**
     * Constructor
     *
     * Prevent creating instances of this class by making the constructor private
     *
     * @param ObjectConfig $config   An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        //Set the base path
        $this->setBasePath($config->base_path);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config  An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'base_path' => null
        ));

        parent::_initialize($config);
    }

    /**
     * Get the locator name
     *
     * @return string The stream name
     */
    public static function getName()
    {
        return static::$_name;
    }

    /**
     * Get the base path
     *
     * @return string The base path
     */
    public function getBasePath()
    {
        return $this->_base_path;
    }

    /**
     * Set the base path
     *
     * @param string $path The base path
     * @return TemplateLocatorAbstract
     */
    public function setBasePath($path)
    {
        $this->_base_path = $path;
        return $this;
    }

    /**
     * Find the template path
     *
     * @param  string $url   The Template url
     * @throws  \RuntimeException If the no base path exists while trying to locate a partial.
     * @return string|false The real template path or FALSE if the template could not be found
     */
    public function locate($url)
    {
        $base = $this->getBasePath();

        if($base) {
            $key = $base.'-'.$url;
        } else {
            $key = $url;
        }

        if(!isset($this->_locations[$key]))
        {
            $info = array(
                'url'   => $url,
                'base'  => $base,
                'path'  => '',
            );

            $this->_locations[$key] = $this->find($info);
        }

        return $this->_locations[$key];
    }

    /**
     * Get a path from an file
     *
     * Function will check if the path is an alias and return the real file path
     *
     * @param  string $file The file path
     * @return string The real file path
     */
    final public function realPath($file)
    {
        $result = false;
        $path   = dirname($file);

        // Is the path based on a stream?
        if (strpos($path, '://') === false)
        {
            // Not a stream, so do a realpath() to avoid directory traversal attempts on the local file system.
            $path = realpath($path); // needed for substr() later
            $file = realpath($file);
        }

        // The substr() check added to make sure that the realpath() results in a directory registered so that
        // non-registered directories are not accessible via directory traversal attempts.
        if (file_exists($file) && substr($file, 0, strlen($path)) == $path) {
            $result = $file;
        }

        return $result;
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param  string $url   The Template url
     * @param int     $time  The last modification time of the cached template (timestamp)
     * @return bool TRUE if the template is still fresh, FALSE otherwise
     */
    public function isFresh($url, $time)
    {
        if($file = $this->locate($url)) {
            return (bool) filemtime($file) < $time;
        }

        return false;
    }
}