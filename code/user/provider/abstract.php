<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract User Provider
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\User\Provider
 */
class KUserProviderAbstract extends KObject implements KUserProviderInterface
{
    /**
     * The list of users
     *
     * @var array
     */
    private $__users = array();

    /**
     * The list of users to fetch
     *
     * @var array
     */
    protected $_fetch = array();

    /**
     * Constructor
     *
     * The user array is a hash where the keys are user identifier and the values are an array of attributes:
     * 'password', 'enabled', and 'roles' etc. The user identifiers should be unique.
     *
     * @param KObjectConfig $config  An optional ObjectConfig object with configuration options
     * @return KUserProviderAbstract
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Create the users
        foreach($config->users as $identifier => $user)
        {
            if(!$user instanceof KUserInterface) {
                $user = $this->create($user);
            }

            $this->setUser($user);
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation
     *
     * @param   KObjectConfig $object An optional ObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'users' => array($this->getObject('user')),
        ));

        parent::_initialize($config);
    }

    /**
     * Load the user for the given username or identifier
     *
     * If the user could not be loaded an anonymous user will be returned with a user 'id' off 0.
     *
     * @param string $identifier A unique user identifier, (i.e a username or email address)
     * @return KUserInterface Returns a UserInterface object.
     */
    public function getUser($identifier)
    {
        //Fetch a user from the backend if not loaded yet
        if(!$this->isLoaded($identifier)) {
            $this->fetch($identifier);
        }

        //Create an anonymous user was not loaded
        if(!$user = $this->findUser($identifier))
        {
            $user  = $this->create(array(
                'id'   => 0,
                'name' => $this->getObject('translator')->translate('Anonymous')
            ));
        }

        return $user;
    }

    /**
     * Store user object in the provider
     *
     * @param KUserInterface $user
     * @return KUserProviderAbstract
     */
    public function setUser(KUserInterface $user)
    {
        $this->__users[$user->getId()] = $user;
        return $this;
    }

    /**
     * Find a user for the given identifier
     *
     * @param string $identifier A unique user identifier, (i.e a username or email address)
     * @return KUserInterface|null Returns a UserInterface object or NULL if the user hasn't been loaded yet
     */
    public function findUser($identifier)
    {
        return $this->isLoaded($identifier) ? $this->__users[$identifier] : null;
    }

    /**
     * Fetch the user for the given user identifier from the data store
     *
     * @param string|array $identifier A unique user identifier, (i.e a username or email address)
     *                                 or an array of identifiers
     * @param bool   $lazyload  Lazyload the $identifier(s) on the following call to getUser()
     * @return boolean
     */
    public function fetch($identifier, $lazyload = false)
    {
        $identifiers = array_merge((array) $identifier, $this->_fetch);

        //Only fetch identifiers that haven't been loaded yet.
        foreach($identifiers as $key => $value)
        {
            if($this->isLoaded($value)) {
                unset($identifiers[$key]);
            }
        }

        if(!empty($identifiers))
        {
            if (!$lazyload)
            {
                foreach ($identifiers as $identifier)
                {
                    $data = array(
                        'id' => $identifier,
                        'authentic' => false
                    );

                    $this->setUser($this->create($data));
                }

                return true;
            }
            else $this->_fetch = $identifiers;
        }

        return false;
    }

    /**
     * Create a user object
     *
     * @param array $data An associative array of user data
     * @return KUserInterface     Returns a UserInterface object
     */
    public function create($data)
    {
        $user = $this->getObject('user.default', array('data' => $data));
        return $user;
    }

    /**
     * Check if a user has already been loaded for a given user identifier
     *
     * @param $identifier
     * @return boolean TRUE if a user has already been loaded. FALSE otherwise
     */
    public function isLoaded($identifier)
    {
        return isset($this->__users[$identifier]);
    }
}