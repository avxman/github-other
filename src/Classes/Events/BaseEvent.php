<?php

namespace Avxman\Github\Classes\Events;

use Avxman\Github\Classes\Logs\AllLog;

abstract class BaseEvent
{

    public bool $is_event = false;
    protected AllLog $log;
    protected array $config = [];
    protected array $server = [];

    protected function command(string $command = '') : string{
        $result = shell_exec($command);
        return (is_null($result) || is_bool($result)) ? "" : $result;
    }

    public function __call(string $name, array $arguments)
    {
        if(function_exists($name)){
            $this->{$name}($arguments);
        }
        $this->is_event = false;
    }

    public function __construct(array $server, array $config){
        $this->server = $server;
        $this->config = $config;
        $this->log = new AllLog($config);
    }

    public function events(array $data) : bool{
        return false;
    }

}
