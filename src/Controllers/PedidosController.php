<?php
namespace src\Controllers;

use Autenticador;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Pedido;
use Usuario;

require_once '../src/Clases/Pedido.php';
require_once '../src/Clases/Usuario.php';
require_once '../src/Clases/Autenticador.php';

class PedidosController
{
    public static function GET_TraerTodos(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $respuesta = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");
            if($respuesta == "Validado"){
                $pedidos = Pedido::obtener_todos_los_pedidos();
                $pedidosMapp = Pedido::mapear_para_mostrar($pedidos);
                $respuesta = json_encode(array("Listado_de_pedidos"=>$pedidosMapp));
            }
            else{
                $sector = Autenticador::traer_sector_desde_token($token);
                $pedidos = Pedido::obtener_todos_los_pedidos();
                $pedidosFiltrados = Pedido::filtrar_segun_sector($pedidos, $sector);
                $pedidosMapp = Pedido::mapear_para_mostrar($pedidosFiltrados);
                $respuesta = json_encode(array("Listado_de_pedidos"=>$pedidosMapp));
            }
        }
        $response->getBody()->write($respuesta);
        return $response;
    }
    public static function POST_alta_pedido(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Empleado" ,0);
            if($respuesta == "Validado")
            {
                $pedido = new Pedido();
                $parametros = $request->getParsedBody();
                $cadena_items = $parametros['items'];
                $elementos = explode(",", $cadena_items);
                foreach($elementos as $i){
                    // echo $i;
                    $pedido->cargar_nuevo_item($i);
                }
                $id_insertado = $pedido->alta_pedido();
                if($id_insertado != null){
                    $retorno = json_encode(array("mensaje" => "Pedido creado con exito"));
                }
                else{
                    $retorno = json_encode(array("mensaje" => "No se pudo crear"));
                }        
            }       
            else{
                $retorno = json_encode(array("mensaje" => $respuesta));
            }
        }
        $response->getBody()->write($retorno);
        return $response;
    }


    public static function POST_cambiar_estado_pedido(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if (!isset($param['token'])) {
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        } else {
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");
            $resp2 = Autenticador::validar_token($token, "Empleado", 0);
            if ($respuesta == "Validado" || $resp2 == "Validado") {
                $parametros = $request->getParsedBody();
                $numero_pedido = $parametros['numero_pedido'];
                $estado = $parametros['estado'];
    
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
            } else {
                $retorno = json_encode(array("mensaje" => $resp2));
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
    
    public static function GET_Listar_pedidos_segun_estado(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            if(isset($param['estado'])){
                $estado = $param['estado'];
                $respuesta = Autenticador::validar_token($token, "Admin");
                if($respuesta == "Validado"){
                    $pedidos = Pedido::obtener_todos_los_pedidos();
                    $pedidosFiltrados = Pedido::filtrar_segun_estado($pedidos,  $estado);
                    $pedidosMapp = Pedido::mapear_para_mostrar($pedidosFiltrados);
                    $retorno = json_encode(array("Listado_de_pedidos"=>$pedidosMapp));
                }
                else{
                    if($respuesta = Autenticador::validar_token($token, "Empleado", 0) == "Validado"){
                        $pedidos = Pedido::obtener_todos_los_pedidos();
                        $pedidosFiltrados = Pedido::filtrar_segun_estado($pedidos,  $estado);
                        $pedidosMapp = Pedido::mapear_para_mostrar($pedidosFiltrados);
                        $retorno = json_encode(array("Listado_de_pedidos"=>$pedidosMapp));
                    }
                    else{
                        $sector = Autenticador::traer_sector_desde_token($token);
                        $pedidos = Pedido::obtener_todos_los_pedidos();
                        $pedidosFill = Pedido::filtrar_segun_estado($pedidos,  $estado);
                        $pedidosFiltrados = Pedido::filtrar_segun_sector($pedidosFill, $sector, 0);
                        $pedidosMapp = Pedido::mapear_para_mostrar($pedidosFiltrados);
                        $retorno = json_encode(array("Listado_de_pedidos"=>$pedidosMapp));
                    }
                    
                }
            }
            else{
                $retorno = json_encode(array("mensaje" => "Ingrese estado a consultar"));
            }
            
        }
        $response->getBody()->write($retorno);
        return $response;
    }
}
