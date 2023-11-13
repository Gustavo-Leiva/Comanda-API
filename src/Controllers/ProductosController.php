<?php
namespace src\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Producto;
// use Autenticador;
// use Usuario;

require '../src/Clases/Producto.php';
// require_once '../src/Clases/Autenticador.php';

class ProductosController
{
    public static function ErrorDatos(Request $request, Response $response, array $args){
        $response->getBody()->write('ERROR!! Carga de datos invalida');
        return $response;
    }

    public static $sectores = array("Vinoteca", "Cerveceria", "Cocina", "CandyBar");

    public function POST_insertarProducto($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $descripcion = $parametros['descripcion'];
        $precio = $parametros['precio'];
        $sector = $parametros['sector'];

        if(in_array($sector, $this::$sectores))
        {
            $producto = new Producto();
            $producto->descripcion = $descripcion;
            $producto->precio = $precio;
            $producto->sector = $sector;
            $producto->insertarProducto();
            $payload = json_encode(array("Mensaje" => "Producto creado con exito"));
        }
        else
        {
            $payload = json_encode(array("Mensaje" => "Sector de producto no valido. (Vinoteca / Cerveceria / Cocina / CandyBar)"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    

 


    public function GET_traerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }



}

?>