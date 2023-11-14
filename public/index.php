<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use src\Controllers\UsuariosController;
use src\Controllers\MesasController;
use src\Controllers\ProductosController;
use src\Controllers\PedidosController;
use src\Controllers\ComandasController;

require __DIR__ . '/../vendor/autoload.php';
require_once '../src/AccesoDatos.php';
require_once '../src/Controllers/UsuariosController.php';
require_once '../src/Controllers/MesasController.php';
require_once '../src/Controllers/ProductosController.php';
require_once '../src/middlewares/LoginMiddlewareEspecifico.php';
// Instantiate app
$app = AppFactory::create();

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->post('/login', UsuariosController::class . ':POST_Login');
  $group->get('/listar', UsuariosController::class . ':GET_TraerTodos')
      ->add(new LoginMiddlewareEspecifico("Admin"));
  $group->post('/insertar', UsuariosController::class . ':POST_InsertarUsuario')
      ->add(new LoginMiddlewareEspecifico("Admin"));
});




$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->post('/insertar', ProductosController::class . ':POST_insertarProducto');
  $group->get('/listar', ProductosController::class . ':GET_traerTodos');

});

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->post('/insertar', MesasController::class . ':POST_alta_de_mesa');
  $group->get('/listar', MesasController::class . ':GET_traerTodos');
  $group->post('/cambiarEstado', MesasController::class . ':POST_cambiar_estado_de_mesa');
  
});


$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->post('[/]', PedidosController::class . ':POST_AltaPedido');
  $group->get('[/listar]', PedidosController::class . ':GET_TraerTodos');
  $group->get('[/consultarTiempo]', PedidosController::class . ':GET_ConsultarTiempo');
  $group->get('[/listaPendientes]', PedidosController::class . ':GET_Listar_pedidos_segun_estado');
  $group->post('[/cambiarEstado]', PedidosController::class . ':POST_cambiar_estado_pedido');
  
});



//Run application
$app->run();
