<?php

namespace Application\Objects;

class RouteStruct
{
    public $path;
    public $callback;
    public $requreParams;

    public $routeMethod;
    public function __construct($path, $requreParams, $callback, $routeMethod)
    {
        $this->path = $path;
        $this->callback = $callback;
        $this->routeMethod = $routeMethod;
        $this->requreParams = $requreParams;
    }
}
