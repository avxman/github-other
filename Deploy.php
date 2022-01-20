<?php

error_reporting(0);

class Deploy{

    public const DIR = __DIR__;
    public \Avxman\Github\Messages\GithubMessage $message;
    protected string $path_src = self::DIR.'/src';
    protected array $config = [];
    protected array $server = [];
    protected bool $isError = false;
    protected array $errorMessage = [];

    protected function requiredFiles(string $file) : void{
        require_once $file;
    }

    protected function recursiveFolder(string $folder) : void{
        $dir = scandir($folder);
        foreach ($dir??[] as $file){
            if($file === '.' || $file === '..') {
                continue;
            }
            $path = $folder.'/'.$file;
            if(is_dir($path)) {
                $this->recursiveFolder($path);
            }
            elseif (file_exists($path)){
                $this->requiredFiles($path);
            }
        }
    }

    protected function includeConfig() : void{
        $this->config = require self::DIR.'/config/github.php';
        if(!count($this->config)) {
            $this->isError = true;
            $this->errorMessage[] = "Не найден конфигурационный файл.";
        }
        elseif (!$this->config['GITHUB_TOKEN'] || empty($this->config['GITHUB_TOKEN'])){
            $this->isError = true;
            $this->errorMessage[] = empty($this->config['GITHUB_TOKEN'])
                ? "Токен пустой"
                : "Не найден токен";
        }
        elseif (!$this->config['IS_DEBUG'] || empty($this->config['IS_DEBUG'])){
            $this->isError = true;
            $this->errorMessage[] = empty($this->config['IS_DEBUG'])
                ? "Дебагер пуст"
                : "Не найден ключ дебагер";
        }
    }

    protected function includeMessage() : void{
        $this->message = new \Avxman\Github\Messages\GithubMessage($this->config['IS_DEBUG']??false);
        $this->message->setMessages($this->errorMessage);
    }

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->recursiveFolder($this->path_src);
        $this->includeConfig();
        $this->includeMessage();
    }

    public function isError() : bool{
        return $this->isError;
    }

    public function dispatcher() : void{
        (new \Avxman\Github\Providers\GithubServiceProvider)->register($this->server, $this->config);
    }

}

$deploy = new Deploy();

if($deploy->isError()) {
    $deploy->message->errors();
}

$deploy->dispatcher();
