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
 * User Session Singleton
 *
 * Force the user object to a singleton
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\User\Session
 */
final class UserSession extends UserSessionAbstract implements ObjectSingleton {}