<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Loader Adapter Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Class\Locator
 */
interface KClassLocatorInterface
{
    /**
     * Get locator name
     *
     * @return string
     */
    public static function getName();

    /**
     * Get a fully qualified path based on a class name
     *
     * @param  string  $class    The class name
     * @return string|boolean    Returns the path on success FALSE on failure
     */
    public function locate($class);

    /**
     * Register a namespace
     *
     * @param  string       $namespace
     * @param  string|array $path(s) The location of the namespace
     * @return KClassLocatorInterface
     */
    public function registerNamespace($namespace, $path);

    /**
     * Get a namespace path
     *
     * @param string $namespace The namespace
     * @return array|false The namespace path(s) or FALSE if the namespace does not exist.
     */
    public function getNamespacePath($namespace);

    /**
     * Get the registered namespaces
     *
     * @return array An array with namespaces as keys and path as value
     */
    public function getNamespaces();
}
