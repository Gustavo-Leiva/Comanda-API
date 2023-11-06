<?php
namespace src\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Usuario;
// use Autenticador;

require '../src/Clases/Usuario.php';
// require_once '../src/Clases/Autenticador.php';

class UsuariosController
{
    public static function ErrorDatos(Request $request, Response $response, array $args){
        $response->getBody()->write('ERROR!! Carga de datos invalida');
        return $response;
    }

    public static function POST_insertarUsuario(Request $request, Response $response, array $args){
        $parametros = $request->getParsedBody();
        $nombre = $parametros['nombre'];
        $apellido = $parametros['apellido'];
        $tipo = $parametros['tipo'];
        $sector = $parametros['sector'];
    
        $user = new Usuario($nombre, $apellido, $tipo, $sector);
        $ok = $user->insertarUsuario();
    
        if($ok != null){
            $retorno = json_encode(array("mensaje" => "Usuario creado con éxito"));
        } else {
            $retorno = json_encode(array("mensaje" => "No se pudo crear"));
        }
    
        $response->getBody()->write($retorno);
        return $response;
    }
    

 


    public function GET_traerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    

}

?>