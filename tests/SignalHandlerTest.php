<?php

namespace Seld\Signal;

use PHPUnit\Framework\TestCase;

class SignalHandlerTest extends TestCase
{
    /**
     * @requires extension pcntl
     * @requires extension posix
     */
    public function testLoggingAndDefault()
    {
        $log = $this->prophesize('Psr\Log\LoggerInterface');

        $signal = SignalHandler::create(null, $log->reveal());
        $log->info('Received SIGINT')->shouldBeCalledTimes(1);
        $log->info('Received SIGTERM')->shouldBeCalledTimes(1);

        posix_kill(posix_getpid(), SIGINT);
        posix_kill(posix_getpid(), SIGTERM);
        posix_kill(posix_getpid(), SIGURG);
    }

    /**
     * @requires extension pcntl
     * @requires extension posix
     */
    public function testCallbackAndCustom()
    {
        $sigNo = null;
        $sigName = null;

        $signal = SignalHandler::create(['SIGHUP'], function ($no, $name) use (&$sigNo, &$sigName) {
            $sigNo = $no;
            $sigName = $name;
        });

        posix_kill(posix_getpid(), SIGINT);
        $this->assertNull($sigName);
        $this->assertNull($sigNo);

        posix_kill(posix_getpid(), SIGHUP);
        $this->assertSame('SIGHUP', $sigName);
        $this->assertSame(SIGHUP, $sigNo);
    }

    /**
     * @requires extension pcntl
     * @requires extension posix
     */
    public function testTriggerResetCycle()
    {
        $signal = SignalHandler::create(['SIGUSR1', 'SIGUSR2']);

        $this->assertFalse($signal->isTriggered());
        posix_kill(posix_getpid(), SIGUSR1);
        $this->assertTrue($signal->isTriggered());

        $signal->reset();
        $this->assertFalse($signal->isTriggered());
        posix_kill(posix_getpid(), SIGUSR2);
        $this->assertTrue($signal->isTriggered());
    }

    /**
     * @requires OSFAMILY Windows
     * @requires PHP >= 7.4
     */
    public function testLoggingAndDefaultOnWindows()
    {
        $log = $this->prophesize('Psr\Log\LoggerInterface');

        $signal = SignalHandler::create(null, $log->reveal());
        $log->info('Received SIGINT')->shouldBeCalledTimes(2);

        sapi_windows_generate_ctrl_event(PHP_WINDOWS_EVENT_CTRL_BREAK);
        sapi_windows_generate_ctrl_event(PHP_WINDOWS_EVENT_CTRL_BREAK);
    }

    /**
     * @requires OSFAMILY Windows
     * @requires PHP >= 7.4
     */
    public function testCallbackAndCustomOnWindows()
    {
        $sigNo = null;
        $sigName = null;

        $signal = SignalHandler::create(['SIGINT'], function ($no, $name) use (&$sigNo, &$sigName) {
            $sigNo = $no;
            $sigName = $name;
        });

        sapi_windows_generate_ctrl_event(PHP_WINDOWS_EVENT_CTRL_BREAK);
        sapi_windows_generate_ctrl_event(PHP_WINDOWS_EVENT_CTRL_BREAK);
        sapi_windows_generate_ctrl_event(PHP_WINDOWS_EVENT_CTRL_BREAK);
        $this->assertSame('SIGINT', $sigName);
        $this->assertSame(2, $sigNo);
    }

    /**
     * @requires OSFAMILY Windows
     * @requires PHP >= 7.4
     */
    public function testTriggerResetCycleOnWindows()
    {
        $signal = SignalHandler::create(['SIGINT']);

        $this->assertFalse($signal->isTriggered());
        sapi_windows_generate_ctrl_event(PHP_WINDOWS_EVENT_CTRL_BREAK);
        sapi_windows_generate_ctrl_event(PHP_WINDOWS_EVENT_CTRL_BREAK);
        sapi_windows_generate_ctrl_event(PHP_WINDOWS_EVENT_CTRL_BREAK);
        $this->assertTrue($signal->isTriggered());

        $signal->reset();
        $this->assertFalse($signal->isTriggered());
        sapi_windows_generate_ctrl_event(PHP_WINDOWS_EVENT_CTRL_BREAK);
        sapi_windows_generate_ctrl_event(PHP_WINDOWS_EVENT_CTRL_BREAK);
        sapi_windows_generate_ctrl_event(PHP_WINDOWS_EVENT_CTRL_BREAK);
        $this->assertTrue($signal->isTriggered());
    }
}
