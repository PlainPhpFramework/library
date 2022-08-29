<?php
/**
 * (c) Francesco Terenzani
 */

namespace pp;

/**
 * A simple Hook system
 * 
 * Subscribe an event with a callable observer:
 * 
 * ```
 * Hook::$on['event_name'][] = function() {};
 * ```
 * 
 * Subscribe to filter values:
 * 
 * ```
 * Hook::$on['event_name'][] = function(&$var1) {};
 * ```
 * 
 * Subscribe end avoid next observers to run:
 * 
 * ```
 * Hook::$on['event_name'][] = function() { return false; };
 * ```
 * 
 * Subscribe with hight priority
 * 
 * ```
 * Hook::$on['init'][] = function() { 
 *     array_unshift(Hook::$on['event_name'], function() {});
 * };
 * ```
 * 
 * Subscribe with low priority
 * 
 * ```
 * Hook::$on['init'][] = function() { 
 *     Hook::$on['event_name'][] = function() {};
 * };
 * ```
 * 
 * Trigger actions & filters:
 * 
 * ```
 * Hook::trigger('init');
 * $myVar = 'Foo';
 * Hook::trigger('event_name', $myVar);
 * echo $myVar;
 * ```
 */
class Hook
{

	static $on = [];

	/**
	 * Trigger an event and notify the observers
	 * 
	 * @var string   $eventName 	The name of the event
	 * @var mixin $argument		Event related argument
	 * 
	 * @return void
	 */
	static function trigger($eventName, &$argument = null): void
	{

		// There are subscribers
		if (isset(static::$on[$eventName])) {

			// Notify the subscribers. If a subscriber return false, 
			// stop the propagation
			foreach (static::$on[$eventName] as $callback) {
				if (false === $callback($argument)) {
					break;
				}
			}

		}

	}
}

