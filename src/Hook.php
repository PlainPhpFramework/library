<?php
/**
 * (c) Francesco Terenzani
 */

namespace pp;

use ksort;

/**
 * A simple Hook system
 * 
 * Subscribe an event with a callable observer:
 * 
 * ```
 * Hook::on('event_name', function() {});
 * ```
 * 
 * Subscribe to filter values:
 * 
 * ```
 * Hook::on('event_name', function(&$argument) {});
 * ```
 * 
 * Subscribe end avoid next observers to run:
 * 
 * ```
 * Hook::on('event_name', function() {return false;});
 * ```
 * 
 * Subscribe with hight priority
 * 
 * ```
 * Hook::on('event_name', function() {}, 255);
 * ```
 * 
 * Subscribe with low priority
 * 
 * ```
 * Hook::on('event_name', function() {}, -255);
 * ```
 * 
 * Trigger actions & filters:
 * 
 * ```
 * $myVar = 'Foo';
 * Hook::trigger('event_name', $myVar);
 * echo $myVar;
 * ```
 */
class Hook
{

    static protected $on = [];
    static protected $isSorted = [];

    /**
     * Register an event listener
     * 
     * @var string      $eventName    The name of the event
     * @var callable    $callback     A callable
     * @var mixin       $priority     The priority of the callable (default=0)
     */
    static function on(string $eventName, callable $callback, int $priority = 0): void
    {
        static::$on[$eventName][$priority][] = $callback;
        static::$isSorted[$eventName] = null;
    }


	/**
	 * Trigger an event and notify the observers
	 * 
	 * @var string     $eventName      The name of the event
	 * @var mixin      $argument       Event related argument
	 * 
	 * @return void
	 */
	static function trigger($eventName, &$argument = null): void
	{

		// There are some subscribers
		if (isset(static::$on[$eventName])) {

            if (!isset(static::$isSorted[$eventName])) {
                ksort(static::$on[$eventName], SORT_NUMERIC); 
                static::$isSorted[$eventName] = true;
            }

			// Notify the subscribers. If a subscriber return false, 
			// stop the propagation
			foreach (array_merge(...static::$on[$eventName]) as $callback) {
				if (false === $callback($argument)) {
					break;
				}
			}

		}

	}
}

