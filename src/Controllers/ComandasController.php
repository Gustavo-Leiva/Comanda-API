<?php
namespace src\Controllers;

use AddressInfo;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Pedido;
use Comanda;
use Mesa;
use Autenticador;
use Producto;

require_once '../src/Clases/Producto.php';
require_once '../src/Clases/Pedido.php';
require_once '../src/Clases/Mesa.php';
require_once '../src/Clases/Comanda.php';
require_once '../src/Clases/Usuario.php';
require_once '../src/Clases/Autenticador.php';

class ComandasController
{
    public static function GET_TraerTodos(Request $request, Response $response, array $args){
        $comandas = Comanda::traer_todas_las_comandas();
        $listado = json_encode(array("Listado_de_productos"=>$comandas));
        $response->getBody()->write($listado);
        return $response;
    }
    public static function GET_ListarMejoresPuntuaciones(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");
            if($respuesta == "Validado"){
                $comandas = Comanda::traer_todas_las_comandas();
                $mapp = Comanda::mapeo_mejores_comentarios($comandas);
                $retorno = json_encode(array("Mejor puntuacion"=>$mapp));
            }
            else{
                $retorno = json_encode(array("mensaje"=>$respuesta));
            }
        }
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function GET_MesaMasUsada(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");
            if($respuesta == "Validado"){
                $comandas = Comanda::traer_todas_las_comandas();
                $mesaMasUsada = Comanda::mesa_mas_usada($comandas);
                $retorno = json_encode(array("La mesa mas usada es:"=>$mesaMasUsada));
            }
            else{
                $retorno = json_encode(array("mensaje"=>$respuesta));
            }
        }
        $response->getBody()->write($retorno);
        return $response;
    }

    public static function POST_alta_Comanda(Request $request, Response $response, array $args){
        $rutaImagenMesa = 'C:\xampp\htdocs\apiComanda\src\Controllers\Media\imagenes_mesa';
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Empleado" ,0);
            if($respuesta == "Validado")
            {
                $parametros = $request->getParsedBody();
                $nombre_cliente = $parametros['nombre_cliente'];
                $numero_mesa = $parametros['id_mesa'];
                $cadena_items = $parametros['items'];
                $comanda = new Comanda($nombre_cliente, $numero_mesa);
                $mesa = new Mesa(0, $numero_mesa);
                $mesa->CambiarEstadoMesa(1);
                $pedido = new Pedido();
                $comanda->numero_pedido = $pedido->numero_pedido;
                $elementos = explode(",", $cadena_items);
                foreach($elementos as $i){
                    $pedido->cargar_nuevo_item($i);
                }
                $id_insertado = $pedido->alta_pedido();
                $id_comanda = $comanda->alta_de_comanda();
                if(isset($_FILES['imagen'])){
                    $imagen = $_FILES['imagen'];
                    $destino = $comanda->DefinirDestinoImagen($rutaImagenMesa);
                    move_uploaded_file($imagen['tmp_name'], $destino);
                }
                if($id_insertado != null && $id_comanda != null){
                    $retorno = json_encode(array("mensaje" => "Comanda cargada con exito"));
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
    public static function POST_CobrarComanda(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Empleado" ,0);
            if($respuesta == "Validado")
            {
                $parametros = $request->getParsedBody();
                $numero_pedido = $parametros['numero_pedido'];
                $precio_acum = 0;
                $pedido = Pedido::traer_un_pedido_numero_pedido($numero_pedido);
                $comanda = Comanda::traer_una_comanda_numero_pedido($numero_pedido);
                foreach($pedido->items as $i){
                    $producto = Producto::traer_un_producto_nombre($i->nombre);
                    $precio_acum += $producto->precio;
                    $i->estado = -1;  
                }
                Mesa::CambiarEstadoMesa_Id(3, $comanda->id_mesa);
                $pedido->Actualizar_items_BD();
                $retorno = json_encode(array("Total a abonar" => $precio_acum));
            }       
            else{
                $retorno = json_encode(array("mensaje" => $respuesta));
            }
        }
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function POST_Ver_tiempo_restante(Request $request, Response $response, array $args){
        $parametros = $request->getParsedBody();
        $numero_pedido = $parametros['numero_pedido'];
        $minutos_faltantes = Comanda::ver_tiempo_restante($numero_pedido);
        $respuesta = json_encode(array($minutos_faltantes=>" minutos restantes"));
        $response->getBody()->write($respuesta);
        return $response;
    }
    public static function POST_Guardar_imagen(Request $request, Response $response, array $args){
        $rutaImagenMesa = 'C:\xampp\htdocs\Comanda-II\src\Controllers\Multimedia\imagenes_mesa';
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Empleado",0);
            if($respuesta == "Validado")
            {
                $parametros = $request->getParsedBody();
                $numero_pedido = $parametros['numero_pedido'];
                $comanda = Comanda::traer_una_comanda_numero_pedido($numero_pedido);
                var_dump($comanda);
                if($comanda == null){
                    $retorno = json_encode(array("mensaje" => "No existe numero de pedido"));
                }
                else{
                    if(isset($_FILES['imagen'])){
                        $imagen = $_FILES['imagen'];
                        $destino = $comanda->definir_destino_imagen($rutaImagenMesa);
                        if(move_uploaded_file($imagen['tmp_name'], $destino)){
                            $retorno = json_encode(array("mensaje" => "Imagen guardada con exito"));
                        }
                    }
                    else{
                        $retorno = json_encode(array("mensaje" => "Debe ingresar imagen"));
                    } 
                }     
            }       
            else{
                $retorno = json_encode(array("mensaje" => $respuesta));
            }
        }
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function GET_Listar_pedidos_listos_cambiar_estado_mesa(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Empleado", 0);
            if($respuesta == "Validado"){
                $pedidos = Pedido::obtener_todos_los_pedidos();
                $pedidosFiltrados = Pedido::Comprobar_estado_pedido_listo($pedidos);
                if(count($pedidosFiltrados) == 0){
                $retorno = json_encode(array("mensaje" => "No hay pedidos listos para servir"));
                }
                else{
                    $pedidosMapp = Pedido::MapearParaMostrar($pedidosFiltrados);
                    $retorno = json_encode(array("Listado_de_pedidos"=>$pedidosMapp));
                    foreach($pedidosFiltrados as $i){
                        $comanda = Comanda::TraerUnaComanda_Numero_pedido($i->numero_pedido);
                        Mesa::CambiarEstadoMesa_Id(2, $comanda->id_mesa);
                    }
                }
            }
        }
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function POST_ClienteEncuesta(Request $request, Response $response, array $args){
        $parametros = $request->getParsedBody();
        if(isset($parametros['numero_pedido'])){
            $numero_pedido = $parametros['numero_pedido'];
            if(isset($parametros['puntuacion_mesa'], $parametros['puntuacion_restaurante'], 
            $parametros['puntuacion_mozo'], $parametros['puntuacion_cocinero'], 
            $parametros['reseña'])){
                $puntuacion_mesa = $parametros['puntuacion_mesa'];
                $puntuacion_restaurante = $parametros['puntuacion_restaurante'];
                $puntuacion_mozo = $parametros['puntuacion_mozo'];
                $puntuacion_cocinero = $parametros['puntuacion_cocinero'];
                $reseña = $parametros['reseña'];
                $comanda = Comanda::traer_una_comanda_numero_pedido($numero_pedido);
                $comanda->puntuacion_mesa = $puntuacion_mesa;
                $comanda->puntuacion_restaurante = $puntuacion_restaurante;
                $comanda->puntuacion_mozo = $puntuacion_mozo;
                $comanda->puntuacion_cocinero = $puntuacion_cocinero;
                $comanda->reseña = $reseña;
                $comanda->comanda_cargar_encuesta();
                $retorno = json_encode(array("mensaje" => "Encuesta cargada con exito"));
            }
            else{
                $retorno = json_encode(array("mensaje" => "Debe completar encuesta"));
            }
        }
        else{
            $retorno = json_encode(array("mensaje" => "Ingrese numero de pedido"));
        }
        $response->getBody()->write($retorno);
        return $response;
    }
    
}