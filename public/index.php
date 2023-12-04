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
// use src\middlewares\SocioMiddleware;
// // use src\middlewares\MozoMiddleware;
// use src\middlewares\AuthMiddleware;



require __DIR__ . '/../vendor/autoload.php';
require_once '../src/AccesoDatos.php';
require_once '../src/Controllers/UsuariosController.php';
require_once '../src/Controllers/MesasController.php';
require_once '../src/Controllers/ProductosController.php';
require_once '../src/Controllers/PedidosController.php';
require_once '../src/Controllers/ComandasController.php';
require_once '../src/Middlewares/AuthMiddleware.php';
require_once '../src/Middlewares/SocioMiddleware.php';
require_once '../src/Middlewares/MozoMiddleware.php';
require_once '../src/Middlewares/CocineroMiddleware.php';
require_once '../src/Middlewares/CerveceroMiddleware.php';
require_once '../src/Middlewares/CandyBarMiddleware.php';
// Instantiate app
$app = AppFactory::create();

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

$app->group('/usuarios', function (RouteCollectorProxy $group) {

  //ok visto
  $group->post('/login', UsuariosController::class . ':POST_Login');

  //ok visto
  $group->get('/listar', UsuariosController::class . ':GET_TraerTodos')
  ->add(new SocioMiddleware())
  ->add(new AuthMiddleware());
  
   //ok visto
  $group->get('/listarId', UsuariosController::class . ':GET_TraerUsuarioId')
  ->add(new SocioMiddleware())
  ->add(new AuthMiddleware());
   
  //ok visto
  $group->post('/insertar', UsuariosController::class . ':POST_InsertarUsuario')
  ->add(new SocioMiddleware())
  ->add(new AuthMiddleware());
 
   
  $group->get('/guardarUsuarioCsv', UsuariosController::class . ':GET_GuardarEnCSV');
  $group->get('/leerUsuarioDeCsv', UsuariosController::class . ':GET_CargarUsuariosCSV');
});



//ok visto
$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->post('/insertar', ProductosController::class . ':POST_insertarProducto');
  $group->get('/listarTodos', ProductosController::class . ':GET_traerTodos');
  $group->get('/listarIndividual', ProductosController::class . ':GET_TraerProductoId');

});

$app->group('/mesas', function (RouteCollectorProxy $group) {

  //ok visto
  $group->post('/insertar', MesasController::class . ':POST_alta_de_mesa');

  //ok visto
  $group->get('/listar', MesasController::class . ':GET_traerTodos')
  ->add(new SocioMiddleware())
  ->add(new AuthMiddleware());

  //ok visto
  $group->post('/cambiarEstado', MesasController::class . ':POST_cambiar_estado_de_mesa')
  ->add(new SocioMiddleware())
  ->add(new AuthMiddleware());


  $group->post('/cerrarMesa', MesasController::class . ':POST_cerrar_mesa')
  ->add(new SocioMiddleware())
  ->add(new AuthMiddleware());
 
  
});


$app->group('/pedidos', function (RouteCollectorProxy $group) {

  $group->post('/alta', PedidosController::class . ':POST_alta_pedido')
  ->add(new MozoMiddleware())
  ->add(new AuthMiddleware());


  $group->get('/listar', PedidosController::class . ':GET_TraerTodos');

  $group->get('/consultarTiempo', PedidosController::class . ':GET_ConsultarTiempo')
  ->add(new SocioMiddleware())
  ->add(new AuthMiddleware());


  $group->get('/consultarEstadoSector', PedidosController::class . ':GET_Listar_pedidos_segun_estado');


  //ok visto
  $group->get('/consultarTiempoCliente', PedidosController::class . ':GET_ConsultarTiempoCliente');


  $group->get('/listarEstadoEmp', PedidosController::class . ':GET_Listar_pedidos_segun_estado');
  $group->get('/listarEstadoSocio', PedidosController::class . ':GET_Listar_pedidos_segun_estado');
 
  //ok visto
  $group->post('/cambiarEstado', PedidosController::class . ':POST_cambiar_estado_pedido');

  //ok visto
  $group->post('/cambiarEstadoProductoCocina', PedidosController::class . ':POST_cambiar_estado_pedido2')
  ->add(new CocineroMiddleware())
  ->add(new AuthMiddleware());

  //ok visto
  $group->post('/cambiarEstadoProductoCerveza', PedidosController::class . ':POST_cambiar_estado_pedido2')
  ->add(new CerveceroMiddleware())
  ->add(new AuthMiddleware());

  //ok visto
  $group->post('/cambiarEstadoProductoCandyBar', PedidosController::class . ':POST_cambiar_estado_pedido2')
  ->add(new CandyBarMiddleware())
  ->add(new AuthMiddleware());



});


$app->group('/comandas', function (RouteCollectorProxy $group) {
  $group->post('/alta', ComandasController::class . ':POST_alta_comanda');
  $group->get('/listar', ComandasController::class . ':GET_TraerTodos');
  $group->get('/pedidosListosCambiarEstado', ComandasController::class . ':GET_Listar_pedidos_listos_cambiar_estado_mesa')
  ->add(new MozoMiddleware())
  ->add(new AuthMiddleware());

  $group->get('/listarMejorPuntuaciones', ComandasController::class . ':GET_ListarMejoresPuntuaciones')
  ->add(new SocioMiddleware())
  ->add(new AuthMiddleware());

  //ok visto
  $group->get('/mesaMasUsada', ComandasController::class . ':GET_MesaMasUsada')
  ->add(new SocioMiddleware())
  ->add(new AuthMiddleware());

  //ok visto
  $group->post('/verTiempo', ComandasController::class . ':POST_Ver_tiempo_restante')
  ->add(new SocioMiddleware())
  ->add(new AuthMiddleware());

  $group->post('/guardarImagen', ComandasController::class . ':POST_Guardar_imagen');


  //ok visto
  $group->post('/cobrarComanda', ComandasController::class . ':POST_CobrarComanda');

  //ok visto
  $group->post('/encuestas', ComandasController::class . ':POST_ClienteEncuesta');
  
});



//Run application
$app->run();
