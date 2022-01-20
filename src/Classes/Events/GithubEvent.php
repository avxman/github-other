<?php

namespace Avxman\Github\Classes\Events;

class GithubEvent extends BaseEvent
{

    protected function default(array $data) : void{
        $this->log->write('Event: '.($this->server['HTTP_X_GITHUB_EVENT']??'Default'));
        $this->command('git --version');
    }

    protected function ping(array $data) : void{
        $this->log->write('Event: '.($this->server['HTTP_X_GITHUB_EVENT']??'Ping'));
    }

    protected function push(array $data) : void{
        $result = $this->command('git pull');
        $this->log->write('Event: '.($this->server['HTTP_X_GITHUB_EVENT']??'Push'));
    }

    public function events(array $data) : bool{

        $this->is_event = true;
        $event = strtolower($this->server['HTTP_X_GITHUB_EVENT']??'default');
        $this->{$event}($data);

        return $this->is_event;

    }

}
