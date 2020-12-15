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
    /**
     * The SIGHUP signal is sent to a process when its controlling terminal is closed. It was originally designed to
     * notify the process of a serial line drop (a hangup). In modern systems, this signal usually means that the
     * controlling pseudo or virtual terminal has been closed. Many daemons will reload their configuration files and
     * reopen their logfiles instead of exiting when receiving this signal. nohup is a command to make a command ignore
     * the signal.
     */
    const SIGHUP = 'SIGHUP';

    /**
     * The SIGINT signal is sent to a process by its controlling terminal when a user wishes to interrupt the process.
     * This is typically initiated by pressing Ctrl-C, but on some systems, the "delete" character or "break" key can be
     * used.
     */
    const SIGINT = 'SIGINT';

    /**
     * The SIGQUIT signal is sent to a process by its controlling terminal when the user requests that the process quit
     * and perform a core dump.
     */
    const SIGQUIT = 'SIGQUIT';

    /**
     * The SIGILL signal is sent to a process when it attempts to execute an illegal, malformed, unknown, or privileged
     * instruction.
     */
    const SIGILL = 'SIGILL';

    /**
     * The SIGTRAP signal is sent to a process when an exception (or trap) occurs: a condition that a debugger has
     * requested to be informed of — for example, when a particular function is executed, or when a particular variable
     * changes value.
     */
    const SIGTRAP = 'SIGTRAP';

    /**
     * The SIGABRT signal is sent to a process to tell it to abort, i.e. to terminate. The signal is usually initiated
     * by the process itself when it calls abort function of the C Standard Library, but it can be sent to the process
     * from outside like any other signal.
     */
    const SIGABRT = 'SIGABRT';

    const SIGIOT = 'SIGIOT';

    /**
     * The SIGBUS signal is sent to a process when it causes a bus error. The conditions that lead to the signal being
     * sent are, for example, incorrect memory access alignment or non-existent physical address.
     */
    const SIGBUS = 'SIGBUS';

    const SIGFPE = 'SIGFPE';

    /**
     * The SIGKILL signal is sent to a process to cause it to terminate immediately (kill). In contrast to SIGTERM and
     * SIGINT, this signal cannot be caught or ignored, and the receiving process cannot perform any clean-up upon
     * receiving this signal.
     */
    const SIGKILL = 'SIGKILL';

    /**
     * The SIGUSR1 signal is sent to a process to indicate user-defined conditions.
     */
    const SIGUSR1 = 'SIGUSR1';

    /**
     * The SIGUSR1 signa2 is sent to a process to indicate user-defined conditions.
     */
    const SIGUSR2 = 'SIGUSR2';

    /**
     * The SIGSEGV signal is sent to a process when it makes an invalid virtual memory reference, or segmentation fault,
     * i.e. when it performs a segmentation violation.
     */
    const SIGSEGV = 'SIGSEGV';

    /**
     * The SIGPIPE signal is sent to a process when it attempts to write to a pipe without a process connected to the
     * other end.
     */
    const SIGPIPE = 'SIGPIPE';

    /**
     * The SIGALRM, SIGVTALRM and SIGPROF signal is sent to a process when the time limit specified in a call to a
     * preceding alarm setting function (such as setitimer) elapses. SIGALRM is sent when real or clock time elapses.
     * SIGVTALRM is sent when CPU time used by the process elapses. SIGPROF is sent when CPU time used by the process
     * and by the system on behalf of the process elapses.
     */
    const SIGALRM = 'SIGALRM';

    /**
     * The SIGTERM signal is sent to a process to request its termination. Unlike the SIGKILL signal, it can be caught
     * and interpreted or ignored by the process. This allows the process to perform nice termination releasing
     * resources and saving state if appropriate. SIGINT is nearly identical to SIGTERM.
     */
    const SIGTERM = 'SIGTERM';

    const SIGSTKFLT = 'SIGSTKFLT';
    const SIGCLD = 'SIGCLD';

    /**
     * The SIGCHLD signal is sent to a process when a child process terminates, is interrupted, or resumes after being
     * interrupted. One common usage of the signal is to instruct the operating system to clean up the resources used by
     * a child process after its termination without an explicit call to the wait system call.
     */
    const SIGCHLD = 'SIGCHLD';

    /**
     * The SIGCONT signal instructs the operating system to continue (restart) a process previously paused by the
     * SIGSTOP or SIGTSTP signal. One important use of this signal is in job control in the Unix shell.
     */
    const SIGCONT = 'SIGCONT';

    /**
     * The SIGSTOP signal instructs the operating system to stop a process for later resumption.
     */
    const SIGSTOP = 'SIGSTOP';

    /**
     * The SIGTSTP signal is sent to a process by its controlling terminal to request it to stop (terminal stop). It is
     * commonly initiated by the user pressing Ctrl+Z. Unlike SIGSTOP, the process can register a signal handler for or
     * ignore the signal.
     */
    const SIGTSTP = 'SIGTSTP';

    /**
     * The SIGTTIN signal is sent to a process when it attempts to read in from the tty while in the background.
     * Typically, this signal is received only by processes under job control; daemons do not have controlling
     */
    const SIGTTIN = 'SIGTTIN';

    /**
     * The SIGTTOU signal is sent to a process when it attempts to write out from the tty while in the background.
     * Typically, this signal is received only by processes under job control; daemons do not have controlling
     */
    const SIGTTOU = 'SIGTTOU';

    /**
     * The SIGURG signal is sent to a process when a socket has urgent or out-of-band data available to read.
     */
    const SIGURG = 'SIGURG';

    /**
     * The SIGXCPU signal is sent to a process when it has used up the CPU for a duration that exceeds a certain
     * predetermined user-settable value. The arrival of a SIGXCPU signal provides the receiving process a chance to
     * quickly save any intermediate results and to exit gracefully, before it is terminated by the operating system
     * using the SIGKILL signal.
     */
    const SIGXCPU = 'SIGXCPU';

    /**
     * The SIGXFSZ signal is sent to a process when it grows a file larger than the maximum allowed size
     */
    const SIGXFSZ = 'SIGXFSZ';

    /**
     * The SIGVTALRM signal is sent to a process when the time limit specified in a call to a preceding alarm setting
     * function (such as setitimer) elapses. SIGVTALRM is sent when CPU time used by the process elapses.
     */
    const SIGVTALRM = 'SIGVTALRM';

    /**
     * The SIGPROF signal is sent to a process when the time limit specified in a call to a preceding alarm setting
     * function (such as setitimer) elapses. SIGPROF is sent when CPU time used by the process and by the system on
     * behalf of the process elapses.
     */
    const SIGPROF = 'SIGPROF';

    /**
     * The SIGWINCH signal is sent to a process when its controlling terminal changes its size (a window change).
     */
    const SIGWINCH = 'SIGWINCH';

    /**
     * The SIGPOLL signal is sent when an event occurred on an explicitly watched file descriptor.Using it effectively
     * leads to making asynchronous I/O requests since the kernel will poll the descriptor in place of the caller. It
     * provides an alternative to active polling.
     */
    const SIGPOLL = 'SIGPOLL';

    const SIGIO = 'SIGIO';

    /**
     * The SIGPWR signal is sent to a process when the system experiences a power failure.
     */
    const SIGPWR = 'SIGPWR';

    /**
     * The SIGSYS signal is sent to a process when it passes a bad argument to a system call. In practice, this kind of
     * signal is rarely encountered since applications rely on libraries (e.g. libc) to make the call for them.
     */
    const SIGSYS = 'SIGSYS';

    const SIGBABY = 'SIGBABY';

    /**
     * @var bool
     */
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
     *
     * @return void
     */
    public function reset()
    {
        $this->triggered = false;
    }

    /**
     * @param (string|int)[] $signals array of signal names (more portable) or constants
     * @param LoggerInterface|callable $loggerOrCallback A PSR-3 Logger or a callback($signal, $signalName)
     * @phpstan-param LoggerInterface|(callable(int $signal, string $name): void) $loggerOrCallback
     * @return static A handler on which you can call isTriggered to know if the signal was received, and reset() to forget
     */
    public static function create($signals = null, $loggerOrCallback = null)
    {
        /** @phpstan-ignore-next-line */
        $handler = new static;

        if (!function_exists('pcntl_signal')) {
            return $handler;
        }

        if ($signals === null) {
            $signals = [SIGINT, SIGTERM];
        }
        $signals = (array) $signals;

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

   /**
     * Clear all previously registered signal handlers.
     *
     * @param  string[]|int[]|null $signals
     * @return void
     */
    public function unregister($signals = null)
    {
        if (empty($signals)) {
            $signals = [SIGINT, SIGTERM];
        }

        foreach ($signals as $signal) {
            if (is_string($signal)) {
                // skip missing signals, for example OSX does not have all signals
                if (!defined($signal)) {
                    continue;
                }

                $signal = constant($signal);
            }

            pcntl_signal($signal, SIG_DFL);
        }
    }

    /**
     * @param int $signo
     * @return string
     */
    private static function getSignalName($signo)
    {
        static $signals = null;
        if ($signals === null) {
            $reflection = new \ReflectionClass(__CLASS__);
            $constants = $reflection->getConstants();
            $signals = [];
            foreach ($constants as $key => $value) {
                if (defined($value)) {
                    $signals[constant($value)] = $value;
                }
            }

        }

        if (isset($signals[$signo])) {
            return $signals[$signo];
        }

        throw new \LogicException('Unknown signal #'.$signo);
    }
}
