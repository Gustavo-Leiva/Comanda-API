<?php
namespace src\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Mesa;
// use Autenticador;
require_once '../src/Clases/Mesa.php';
// require_once '../src/Clases/Usuario.php';
// require_once '../src/Clases/Autenticador.php';

class MesasController
{

    // public static function POST_AltaMesa(Request $request, Response $response, array $args){
      
    //     $parametros = $request->getParsedBody();
    //     $estado = $parametros['estado'];
               
    
    //     $mesa = new Mesa($estado);
    //     $ok = $mesa->InsertarMesa();
       
    
    //     if($ok != null){
    //         $retorno = json_encode(array("mensaje" => "Mesa dado de alta con éxito"));
    //     } else {
    //         $retorno = json_encode(array("mensaje" => "No se pudo dar de alta la mesa"));
    //     }
    
    //     $response->getBody()->write($retorno);
    //     return $response;       
    // } 
    


    public static $estados = array("con cliente esperando pedido", "con cliente comiendo", "con cliente pagando", "cerrada");
    public function POST_altaMesa($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $estado = $parametros['estado'];

        if(in_array($estado, $this::$estados))
        {
            $mesa = new Mesa($estado);
            $mesa->estado = $estado;
            $mesa->AltaMesa();
            $payload = json_encode(array("Mensaje" => "Mesa creado con exito"));
        }
        else
        {
            $payload = json_encode(array("Mensaje" => "Estado de mesa no valido. (con cliente esperando pedido / con cliente comiendo / con cliente pagando / cerrada)"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public static function POST_TraerTodos(Request $request, Response $response, array $args){
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesa" => $lista));

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
       
    }
         
        
  
    
}