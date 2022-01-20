<?php

namespace Avxman\Github\Classes\Connections;

abstract class BaseConnection
{

    protected bool $is_connect = false;
    protected array $data = [];
    protected array $config = [];
    protected array $server = [];
    protected array $errorMessage = [];

    protected function validation() : bool{
        return false;
    }

    protected function connect() : bool{
        return $this->is_connect = $this->validation();
    }

    public function __construct(array $server = [], array $config = []){
        $this->server = $server;
        $this->config = $config;
        $this->connect();
    }

    public function isConnect() : bool{
        return $this->is_connect;
    }

    public function errorMessage() : array{
        return $this->errorMessage;
    }

    public function getData() : array{
        return $this->data;
    }

}
