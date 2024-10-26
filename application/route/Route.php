<?php

namespace Application\Route;

require_once __DIR__ . '/../../application/Objects/RouteStruct.php';
require_once __DIR__ . '/../../application/Objects/RouteParams.php';
require_once __DIR__ . '/../../application/assets/Header/HttpHeadersManager.php';
require_once __DIR__ . '/../../application/assets/Header/HttpHeadersInterface.php';


use Application\Objects\RouteStruct;
use Application\Objects\RouteParams;
use ArgumentCountError;
use Exception;
use Application\Assets\Header\HttpHeadersManager\HttpHeadersManager;


class Route
{


    private static $routes = [];

    private static $postParams = [];

    private static $request;

    private static $requreParams = [];

    public static function initialize($request, $post)
    {
        self::$request = $request;
        self::$postParams = $post;
    }
    private static function CheckParams($p, $r): bool
    {

        return $r == array_keys($p);
    }

    public static function handle()
    {
        if (boolval(getenv(("DEVELOPER_MODE")))) {
            self::$routes[] = new RouteStruct("/developer/endpoints", [], function () {
                require_once "EndPoints.php";
                echo  setEndpointsPage(self::$routes);
            }, "GET");
        }
        $path = self::$request["REQUEST_URI"];
        $rqMethod = self::$request["REQUEST_METHOD"];
        $foud = false;

        foreach (self::$routes as $i) {
            $routeHandle = self::HandleRouteParams($path, $i->path);
            if (("/" . $routeHandle->trimmedRoute) == $path) {
                if ($rqMethod != $i->routeMethod) {
                    http_response_code(405);
                    break;
                }
                $params = [];
                if (!self::CheckParams($params, $i->requreParams)) {
                    http_response_code(400);
                    echo "Required_params:";
                    echo json_encode($i->requreParams);
                    return;
                }
                call_user_func($i->callback, $params);
                $foud = true;
            }
            foreach (HttpHeadersManager::getAllHeaders() as $key => $value) {
                header($key . ": " . $value);
            }
        }
        if (!$foud) {
            http_response_code(404);
        }
    }
    private static function HandleRouteParams($route, $routePattern)
    {

        $trimmedRoute = trim($route, "/");
        $pattern = preg_replace('/\{([^\}]+)\}/', '([^/]+)', trim($routePattern, '/'));
        $pattern = "/^" . str_replace('/', '\/', $pattern) . "$/";
        if (preg_match($pattern, $trimmedRoute, $matches)) {
            array_shift($matches);
            preg_match_all('/\{([^\}]+)\}/', $routePattern, $paramNames);
            $paramNames = $paramNames[1];

            $params = array_combine($paramNames, $matches);
            return new RouteParams($params, $trimmedRoute);
        } else {
            return false;
        }
    }


    public static function get(String $path, $reqs, $callback)
    {
        self::$routes[] = new RouteStruct($path, $reqs, $callback, "GET");
    }
    public static function post(String $path, $reqs, $callback)
    {
        self::$routes[] = new RouteStruct($path, $reqs, $callback, "POST");
    }
    public static function delete(String $path, $reqs, $callback)
    {
        self::$routes[] = new RouteStruct($path, $reqs, $callback, "DELETE");
    }
    public static function update(String $path, $reqs, $callback)
    {
        self::$routes[] = new RouteStruct($path, $reqs, $callback, "UPDATE");
    }
}
