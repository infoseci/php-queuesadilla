<?php

namespace josegonzalez\Queuesadilla;

use \josegonzalez\Queuesadilla\Job;

abstract class Backend
{
    protected $baseConfig = array(array(
        'queue' => 'default',
    ));

    protected $connected = false;

    protected $connection = null;

    protected $settings = array();

    public function __construct($config)
    {
        $this->settings = array_merge($this->baseConfig, $config);
        return $this->connected = $this->connect();
    }

    public function bulk($jobs, $vars = array(), $queue = null)
    {
        $queue = $this->getQueue($queue);
        $return = array();
        foreach ((array)$jobs as $callable) {
            $return[] = $this->push($callable, $vars, $queue);
        }

        return $return;
    }

    public function getJobClass()
    {
        return '\\josegonzalez\\Queuesadilla\\Job';
    }

    public function getQueue($queue = null)
    {
        if ($queue === null) {
            $queue = $this->settings['queue'];
        }

        return $queue;
    }

    public function setting($settings, $key, $default = null)
    {
        if (!is_array($settings)) {
            $settings = array('queue' => $settings);
        }

        $settings = array_merge($this->settings, $settings);

        if (isset($settings[$key])) {
            $value = $settings[$key];
        } else {
            $value = $default;
        }

        return $value;
    }

    public function watch($queue = null)
    {
        $queue = $this->getQueue($queue);
        return true;
    }

    public function connected()
    {
        return $this->connected;
    }

    abstract public function connect();

    abstract public function delete($item);

    abstract public function pop($queue = null);

    abstract public function push($class, $vars = array(), $queue = null);

    abstract public function release($item, $queue = null);
}
