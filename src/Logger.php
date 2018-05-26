<?php

namespace Crgeary\JAMstackDeployments;

class Logger
{
    /**
     * Path the debug file
     * 
     * @var string
     */
    protected $path;

    /**
     * Number of lines to keep in the file
     * 
     * @var integer
     */
    protected $count = 250;

    /**
     * Create a new Logger
     * 
     * @param string $path
     * @param integer $count
     */
    public function __construct($path, $count = 250)
    {
        $this->path = $path;
        $this->count = (int)$count;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    protected function log($level, $message, array $context = [])
    {
        if (!file_exists($this->path)) {
            touch($this->path);
        }
        
        $items = array_slice(file($this->path), $this->getLineCount());
        $items = array_map('trim', $items);

        $items[] = $this->createEntry($level, $this->interpolate($message, $context));

        file_put_contents($this->path, implode("\n", $items));
    }

    /**
     * Create a single entry to be inserted into the file
     * 
     * @param string $type
     * @param string $message
     * @return string
     */
    protected function createEntry($type, $message)
    {
        $time = (new \DateTime())->format('Y-m-d H:i:s');

        return json_encode(compact('type', 'time', 'message'));
    }

    /**
     * Get the number of lines
     * 
     * @return integer
     */
    protected function getLineCount()
    {
        return ($this->count - 1) * -1;
    }

    /**
     * Replace tags in each entry/message
     * 
     * @param string $message
     * @param array $context
     * @return string
     */
    protected function interpolate($message, array $context = [])
    {
        $replace = [];

        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        return strtr($message, $replace);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function error($message, array $context = [])
    {
        $this->log('error', $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function warning($message, array $context = [])
    {
        $this->log('warning', $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function info($message, array $context = [])
    {
        $this->log('info', $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function debug($message, array $context = [])
    {
        $this->log('debug', $message, $context);
    }
}
