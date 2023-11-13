<?php
namespace src\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Mesa;
use Autenticador;
require_once '../src/Clases/Mesa.php';
require_once '../src/Clases/Usuario.php';
require_once '../src/Clases/Autenticador.php';

class MesasController
{
    public static function GET_traerTodos(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");
            if($respuesta == "Validado"){
                $mesas = Mesa::obtenerTodos();
                $mesasMapp = Mesa::MapearParaMostrar($mesas);
                $retorno = json_encode(array("Mesas"=>$mesasMapp));
            }
            else{
                $retorno = json_encode(array("mensaje" => $respuesta));
            }
        } 
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function POST_alta_de_mesa(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");
            if($respuesta == "Validado")
            {
                $parametros = $request->getParsedBody();
                if(!isset($parametros['estado'])){
                    $retorno = json_encode(array("mensaje" => "Error! carga de datos invalida"));
                }
                else{
                    $estado = $parametros['estado'];

                    $mesa = new Mesa($estado);
                    $ok = $mesa->altaMesa();
                    if($ok != null){
                        $retorno = json_encode(array("mensaje" => "Mesa creada con exito"));
                    }
                    else{
                        $retorno = json_encode(array("mensaje" => "No se pudo crear"));
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
    
    public static function POST_cambiar_estado_de_mesa(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");
            $resp2 = Autenticador::validar_token($token, "Empleado", 0);
            if($respuesta == "Validado" || $resp2 == "Validado")
            {
                $parametros = $request->getParsedBody();
                $id_mesa = $parametros['id'];
                $estado = $parametros['estado'];
                $mesa = new Mesa($estado, $id_mesa);
                if($estado < 4){
                    $mesa->cambiarEstadoMesa($estado);
                    $retorno = json_encode(array("mensaje" => "Estado cambiado con exito"));
                }
                else{
                    if($estado == 4){
                        if($respuesta == "Validado"){
                            $mesa->cambiarEstadoMesa($estado);
                            $retorno = json_encode(array("mensaje" => "Estado cambiado con exito"));
                        }
                        else{
                            $retorno = json_encode(array("mensaje" => $respuesta));
                        }
                    }
                    else{
                        $retorno = json_encode(array("mensaje" => "Valor de estado no valido"));
                    }
                }    
            }       
            else{
                $retorno = json_encode(array("mensaje" => $resp2));
            }
        }
        $response->getBody()->write($retorno);
        return $response;
    }
}