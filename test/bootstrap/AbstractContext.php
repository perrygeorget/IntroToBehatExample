<?php
/**
 * An extension to the RawMinkContext that has some useful helper functions.
 */

use Behat\MinkExtension\Context\RawMinkContext;

abstract class AbstractContext extends RawMinkContext
{
    /**
     * Causes the sleep until a condition is satisfied by the function $lambda when it returns a true value.
     *
     * @param Callable $lambda function to run
     * @param int $wait time to wait for timeout, in milliseconds
     * @param int $pause time to pause between iterations, in milliseconds
     *
     * @return bool
     * @throws \Exception (when timeout occurs)
     */
    public function spin($lambda, $wait = 5000, $pause = 250)
    {
        $e = null;

        $time_start = microtime(true);
        $time_end = $time_start + $wait / 1000.0;
        while (microtime(true) < $time_end) {
            $e = null;

            try {
                if ($lambda($this)) {
                    return true;
                }
            } catch (\Exception $e) {
                // do nothing
            }

            usleep($pause);
        }

        $backtrace = debug_backtrace();

        if (!array_key_exists('file', $backtrace[1])) {
            $backtrace[1]['file'] = '[unknown_file]';
        }

        if (!array_key_exists('line', $backtrace[1])) {
            $backtrace[1]['line'] = '[unknown_line]';
        }


        $message = "Timeout thrown by " . $backtrace[1]['class'] . "::" . $backtrace[1]['function'] . "()\n" .
            $backtrace[1]['file'] . ", line " . $backtrace[1]['line'];

        throw new \Exception($message, 0, $e);
    }

    public function waitFor($type, $value, $timeout = 5000)
    {
        $session = $this->getSession();

        $this->spin(
            function () use ($session, $type, $value) {
                if ($session->getPage()->find($type, $value)) {
                    return true;
                }
                return false;
            },
            $timeout
        );
    }

    public function waitForVisible($type, $value, $timeout = 5000, $visible = true)
    {
        $session = $this->getSession();

        $this->spin(
            function () use ($session, $type, $value, $visible) {
                $element = $session->getPage()->find($type, $value);
                if ($element) {
                    if ($visible && $element->isVisible()) {
                        return true;
                    } elseif (!$visible && !$element->isVisible()) {
                        return true;
                    }
                    return false;
                } else {
                    // If we did not get an element, that is ok if it should not be visible.;
                    return !$visible;
                }
            },
            $timeout
        );
    }

    public function waitForXpath($xpath, $timeout = 5000)
    {
        $this->waitFor('xpath', $xpath, $timeout);
    }

    public function waitForLink($link_name, $timeout = 5000)
    {
        $this->waitFor('named', array('link', '"' . $link_name . '"'), $timeout);
    }

    public function waitForButton($button_name, $timeout = 5000)
    {
        $this->waitFor('named', array('button', '"' . $button_name . '"'), $timeout);
    }

    public function waitForId($id, $timeout = 5000)
    {
        $this->waitForXpath('//*[@id="' . $id . '"]', $timeout);
    }

    public function waitForContent($content, $timeout = 5000)
    {
        $this->waitForXpath('//*[contains(text(), "' . $content . '")]', $timeout);
    }

    public function waitForPageToLoad($timeout = 5000)
    {
        $session = $this->getSession();
        $this->spin(
            function () use ($session) {
                $readyState = $session->evaluateScript("return document.readyState;");
                return $readyState == 'complete';
            }, $timeout
        );
    }
}