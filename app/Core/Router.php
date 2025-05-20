<?php

namespace App\Core;

use App\Core\MiddlewareHandler;

class Router {
  private static array $routes = [];
  private static array $middlewareMap = [];
  private static ?string $lastRouteMethod = null;
  private static ?string $lastRoutePath = null;

  public static function get(string $path, callable|array $callback) : self {
    self::$lastRouteMethod = 'GET';
    self::$lastRoutePath = $path;
    self::$routes['GET'][$path] = $callback;
    return new self();
  }

  public static function post(string $path, callable|array $callback) : self {
    self::$lastRouteMethod = 'POST';
    self::$lastRoutePath = $path;
    self::$routes['POST'][$path] = $callback;
    return new self();
  }

  public static function middleware(array $middleware = []) {
    if(self::$lastRouteMethod) {
      self::$middlewareMap[self::$lastRouteMethod][self::$lastRoutePath] = $middleware;

      // Reset variable to prevent unexpected error
      self::$lastRouteMethod = null;
      self::$lastRoutePath = null;
    }
    // return new self();
  }

  public static function dispatch(string $requestUri) {
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($requestUri, PHP_URL_PATH);

    if(isset(self::$routes[$method][$path])) {
      // print_r(self::$routes);die;
      $callback = self::$routes[$method][$path];
      // print_r($callback);die;
      if(is_callable($callback)) {
        $callback();
      } else if (is_array($callback) && count($callback) === 2) {

        // Implement middleware
        if(!empty(self::$middlewareMap)) {
          $middleware = self::$middlewareMap[$method][$path];
          if(!MiddlewareHandler::run($middleware)) {
            http_response_code(500);
            echo "500 interner server error <br> Middleware not found: {$middleware}";
            return;
          }
        }

        // Get class and method name
        [$class, $methodName] = $callback;

        // Check class exist or not
        if(!class_exists($class)) {
          http_response_code(500);
          echo "500 interner server error <br> Controller not found: {$class}";
          return;
        }

         // Check method exist or not
        if(!method_exists($class, $methodName)) {
          http_response_code(500);
          echo "500 interner server error <br> Method not found: {$class}@{$methodName}";
          return;
        }

        // create the instance of class
        $controller = new $class();
        // invoke the specific method of class
        call_user_func([$controller, $methodName]);
        
      } else {
        http_response_code(500);
        echo "Invalid route handler";
        return;
      }
    } else {
      http_response_code(404);
      echo "404 Not Found: {$method} {$path}";
      return;
    }
  }
}