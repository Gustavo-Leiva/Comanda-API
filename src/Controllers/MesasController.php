<?php
namespace src\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Mesa;
use AutenticadorJWT;
require_once '../src/Clases/Mesa.php';
require_once '../src/Clases/Usuario.php';
require_once '../src/Clases/AutentificadorJWT.php';

class MesasController
{
    public static function GET_traerTodos(Request $request, Response $response, array $args){
     
        $mesas = Mesa::obtenerTodos();
        if($mesas !=null){

            $mesasMapp = Mesa::MapearParaMostrar($mesas);
            $retorno = json_encode(array("Mesas"=>$mesasMapp));
        } else{
        $retorno = json_encode(array("mensaje" => "error al obtener las mesas"));
        }
        
        $response->getBody()->write($retorno);
        return $response;
    }



    public static function POST_alta_de_mesa(Request $request, Response $response, array $args)
{
    $parametros = $request->getParsedBody();

    if (!isset($parametros['estado']) || !is_numeric($parametros['estado'])) {
        $retorno = json_encode(array("mensaje" => "Error! Carga de datos inválida. El estado debe ser un valor numérico."));
    } else {
        $estado = (int)$parametros['estado'];

        // Validar que el estado esté en el rango permitido (1 a 4)
        if ($estado >= 1 && $estado <= 4) {
            $mesa = new Mesa($estado);
            $ok = $mesa->altaMesa();

            if ($ok != null) {
                $retorno = json_encode(array("mensaje" => "Mesa creada con éxito"));
            } else {
                $retorno = json_encode(array("mensaje" => "No se pudo crear la mesa"));
            }
        } else {
            $mensaje = "Valor de estado no válido. Ingrese opción 1, 2, 3 u 4 <br>1) Con cliente esperando pedido <br>2) Con cliente comiendo <br>3) Con cliente pagando<br>4) Cerrada";
            $retorno = json_encode(array("mensaje" => $mensaje));
        }
    }

    $response->getBody()->write($retorno);
    return $response;
}


    //ok visto
    public static function POST_cambiar_estado_de_mesa(Request $request, Response $response, array $args)
{
    $parametros = $request->getParsedBody();
    $id_mesa = $parametros['id'];
    $estado = $parametros['estado'];
    $mesa = new Mesa($estado, $id_mesa);

    if ($estado >= 1 && $estado < 4) {
        $mesa->cambiarEstadoMesa($estado);
        $retorno = json_encode(array("mensaje" => "Estado cambiado con éxito"));
    } elseif ($estado == 4) {
        $mesa->cambiarEstadoMesa($estado);
        $retorno = json_encode(array("mensaje" => "Mesa cerrada con éxito"));
    } else {
        $mensaje = "Valor de estado no válido. Ingrese opción 1, 2, 3 u 4 <br>1) Con cliente esperando pedido <br>2) Con cliente comiendo <br>3) Con cliente pagando<br>4) Cerrada";
        $retorno = json_encode(array("mensaje" => $mensaje));
    }

    $response->getBody()->write($retorno);
    return $response;
}


//ok visto
public static function POST_cerrar_mesa(Request $request, Response $response, array $args)
{
    $parametros = $request->getParsedBody();
    $id_mesa = $parametros['id'];
    $estado = $parametros['estado'];
    $mesa = new Mesa($estado, $id_mesa);
     if ($estado == 4) {
        $mesa->cambiarEstadoMesa($estado);
        $retorno = json_encode(array("mensaje" => "Mesa cerrada con éxito"));
    } else {
        $mensaje = "Valor de estado no válido. Ingrese opción 4 <br>para indicar mesa Cerrada";
        $retorno = json_encode(array("mensaje" => $mensaje));
    }

    $response->getBody()->write($retorno);
    return $response;
}
   
    
}

?>