<?php

namespace App\Core;

class MiddlewareHandler {

  public static function run(array $middlewareList) {

    foreach($middlewareList as $middlewareClass) {
      
      if(!class_exists($middlewareClass)) {
        http_response_code(500);
        echo "Middleware class not found: {$middlewareClass}";
        return false;
      }

      $middleware = new $middlewareClass();

      if(!$middleware instanceof MiddlewareInterface) {
        http_response_code(500);
        echo "Invalid middleware: $middlewareClass";
        return false;
      }

      if(!$middleware->handle()) {
        return false;
      }
    }

    return true;
  }
}