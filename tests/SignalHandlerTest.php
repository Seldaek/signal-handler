<?php

namespace Seld\Signal;

use PHPUnit\Framework\TestCase;

if (!function_exists('pcntl_signal') || !function_exists('posix_kill')) {
    throw new \RuntimeException('PCNTL and POSIX exts are needed for the tests to run');
}

class SignalHandlerTest extends TestCase
{
    public function testLoggingAndDefault()
    {
        $log = $this->prophesize('Psr\Log\LoggerInterface');

        $signal = SignalHandler::create(null, $log->reveal());
        $log->info('Received SIGINT')->shouldBeCalledTimes(1);
        $log->info('Received SIGTERM')->shouldBeCalledTimes(1);

        posix_kill(posix_getpid(), SIGINT);
        posix_kill(posix_getpid(), SIGTERM);
        posix_kill(posix_getpid(), SIGURG);
        pcntl_signal_dispatch();
    }

    public function testCallbackAndCustom()
    {
        $sigNo = null;
        $sigName = null;

        $signal = SignalHandler::create(['SIGHUP'], function ($no, $name) use (&$sigNo, &$sigName) {
            $sigNo = $no;
            $sigName = $name;
        });

        posix_kill(posix_getpid(), SIGINT);
        pcntl_signal_dispatch();
        $this->assertNull($sigName);
        $this->assertNull($sigNo);

        posix_kill(posix_getpid(), SIGHUP);
        pcntl_signal_dispatch();
        $this->assertSame('SIGHUP', $sigName);
        $this->assertSame(SIGHUP, $sigNo);
    }

    public function testTriggerResetCycle()
    {
        $signal = SignalHandler::create(['SIGUSR1', 'SIGUSR2']);

        $this->assertFalse($signal->isTriggered());
        posix_kill(posix_getpid(), SIGUSR1);
        pcntl_signal_dispatch();
        $this->assertTrue($signal->isTriggered());

        $signal->reset();
        $this->assertFalse($signal->isTriggered());
        posix_kill(posix_getpid(), SIGUSR2);
        pcntl_signal_dispatch();
        $this->assertTrue($signal->isTriggered());
    }
}
