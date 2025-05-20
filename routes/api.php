<?php 

use App\Core\Router;
use App\Controllers\{
  HomeController
};
use App\Middleware\{
  Auth
};

Router::get('/', function() {
  echo 'routes invoked';
});

Router::get('/home', [HomeController::class, 'index'])->middleware([Auth::class]);
// Router::middleware([Auth::class])->get('/home', [HomeController::class, 'index']);

Router::post('/home', function() {
  echo 'This is post home';
});