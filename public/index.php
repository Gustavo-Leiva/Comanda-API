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
require_once '../src/Controllers/PedidosController.php';
require_once '../src/Controllers/ComandasController.php';
require_once '../src/middlewares/LoginMiddlewareEspecifico.php';
// Instantiate app
$app = AppFactory::create();

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->post('/login', UsuariosController::class . ':POST_Login');
  $group->get('/listar', UsuariosController::class . ':GET_TraerTodos')
      ->add(new LoginMiddlewareEspecifico("Admin"));
  $group->get('/listarId', UsuariosController::class . ':GET_TraerUsuarioId')
    ->add(new LoginMiddlewareEspecifico("Admin"));    
  $group->post('/insertar', UsuariosController::class . ':POST_InsertarUsuario')
      ->add(new LoginMiddlewareEspecifico("Admin"));
  $group->get('/guardarUsuarioCsv', UsuariosController::class . ':GET_GuardarEnCSV');
  $group->get('/leerUsuarioDeCsv', UsuariosController::class . ':GET_CargarUsuariosCSV');
});




$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->post('/insertar', ProductosController::class . ':POST_insertarProducto');
  $group->get('/listarTodos', ProductosController::class . ':GET_traerTodos');
  $group->get('/listarIndividual', ProductosController::class . ':GET_TraerProductoId');

});

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->post('/insertar', MesasController::class . ':POST_alta_de_mesa');
  $group->get('/listar', MesasController::class . ':GET_traerTodos');
  $group->post('/cambiarEstado', MesasController::class . ':POST_cambiar_estado_de_mesa')
  ->add(new LoginMiddlewareEspecifico("Admin"));
  
});


$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->post('/alta', PedidosController::class . ':POST_alta_pedido');
  $group->get('/listar', PedidosController::class . ':GET_TraerTodos');
  $group->get('/consultarTiempo', PedidosController::class . ':GET_ConsultarTiempo');
  $group->get('/listarEstadoEmp', PedidosController::class . ':GET_Listar_pedidos_segun_estado');
  $group->get('/listarEstadoSocio', PedidosController::class . ':GET_Listar_pedidos_segun_estado')
  ->add(new LoginMiddlewareEspecifico("Admin"));
  $group->post('/cambiarEstado', PedidosController::class . ':POST_cambiar_estado_pedido');
  
});


$app->group('/comandas', function (RouteCollectorProxy $group) {
  $group->post('/alta', ComandasController::class . ':POST_alta_comanda');
  $group->get('/listar', ComandasController::class . ':GET_TraerTodos');
  $group->get('/consultarTiempo', ComandasController::class . ':GET_Listar_pedidos_listos_cambiar_estado_mesa');
  $group->get('/listarMejorPuntuaciones', ComandasController::class . ':GET_ListarMejoresPuntuaciones')
  ->add(new LoginMiddlewareEspecifico("Admin"));
  $group->get('/mesaMasUsada', ComandasController::class . ':GET_MesaMasUsada');
  $group->post('/verTiempo', ComandasController::class . ':POST_Ver_tiempo_restante');
  $group->post('/guardarImagen', ComandasController::class . ':POST_Guardar_imagen');
  $group->post('/cobrarComanda', ComandasController::class . ':POST_CobrarComanda');
  $group->post('/encuestas', ComandasController::class . ':POST_ClienteEncuesta');
  
});



//Run application
$app->run();
