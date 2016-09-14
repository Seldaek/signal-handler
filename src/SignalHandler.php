<?php

/*
 * This file is part of signal-handler.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Seld\Signal;

use Psr\Log\LoggerInterface;

/**
 * SignalHandler and factory
 */
class SignalHandler
{
    private $triggered = false;

    /**
     * Fetches the triggered state of the handler
     *
     * @return bool
     */
    public function isTriggered()
    {
        return $this->triggered;
    }

    /**
     * Resets the state to let a handler accept a signal again
     */
    public function reset()
    {
        $this->triggered = false;
    }

    /**
     * @param array $signals array of signal names (more portable) or constants
     * @param LoggerInterface|callable $loggerOrCallback A PSR-3 Logger or a callback($signal, $signalName)
     * @return SignalHandler A handler on which you can call isTriggered to know if the signal was received, and reset() to forget
     */
    public static function create($signals = null, $loggerOrCallback = null)
    {
        $handler = new static;

        if (!function_exists('pcntl_signal')) {
            return $handler;
        }

        if (!$signals) {
            $signals = [SIGINT, SIGTERM];
        } elseif (!is_array($signals)) {
            $signals = [$signals];
        }
        
        // PHP 7.1 allows async signals
        if (function_exists('pcntl_async_signals')) {
            pcntl_async_signals(true);
        } else {
            declare (ticks = 1);
        }
        foreach ($signals as $signal) {
            if (is_string($signal)) {
                // skip missing signals, for example OSX does not have all signals
                if (!defined($signal)) {
                    continue;
                }

                $signal = constant($signal);
            }

            pcntl_signal($signal, function ($signal) use ($handler, $loggerOrCallback) {
                $handler->triggered = true;
                $signalName = self::getSignalName($signal);

                if ($loggerOrCallback instanceof LoggerInterface) {
                    $loggerOrCallback->info('Received '.$signalName);
                } elseif (is_callable($loggerOrCallback)) {
                    $loggerOrCallback($signal, $signalName);
                }
            });
        }

        return $handler;
    }

    private static function getSignalName($signo)
    {
        $signals = [
            'SIGHUP', 'SIGINT', 'SIGQUIT', 'SIGILL', 'SIGTRAP', 'SIGABRT', 'SIGIOT', 'SIGBUS',
            'SIGFPE', 'SIGKILL', 'SIGUSR1', 'SIGSEGV', 'SIGUSR2', 'SIGPIPE', 'SIGALRM', 'SIGTERM',
            'SIGSTKFLT', 'SIGCLD', 'SIGCHLD', 'SIGCONT', 'SIGSTOP', 'SIGTSTP', 'SIGTTIN', 'SIGTTOU',
            'SIGURG', 'SIGXCPU', 'SIGXFSZ', 'SIGVTALRM', 'SIGPROF', 'SIGWINCH', 'SIGPOLL', 'SIGIO',
            'SIGPWR', 'SIGSYS', 'SIGBABY',
        ];

        foreach ($signals as $name) {
            if (defined($name) && constant($name) === $signo) {
                return $name;
            }
        }

        throw new \LogicException('Unknown signal #'.$signo);
    }
}
