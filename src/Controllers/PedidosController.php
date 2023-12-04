<?php
namespace src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use AutentificadorJWT;
use Pedido;
use Usuario;

require_once '../src/Clases/Pedido.php';
require_once '../src/Clases/Usuario.php';
require_once '../src/Clases/AutentificadorJWT.php';

class PedidosController
{
    //ok visto
    public static function GET_TraerTodos(Request $request, Response $response, array $args){
     
                $pedidos = Pedido::obtener_todos_los_pedidos();
                $pedidosMapp = Pedido::mapear_para_mostrar($pedidos);
                $respuesta = json_encode(array("Listado_de_pedidos"=>$pedidosMapp));          
                $response->getBody()->write($respuesta);
                return $response;
        
    }


    //ok visto
    public static function POST_alta_pedido(Request $request, Response $response, array $args)
    {
        $parametros = $request->getParsedBody();

        $idMesa = isset($parametros['idMesa']) ? $parametros['idMesa'] : null;
        $idMozo = isset($parametros['idMozo']) ? $parametros['idMozo']: null;
        $pedido = new Pedido(null, null,null,$idMesa,$idMozo);
    
        if (isset($parametros['items'])) {
            $cadena_items = $parametros['items'];
            $elementos = explode(",", $cadena_items);
    
            foreach ($elementos as $i) {
                // echo $i;
                $pedido->cargar_nuevo_item($i);
            }
    
            $id_insertado = $pedido->alta_pedido();
    
            if ($id_insertado != null) {
                $retorno = json_encode(array("mensaje" => "Pedido creado con éxito"));
            } else {
                $retorno = json_encode(array("mensaje" => "No se pudo crear el pedido"));
            }
        } else {
            $retorno = json_encode(array("mensaje" => "Error! Carga de datos inválida. Debe proporcionar items en el cuerpo de la solicitud."));
        }
    
        $response->getBody()->write($retorno);
        return $response;
    }
    




//ok funciona
    public static function POST_cambiar_estado_pedido(Request $request, Response $response, array $args)
{
    $parametros = $request->getParsedBody();
    $numero_pedido = $parametros['numero_pedido'];
    $estado = $parametros['estado'];

    // Verificar que el estado esté en el rango permitido
    if ($estado <0  || $estado > 2) {
        $retorno = json_encode(array("mensaje" => "Estado inválido. Debe ser 0) pendiente, 1) preparacion o 2) listo para servir."));
    } else {
        // Obtener el pedido
        $pedido = Pedido::traer_un_pedido_numero_pedido($numero_pedido);

        if ($pedido == null) {
            $retorno = json_encode(array("mensaje" => "El numero de pedido es inválido"));
        } else {
            // Obtener el array de items del pedido
            $items = $pedido->items;

            // Iterar sobre los items para encontrar el correspondiente y modificar el estado
            foreach ($items as $item) {
                if (isset($item->estado)) {
                    $item->estado = $estado;
                }
            }

            // Convertir el array de items a JSON
            $nuevosItemsJson = json_encode($items);

            // Actualizar la columna items en la base de datos
            $pedido->actualizar_items_BD($nuevosItemsJson);

            $retorno = json_encode(array("mensaje" => "Estado cambiado con éxito"));
        }
    }

    $response->getBody()->write($retorno);
    return $response;
}



public static function POST_cambiar_estado_pedido2(Request $request, Response $response, array $args)
{
    $parametros = $request->getParsedBody();
    if (!isset($parametros['numero_pedido'], $parametros['id_producto'], $parametros['estado'])) {
        $retorno = json_encode(array("mensaje" => "Faltan parametros"));
    } else {
        $numero_pedido = $parametros['numero_pedido'];
        $id_producto = $parametros['id_producto'];
        $estado = $parametros['estado'];
        $tiempoOK = 1;
        $pedido = Pedido::traer_un_pedido_numero_pedido($numero_pedido);
        if ($pedido == null) {
            $retorno = json_encode(array("mensaje" => "El numero de pedido es invalido"));
        } else {
            $sector = $pedido->cambiar_estado_item($id_producto, $estado);
            if ($estado == 1) {
                $tiempoOK = 0;
                if (!isset($parametros['tiempo'])) {
                    $retorno = json_encode(array("mensaje" => "Ingrese el tiempo de elaboracion"));
                } else {
                    $tiempo = $parametros['tiempo'];
                    if (!($pedido->agregar_tiempo_item($id_producto, $tiempo))) {
                        $retorno = json_encode(array("mensaje" => "No se pudo realizar"));
                    } else {
                        $tiempoOK = 1;
                    }
                }
            } else {
                if ($estado == 2) {
                    $pedido->agregar_tiempo_item($id_producto, 0);
                }
            }
            if ($sector == null) {
                $retorno = json_encode(array("mensaje" => "No se pudo realizar"));
            } else {
                if ($tiempoOK == 1) {
                    $pedido->actualizar_items_BD();
                    $retorno = json_encode(array("mensaje" => "Estado actualizado con exito"));
                } else {
                    $retorno = json_encode(array("mensaje" => "Ingrese el tiempo de elaboracion"));
                }
            }
        }
    }
    $response->getBody()->write($retorno);
    return $response;
}


    
    // public static function POST_cambiar_estado_pedido(Request $request, Response $response, array $args){
    //     $param = $request->getQueryParams();
       
