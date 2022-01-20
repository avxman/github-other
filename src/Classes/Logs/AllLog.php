<?php

namespace Avxman\Github\Classes\Logs;

use Avxman\Github\Messages\GithubMessage;

class AllLog
{

    protected array $config = [];
    protected string $dir = '';
    protected string $name = 'github.log';
    protected string $full_name = '';
    protected int $size = 1024000;
    protected GithubMessage $message;
    protected string $date_format = 'd.m.Y H:i:s';

    protected function getDate() : string{
        return date($this->date_format);
    }

    protected function rewrite() : bool{
        return !file_exists($this->full_name) || filesize($this->full_name) >= $this->size;
    }

    public function __construct(array $config){

        $this->config = $config;
        $this->dir = dirname(__DIR__, 3);
        $this->full_name = $this->dir.'/'.$this->name;
        $this->message = new GithubMessage($this->config['IS_DEBUG']??false);

    }

    public function write(string $text) : void{
        if(file_put_contents($this->full_name, $this->getDate().': '.$text.PHP_EOL, $this->rewrite() ? FILE_TEXT : FILE_APPEND) === FALSE){
            $this->message->setMessage('Не удалось сохранить данные в лог файл')->error();
        }
    }

    public function read() : string{
        if(($file = file_get_contents($this->full_name)) === FALSE){
            $this->message->setMessage('Не удалось открыть файл логов')->error();
        }
        return $file;
    }

}
