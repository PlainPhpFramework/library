<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use pp\Hook;

final class HookTest extends TestCase
{

    public function testTriggerParameterCanBeModified(): void
    {

        Hook::$on['test_event'][] = function (&$counter) {
            $counter++;
        };
        
        Hook::$on['test_event'][] = function ($counter) {
            $counter++;
        };

        $counter = 0;

        Hook::trigger('test_event', $counter);

        $this->assertSame(
            1,
            $counter
        );
    }

    public function testMultipleObserversRun(): void
    {


        Hook::$on['test_event_2'][] = function (&$counter) {
            $counter++;
        };

        Hook::$on['test_event_2'][] = function (&$counter) {
            $counter++;
        };

        $counter = 0;

        Hook::trigger('test_event_2', $counter);

        $this->assertSame(
            2,
            $counter
        );

    }


    public function testStopPropagation(): void
    {

        Hook::$on['test_event_3'][] = function (&$counter) {
            $counter++;
            // Stop propagation
            return false;
        };

        Hook::$on['test_event_3'][] = function (&$counter) {
            $counter++;
        };

        $counter = 0;

        Hook::trigger('test_event_3', $counter);

        $this->assertSame(
            1,
            $counter
        );

    }


    public function testObserverWithoutParameterRun(): void
    {


        $counter = 0;
        Hook::$on['test_event_4'][] = function () use(&$counter)  {
            $counter++;
        };

        Hook::trigger('test_event_4', $counter);

        $this->assertSame(
            1,
            $counter
        );

    }

}
