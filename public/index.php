<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use src\Controllers\UsuariosController;
use src\Controllers\ProductosController;
use src\Controllers\MesasController;



require __DIR__ . '/../vendor/autoload.php';
require '../src/AccesoDatos.php';
require '../src/Controllers/UsuariosController.php';
require '../src/Controllers/ProductosController.php';
require_once '../src/Controllers/MesasController.php';
// Instantiate <app></app>
$app = AppFactory::create();

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

// Add parse body
$app->addBodyParsingMiddleware();



  $app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->post('/insertar', UsuariosController::class . ':POST_insertarUsuario');
    $group->get('/mostrarTodos', UsuariosController::class . ':GET_traerTodos');
   
    // echo'funciona';
  });

  
 
  $app->group('/productos', function (RouteCollectorProxy $group) {
    $group->post('/insertar', ProductosController::class . ':POST_insertarProducto');
    $group->get('/mostrarTodos', ProductosController::class . ':GET_traerTodos');

  });

  $app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->post('/insertar', MesasController::class . ':POST_altaMesa');
    $group->get('/mostrarTodos', MesasController::class . ':POST_traerTodos');
    
  });



//   $app->group('/pedidos', function (RouteCollectorProxy $group) {
//     if(!isset($_GET['accion'])){
//       $group->get('[/]', UsuariosController::class . ':ErrorDatos');
//       $group->post('[/]', UsuariosController::class . ':ErrorDatos');
//     }
//     else{
//       switch($_GET['accion']){
          
//       }
//     }
//   });


//   $app->group('/comandas', function (RouteCollectorProxy $group) {
//     if(!isset($_GET['accion'])){
//       $group->get('[/]', UsuariosController::class . ':ErrorDatos');
//       $group->post('[/]', UsuariosController::class . ':ErrorDatos');
//     }
//     else{
//       switch($_GET['accion']){
     
//       }
//     }
//   });



// Run application
$app->run();

?>