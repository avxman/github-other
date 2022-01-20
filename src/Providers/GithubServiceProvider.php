<?php

namespace Avxman\Github\Providers;

use Avxman\Github\Classes\GithubClass;

class GithubServiceProvider
{

    /**
     * @throws \Exception
     */
    public function register(array $server = [], array $config = []) : void
    {
        $github = new GithubClass($server, $config);
        $github->instance();
    }

}
