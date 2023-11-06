<?php
// namespace src\Controllers;
// use Psr\Http\Message\ResponseInterface as Response;
// use Psr\Http\Message\ServerRequestInterface as Request;
// use Mesa;
// // use Autenticador;
// require_once '../src/Clases/Pedido.php';
// // require_once '../src/Clases/Usuario.php';
// // require_once '../src/Clases/Autenticador.php';

// class pedidoController{


    
//     public function CargarPedido($request, $response, $args)
//     {
//         $parametros = $request->getParsedBody();
//         $idProducto = Producto::GetProductoPorId($parametros['idProducto']);
//         $cantidadProductos = $parametros['cantidadProductos'];
//         $idMesa = Mesa::GetMesaPorId($parametros['idMesa']);
//         $codigoPedido = $parametros['codigoPedido'];
    
//         if($idProducto != null && $idMesa != null)
//         {
//             $pedido = new Pedido();
//             $pedido->idEmpleado = 0;
//             $pedido->idProducto = $parametros['idProducto'];
//             $pedido->cantidadProductos = $cantidadProductos;
//             $pedido->idMesa = $parametros['idMesa'];
//             $pedido->estado = "Pendiente";
//             $pedido->codigoPedido = $codigoPedido;
//             $pedido->tiempoPreparacion = 0;
//             date_default_timezone_set('America/Argentina/Buenos_Aires');
//             $pedido->horaCreacion = new DateTime(date("h:i:sa"));
//             if(file_exists($_FILES["fotoMesa"]["tmp_name"]))
//             {
//                 $pedido->fotoMesa = $this->MoverFoto($pedido->codigoPedido);
//             }
//             $pedido->AltaPedido();
//             $payload = json_encode(array("Mensaje" => "Pedido creado con exito"));
//         }
//         else
//         {
//             $payload = json_encode(array("Mensaje" => "El producto o la mesa no existen!"));
//         }
    
//         $response->getBody()->write($payload);
//         return $response->withHeader('Content-Type', 'application/json');
//     }    



// }



?>