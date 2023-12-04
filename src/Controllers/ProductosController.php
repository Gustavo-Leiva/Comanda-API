<?php
namespace src\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Producto;
use AutentificadorJWT;
// use Usuario;

require_once '../src/Clases/Producto.php';
require_once '../src/Clases/AutentificadorJWT.php';

class ProductosController
{
    public static function ErrorDatos(Request $request, Response $response, array $args){
        $response->getBody()->write('ERROR!! Carga de datos invalida');
        return $response;
    }

    public static $sectores = array("Vinoteca", "Cerveceria", "Cocina", "CandyBar");

    //ok visto
    public function POST_insertarProducto($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $nombre = $parametros['nombre'];
        $sector = $parametros['sector'];
        $precio = $parametros['precio'];

        if(in_array($sector, $this::$sectores))
        {
            $producto = new Producto();
            $producto->nombre = $nombre;
            $producto->sector = $sector;
            $producto->precio = $precio;
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
    

 

   //ok visto
    public function GET_traerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }



    //ok
    public static function GET_TraerProductoId(Request $request, Response $response, array $args)
    {
        $param = $request->getQueryParams();
        $idProducto = $param['id_producto'];
        $productos = Producto::traer_un_producto_Id($idProducto);
    
        if ($productos !== null) {
            // Puedes habilitar esta línea si necesitas filtrar los productos antes de enviar la respuesta
            // $productosFiltrados = Producto::filtrar_para_mostrar($productos);
            $retorno = json_encode(array("producto" => $productos));
        } else {
            $retorno = json_encode(array("mensaje" => "Producto no encontrado"));
        }
    
        $response->getBody()->write($retorno);
        return $response;
    }
    




}

?>