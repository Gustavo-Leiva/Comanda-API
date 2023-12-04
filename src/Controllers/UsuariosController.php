<?php
namespace src\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Usuario;
use AutentificadorJWT;

require '../src/Clases/Usuario.php';
require_once '../src/Clases/AutentificadorJWT.php';

class UsuariosController
{
    public static function ErrorDatos(Request $request, Response $response, array $args){
        $response->getBody()->write('ERROR!! Carga de datos invalida');
        return $response;
    }


    //ok visto
    //  public static function POST_Login(Request $request, Response $response, array $args){
    //     $parametros = $request->getParsedBody();

    //     $email = $parametros['email'];
    //     $contraseña = $parametros['contraseña'];

    //     $usuarioEncontrado = null;
    //     $usuarioEncontrado = Usuario::traer_un_usuario_email($email);

    //     if($usuarioEncontrado != null){
    //         if($contraseña == $usuarioEncontrado->password){
    //             // $tipo = ($usuarioEncontrado->tipo == 'socio') ? 'socio' : 'usuario';
    //             // $token = AutentificadorJWT::CrearToken($usuarioEncontrado->id, $email,$tipo);
    //             $token = AutentificadorJWT::CrearToken($usuarioEncontrado->id, $email);

    //             $data = array(
    //                 "token" => $token
    //             );
    //             $usuarioEncontrado->modificar_token_DB($data);
    //             // $retorno = json_encode(array("mensaje" => "Proceso exitoso"));
    //             $retorno = json_encode(array("mensaje" => "Proceso exitoso", "token" => $token));
    //         }
    //         else{
    //             $retorno = json_encode(array("mensaje" => "Contraseña incorrecta"));
    //         }
    //     }
    //     else{
    //         $retorno = json_encode(array("mensaje" => "Usuario no encontrado"));
    //     }
    //     $response->getBody()->write($retorno);
    //     return $response;
    // }


    public static function POST_Login(Request $request, Response $response, array $args){
        $parametros = $request->getParsedBody();

        $email = $parametros['email'];
        $contraseña = $parametros['contraseña'];
        $tipo =$parametros['tipo'];

        $usuarioEncontrado = Usuario::traer_un_usuario_email($email);

        if($usuarioEncontrado != null){
            if($contraseña == $usuarioEncontrado->password){
                $id = $usuarioEncontrado->id;
                $tipo = $usuarioEncontrado->tipo;
                $data['id']=$id;
                $data['tipo']=$tipo;
                $token = AutentificadorJWT::CrearToken($data);

                $data = array(
                    "token" => $token
                );
                $usuarioEncontrado->modificar_token_DB($data);
                // $retorno = json_encode(array("mensaje" => "Proceso exitoso"));
                $retorno = json_encode(array("mensaje" => "Proceso exitoso", "token" => $token));
            }
            else{
                $retorno = json_encode(array("mensaje" => "Contraseña incorrecta"));
            }
        }
        else{
            $retorno = json_encode(array("mensaje" => "Usuario no encontrado"));
        }
        $response->getBody()->write($retorno);
        return $response;
    }



 
    //ok visto
    public static function GET_TraerTodos(Request $request, Response $response, array $args)
    {
        $usuarios = Usuario::traer_todos_los_usuarios();
        
        if($usuarios != null){
            $usuariosFiltrados = Usuario::filtrar_para_mostrar($usuarios);
            $retorno = json_encode(array("ListadoUsuarios" => $usuariosFiltrados));
        } else {
            $retorno = json_encode(array("mensaje" => "Error al obtener usuarios"));
        }
    
        $response->getBody()->write($retorno);
        return $response;
    }
    

    // public static function GET_TraerUsuarioId(Request $request, Response $response, array $args){
    //     $param = $request->getQueryParams();
    //     if(!isset($param['token'])){
    //         $retorno = json_encode(array("mensaje" => "Token necesario"));
    //     }

    //    elseif (!isset($param['id_usuario'])) {
    //     $retorno = json_encode(array("mensaje" => "Se requiere el ID del usuario"));
    //    }


    //     else{
    //         $token = $param['token'];
    //         $respuesta = Autenticador::validar_token($token, "Admin");
    //         if($respuesta == "Validado"){
    //             $idUsuario = $param['id_usuario'];
    //             $usuarios = Usuario::traer_un_usuarioId($idUsuario);
    //             $usuariosFiltrados = Usuario::filtrar_para_mostrar($usuarios);
    //             $retorno = json_encode(array("ListadoUsuarios"=>$usuariosFiltrados));
    //         }
    //         else{
    //             $retorno = json_encode(array("mensaje" => $respuesta));
    //         }
    //     }
    //     $response->getBody()->write($retorno);
    //     return $response;
    // }


