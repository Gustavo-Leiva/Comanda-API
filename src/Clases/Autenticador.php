<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once '../src/Clases/Usuario.php';

class Autenticador
{
    private static $claveSecreta = "gustavoLeiva";
    private static $tipoEncriptacion = "HS256";

    public static function definir_token($id, $email){
        $time = time();
        $payload = array(
         
            "iat" => $time, //Tiempo en que inicia el token
            "exp" => $time + (60*60*24*7), // Validez del token por 7 días
            "data" => [
                "id" => $id,
                "email" => $email,
            ]
        );
        $token = JWT::encode($payload, self::$claveSecreta, self::$tipoEncriptacion);
        return $token;
    }

    
    
    public static function validar_token($token, $tipo, $sector = null){
        $usuario = null;
        $resp = "No autorizado";

        // Verificar si el token es nulo
        if ($token === null) {
            return "Token no proporcionado";
        }
        try {
            $decodificado = JWT::decode(
                $token,
                new Key(self::$claveSecreta, self::$tipoEncriptacion)
            );

            var_dump("Tipo de Usuario: $tipo, Sector: $sector, Usuario ID: " . $decodificado->data->id);

            $usuario = Usuario::traer_un_usuarioId($decodificado->data->id);
        if($usuario != null && $usuario->tipo == $tipo){
            if($sector != null){
                if($usuario->sector == $sector){
                    $resp =  "Validado";
                }
                 else {
                    throw new Exception("No autorizado: El usuario no tiene acceso al sector requerido.");
                }
           }else{
                $resp =  "Validado";
            }
          }
        } catch (Exception $e) {
            switch($e->getMessage()){
                case "Expired token":
                $resp = "Sesion expirada"; 
                break;
                case "Signature verification failed":
                    $resp = "Token invalido";
                    break;
                default:
                // $resp = "No autorizado: " . $e->getMessage();
                $resp = "No autorizado: " . $e->getMessage() . " - Tipo: $tipo, Sector: $sector, Usuario ID: " . $decodificado->data->id;
                break;
            }
            // die(json_encode(array("mensaje" => $resp)));
            // die(json_encode(array("mensaje" => "No autorizado: " . $e->getMessage())));
        }
        return $resp;

       

    }

    
    public static function traer_sector_desde_token($token){
        $usuario = null;
        $decodificado = JWT::decode(
            $token,
        new Key(self::$claveSecreta, self::$tipoEncriptacion)
        );
        $usuario = Usuario::traer_un_usuarioId($decodificado->data->id);
        $resp = $usuario->sector;
        return $resp;
    }
}

?>