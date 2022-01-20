<?php

namespace Avxman\Github\Classes\Connections;

class GithubConnection extends BaseConnection
{

    public function validation() : bool{

        if (!extension_loaded('hash')) {
            $this->errorMessage[] = "Missing 'hash' php extension to check the secret code validity.";
            return false;
        }

        if(!function_exists('shell_exec')){
            $this->errorMessage[] = "Missing 'shell_exec' php global function is disabled.";
            return false;
        }

        if(!$this->config['HTTP_X_GITHUB_SECRET']||empty($this->config['HTTP_X_GITHUB_SECRET'])){
            $this->errorMessage[] = "Github secret is empty.";
            return false;
        }

        if (!($this->server['HTTP_X_HUB_SIGNATURE']??false)) {
            $this->errorMessage[] = "HTTP header 'X-Hub-Signature' is missing.";
            return false;
        }

        if (!($this->server['HTTP_X_GITHUB_EVENT']??false)) {
            $this->errorMessage[] = "Missing HTTP 'X-Github-Event' header.";
            return false;
        }

        if (!($this->server['CONTENT_TYPE']??false)) {
            $this->errorMessage[] = "Missing HTTP 'Content-Type' header.";
            return false;
        }

        [$algo, $hash] = explode('=', $this->server['HTTP_X_HUB_SIGNATURE'], 2) + ['', ''];

        if (!in_array($algo, hash_algos(), TRUE)) {
            $this->errorMessage[] = "Hash algorithm '$algo' is not supported.";
            return false;
        }

        $input = file_get_contents('php://input');

        $secret = $this->config['HTTP_X_GITHUB_SECRET'];

        if (!hash_equals($hash, hash_hmac($algo, $input, $secret))) {
            $this->errorMessage[] = "Hook secret does not match.";
            return false;
        }

        return true;

    }

    public function getData(): array
    {

        $type = $this->server['CONTENT_TYPE']??'default';

        $json = match ($type) {
            'application/json' => file_get_contents('php://input'),
            'application/x-www-form-urlencoded' => $_POST['payload'],
            default => false,
        };

        if($json === FALSE) {
            $this->errorMessage[] = "Unsupported content type: ".$type;
            $json = json_encode([]);
        }

        return $this->data = json_decode($json);

    }

}