    public static function GET_TraerUsuarioId(Request $request, Response $response, array $args)
{
    $param = $request->getQueryParams();

    $idUsuario = $param['id_usuario'];
    $usuarios = Usuario::traer_un_usuarioId($idUsuario);

    if ($usuarios !== null) {
        $usuariosFiltrados = Usuario::filtrar_para_mostrar($usuarios);
        $retorno = json_encode(array("ListadoUsuarios" => $usuariosFiltrados));
    } else {
        $retorno = json_encode(array("mensaje" => "Usuario no encontrado"));
    }

    $response->getBody()->write($retorno);
    return $response;
}


    //sin validar
    // public static function GET_TraerUsuarioId(Request $request, Response $response, array $args){
    //     $param = $request->getQueryParams();
                
    //     if (!isset($param['token'])) {
    //         $retorno = json_encode(array("mensaje" => "Token necesario"));
    //     } elseif (!isset($param['id_usuario'])) {
    //        $retorno = json_encode(array("mensaje" => "Se requiere el ID del usuario"));
    //     } else {
    //         $token = $param['token'];
    //         $respuesta = Autenticador::validar_token($token, "Admin");
    //         if ($respuesta == "Validado") {
    //             $idUsuario = $param['id_usuario'];
    //             $usuarios = Usuario::traer_un_usuarioId($idUsuario);
    //             $retorno = json_encode(array("usuario"=>$usuarios));
    
    //         } else {
    //             $retorno = json_encode(array("mensaje" => $respuesta));
    //         }
    //     }
    //     $response->getBody()->write($retorno);
    //     return $response;
    // }
    


    public static function GET_GuardarEnCSV(Request $request, Response $response, array $args){
        $path = "Usuarios.csv";
        
        $usuarios = Usuario::traer_todos_los_usuarios_EnArray();
        $archivo = fopen($path, "w");
        $encabezado = array("id", "nombre", "apellido", "tipo", "sub_tipo", "sector", "email", "password", "fecha_registro");
        fputcsv($archivo, $encabezado);
        foreach($usuarios as $fila){
            fputcsv($archivo, $fila);
        }
        fclose($archivo);
        $retorno = json_encode(array("mensaje"=>"Usuarios guardados en CSV con exito"));
    
   
    $response->getBody()->write($retorno);
    return $response;
    }
    
    public static function GET_CargarUsuariosCSV(Request $request, Response $response, array $args){
        $path = "Usuarios.csv";
      
        $archivo = fopen($path, "r");
        $encabezado = fgets($archivo);
    
        while(!feof($archivo)){
            $linea = fgets($archivo);
            $datos = str_getcsv($linea);
            if(isset($datos[1])){
                $usuario = new Usuario($datos[1], $datos[2], $datos[3],$datos[6],$datos[7],$datos[4],$datos[5],$datos[8],$datos[0]);
                $usuario->insertarUsuario();
            }
        }
        fclose($archivo);
        
        $retorno = json_encode(array("mensaje"=>"Usuarios guardados en base de datos con exito"));
      
        $response->getBody()->write($retorno);
        return $response;
    }
    

    //ok visto
    public static function POST_InsertarUsuario(Request $request, Response $response, array $args) {
        $parametros = $request->getParsedBody();
    
        // Verifica si los parámetros necesarios están presentes
        if (isset($parametros['nombre'], $parametros['apellido'], $parametros['tipo'], $parametros['email'], $parametros['contraseña'], $parametros['subTipo'], $parametros['sector'])) {
            $nombre = $parametros['nombre'];
            $apellido = $parametros['apellido'];
            $tipo = $parametros['tipo'];
            $email = $parametros['email'];
            $contraseña = $parametros['contraseña'];
            $subTipo = $parametros['subTipo'];
            $sector = $parametros['sector'];
    
            // Resto del código para crear el objeto Usuario y realizar la inserción
            $user = new Usuario($nombre, $apellido, $tipo, $email, $contraseña, $subTipo, $sector);
            $ok = $user->insertarUsuario();
    
            if ($ok != null) {
                $retorno = json_encode(array("mensaje" => "Usuario creado con éxito"));
            } else {
                $retorno = json_encode(array("mensaje" => "No se pudo crear el usuario"));
            }
        } else {
            $retorno = json_encode(array("mensaje" => "Faltan parámetros necesarios"));
        }
    
        // Configura el encabezado Content-Type para indicar que la respuesta es JSON
        $response = $response->withHeader('Content-Type', 'application/json');
    
        // Escribe la respuesta en el cuerpo de la respuesta
        $response->getBody()->write($retorno);
    
        return $response;
    }
   
}

























?>