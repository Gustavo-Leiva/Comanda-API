<?php
namespace src\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Mesa;
require_once '../src/Clases/Mesa.php';


class MesasController
{
    


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