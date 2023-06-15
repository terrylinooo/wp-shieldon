<?php
/**
 * This file is part of the Shieldon package.
 *
 * (c) Terry L. <contact@terryl.in>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * php version 7.1.0
 *
 * @category  Web-security
 * @package   Shieldon
 * @author    Terry Lin <contact@terryl.in>
 * @copyright 2019 terrylinooo
 * @license   https://github.com/terrylinooo/shieldon/blob/2.x/LICENSE MIT
 * @link      https://github.com/terrylinooo/shieldon
 * @see       https://shieldon.io
 */

declare(strict_types=1);

namespace Shieldon\Event;

use Shieldon\Event\EventDispatcher;

/**
 * The helpers methods for Event Dispatcher.
 */
class Event
{
    /**
     * Add a new event Listener
     *
     * @param string  $name     The name of an event.
     * @param mixed   $func     Can be a function name, closure function or class.
     * @param integer $priority The execution priority.
     * 
     * @return void
     */
    public static function addListener(string $name, $func, int $priority = 10)
    {
        return EventDispatcher::instance()->addListener(
            $name,
            $func,
            $priority
        );
    }

    /**
     * Execute an event.
     *
     * @param string $name The name of an event.
     * @param array  $args The arguments.
     * 
     * @return mixed
     */
    public static function doDispatch(string $name, array $args = [])
    {
        return EventDispatcher::instance()->doDispatch(
            $name,
            $args
        );
    }
}

