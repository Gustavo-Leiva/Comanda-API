<?php
namespace src\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Producto;
use Autenticador;
// use Usuario;

require_once '../src/Clases/Producto.php';
require_once '../src/Clases/Autenticador.php';

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
    

 


    public function GET_traerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }



    public static function GET_TraerProductoId(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");
            if($respuesta == "Validado"){
                $idProducto = $param['id_producto'];
                $productos = Producto::traer_un_producto_Id($idProducto);
                // $productosFiltrados = Producto::filtrar_para_mostrar($productos);
                // $retorno = json_encode(array("ListadoUsuarios"=>$productosFiltrados));
                $retorno = json_encode(array("producto"=>$productos));
            }
            else{
                $retorno = json_encode(array("mensaje" => $respuesta));
            }
        }
        $response->getBody()->write($retorno);
        return $response;
    }




}

?>