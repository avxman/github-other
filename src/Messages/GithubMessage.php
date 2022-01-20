<?php

namespace Avxman\Github\Messages;

class GithubMessage
{

    protected bool $is_debug = false;
    protected array $messages = [];

    protected function themeHandler(int $type = 0, string $body = '', string $file = '', int $line = 0) : string{
        return "<!DOCTYPE html>
<html>
    <head>
        <title>Server Error Custom</title>
    </head>
    <body>
        <h1 style=\"text-align: center\">Server Error Custom</h1>
        <div style=\"display: flex;flex-direction: column;\">
            <div><strong>ОШИБКА:</strong> <span style=\"color: red\">$body</span></div>
            <div><strong>СТРОКА:</strong> $line</div>
            <div><strong>ФАЙЛ:</strong> $file</div>
        </div>
    </body>
</html>
";
    }

    protected function themeException(string $body = '') : string{
        return "<!DOCTYPE html>
<html>
    <head>
        <title>Server Error</title>
    </head>
    <body>
        <h1 style=\"text-align: center\">Server Error</h1>
        <ul style=\"list-style: none\">
            $body
        </ul>
    </body>
</html>
";
    }

    protected function errorHandler(int $errType, string $errText, string $errFile, int $errLine) : void{
        if($errType && !error_reporting(E_ERROR | E_WARNING)){
            return;
        }

        header('Content-Type: text/html; charset=utf-8');

        echo $this->themeHandler($errType, $errText, $errFile, $errLine);

        exit;
    }

    protected function exceptionHandler($e) : void{
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: text/html; charset=utf-8');
        echo $this->themeException($e->getMessage());
        exit;
        // header('Content-Type: application/json; charset=utf-8');
        // echo json_encode(["error"=>"Server Error:<br>".$e->getMessage()]);
    }

    protected function getMessage(int $index = 0) : string{
        return $this->messages[$index]??"Не известная ошибка";
    }

    public function __construct(bool $debug){

        $this->is_debug = $debug;

        $self = $this;

        set_error_handler(static function (int $errType, string $errText, string $errFile, int $errLine) use ($self){
            $self->errorHandler($errType, $errText, $errFile, $errLine);
        });

        set_exception_handler(static function($e) use ($self){
            $self->exceptionHandler($e);
        });

    }

    public function setMessage(string $message = "") : self{
        $this->messages[] = $message;
        return $this;
    }

    public function setMessages(array $messages = []) : self{
        $this->messages = $messages;
        return $this;
    }

    /**
     * @throws \ErrorException
     */
    public function error() : void{
        throw new \ErrorException($this->getMessage());
    }

    /**
     * @throws \ErrorException
     */
    public function errors() : void{
        $messages = "";
        $i = 1;
        foreach ($this->messages as $message){
            $messages .= "<li>$i. $message</li>".PHP_EOL;
            $i++;
        }
        throw new \ErrorException($messages);
    }

}