    //     if(!isset($param['token'])){
    //         $retorno = json_encode(array("mensaje" => "Token necesario"));
    //     }
    //     else{
    //         $parametros = $request->getParsedBody();
    //         if(!isset($parametros['numero_pedido'], $parametros['id_producto'], $parametros['estado'])){
    //             $retorno = json_encode(array("mensaje" => "Error en la carga de datos"));
    //         }
    //         else{
    //             $numero_pedido = $parametros['numero_pedido'];
    //             $id_producto = $parametros['id_producto'];
    //             $estado = $parametros['estado'];
    //             $tiempoOK = 1;
    //             $pedido = Pedido::traer_un_pedido_numero_pedido($numero_pedido);
    //             var_dump($pedido);
    //             if($pedido == null){
    //                 $retorno = json_encode(array("mensaje" => "El numero de pedido es invalido"));
    //             }
    //             else{
    //                 $sector = $pedido->cambiar_estado_item($id_producto, $estado);
    //                 var_dump($sector);
                    
    //                 if($estado == 1){
    //                     $tiempoOK = 0;
    //                     if(!isset($parametros['tiempo'])){
    //                         $retorno = json_encode(array("mensaje" => "ingrese el tiempo de elaboracion"));
    //                     }
    //                     else{
    //                         $tiempo = $parametros['tiempo'];
    //                         if(!($pedido->agregar_tiempo_item($id_producto, $tiempo))){
    //                             $retorno = json_encode(array("mensaje" => "No se pudo realizar"));
    //                         }
    //                         else{
    //                             $tiempoOK = 1;
    //                         }
    //                     }
    //                 }
    //                 else{
    //                     if($estado == 2){
    //                         $pedido->agregar_tiempo_item($id_producto, 0);
    //                     }
    //                 }

    //                 if($sector === -1){

    //                     $retorno = json_encode(array("mensaje" => "Producto no encontrado")); 

    //                 }

    //                 else{

    //                     if($sector == null){
    //                         $retorno = json_encode(array("mensaje" => "No se pudo realizar"));
    //                     }
    //                     else{
    //                         $token = $parametros['token'];
    //                         echo "Token: $token\n";
    //                         $respuesta = Autenticador::validar_token($token, "Empleado",$sector);
    //                         echo "Respuesta de validación: $respuesta\n";
    //                         if($respuesta == "Validado"){
    //                             if($tiempoOK == 1){
    //                                 $pedido->actualizar_items_BD();
    //                                 $retorno = json_encode(array("mensaje" => "Estado actualizado con exito"));
    //                             }
    //                             else{
    //                                 $retorno = json_encode(array("mensaje" => "ingrese el tiempo de elaboracion"));
    //                             }
    //                         }
    //                         else{
    //                             $retorno = json_encode(array("mensaje" => $respuesta));
    //                         }
    //                     }

    //                 }
                  
    //             }
    //         }
    //     }
    //     $response->getBody()->write($retorno);
    //     return $response;
    // }




    
    public static function GET_ConsultarTiempo(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['numero_pedido'])){
            $retorno = json_encode(array("mensaje" => "ingrese numeo de pedido"));
        }
        else{
            $numero_pedido = $param['numero_pedido'];
            $pedido = Pedido::traer_un_pedido_numero_pedido($numero_pedido);
            $tiempo = $pedido->calcular_tiempo_total_pedido();
            $retorno = json_encode(array("La demora es" => $tiempo." minutos"));
        }
        $response->getBody()->write($retorno);
        return $response;
    }

    public static function GET_ConsultarTiempoCliente(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['numero_pedido']) &&!isset($param['id_mesa']) ){
            $retorno = json_encode(array("mensaje" => "ingrese numero de pedido y id de mesa"));
        }
        else{
            $numero_pedido = $param['numero_pedido'];
            $id_mesa = $param['id_mesa'];
            $pedido = Pedido::traer_un_pedido_numero_pedido($numero_pedido);
            $tiempo = $pedido->calcular_tiempo_total_pedido();
            $retorno = json_encode(array("La demora es" => $tiempo." minutos"));
        }
        $response->getBody()->write($retorno);
        return $response;
    }
    
    public static function GET_Listar_pedidos_segun_estado(Request $request, Response $response, array $args)
    {
        $param = $request->getQueryParams();
    
        if (isset($param['estado'])) {
            $estado = $param['estado'];
    
            if (isset($param['sector'])) {
                $sector = $param['sector'];
    
                $pedidos = Pedido::obtener_todos_los_pedidos();
                $pedidosFill = Pedido::filtrar_segun_estado($pedidos, $estado);
                $pedidosFiltrados = Pedido::filtrar_segun_sector($pedidosFill, $sector, 0);
                $pedidosMapp = Pedido::mapear_para_mostrar($pedidosFiltrados);
                $retorno = json_encode(array("Listado_de_pedidos" => $pedidosMapp));
            } else {
                $pedidos = Pedido::obtener_todos_los_pedidos();
                $pedidosFiltrados = Pedido::filtrar_segun_estado($pedidos, $estado);
                $pedidosMapp = Pedido::mapear_para_mostrar($pedidosFiltrados);
                $retorno = json_encode(array("Listado_de_pedidos" => $pedidosMapp));
            }
        } else {
            $retorno = json_encode(array("mensaje" => "Ingrese estado a consultar"));
        }
    
        $response->getBody()->write($retorno);
        return $response;
    }
    
}
