<?php

namespace App\Middleware;

use App\Core\MiddlewareInterface;

class Auth implements MiddlewareInterface {
  public function handle() : bool {
    return false;
  }
}