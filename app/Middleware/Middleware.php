<?php
namespace Nurdin\Djawara\Middleware;

interface Middleware
{
    public function auth() : void;
}

?>