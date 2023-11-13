<?php

class Usuario
{
    public $id;
    public $nombre;
    public $apellido;
    public $tipo;
    public $subTipo;
    public $sector;
    public $email;
    public $password;
    public $token;
    public $fechaRegistro;

    public function __construct($nombre, $apellido, $tipo, $email, $password = null, $subTipo = null, $sector = null, $fechaRegistro = null, $id = null, $token = null)
    {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->tipo = $tipo;
        $this->email = $email;
        if($id != null){
            $this->id = $id;
        }
        if($subTipo != null){
            $this->subTipo = $subTipo;
        }
        if($sector != null){
            $this->sector = $sector;
        }
        if($fechaRegistro == null){
            $this->fechaRegistro =  date("Y-m-d");
        }
        else{
            $this->fechaRegistro = $fechaRegistro;
        }
        if($password != null){
            $this->password = $password;
        }
        else{
            $this->password = '12345';
        }
        if($token != null){
            $this->token = $token;
        }
    }
    public function insertarUsuario()
	{
		$objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
		$consulta =$objetoAccesoDato->retornarConsulta("INSERT INTO usuarios (nombre,apellido,tipo, sub_tipo, sector, email, password, token, fechaRegistro)values('$this->nombre','$this->apellido','$this->tipo', '$this->subTipo', '$this->sector', '$this->email', '$this->password', '$this->token', '$this->fechaRegistro')");
		$consulta->execute();
		return $objetoAccesoDato->retornarUltimoIdInsertado();
	}
    public static function traer_todos_los_usuarios_EnArray()
	{
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("SELECT id as id, nombre as nombre, apellido as apellido, tipo as tipo, sub_tipo as subTipo, sector as sector, email as email, contraseña as password, token as token, fecha_registro as fechaRegistro from usuarios");
        $consulta->execute();
        $usuarios = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $usuario = array($i->id, $i->nombre, $i->apellido, $i->tipo, $i->subTipo, $i->sector, $i->email, $i->password,$i->fechaRegistro);
            $usuarios[] = $usuario;
        }
        return $usuarios;
	}
    public static function traer_todos_los_usuarios()
	{
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("SELECT id as id, nombre as nombre, apellido as apellido, tipo as tipo, sub_tipo as subTipo, sector as sector, email as email, password as password, token as token, fechaRegistro as fechaRegistro from usuarios");
        $consulta->execute();
        $arrayObtenido = array();
        $usuarios = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $usuario = new Usuario($i->nombre, $i->apellido, $i->tipo, $i->email, $i->password, $i->subTipo, $i->sector,$i->fechaRegistro, $i->id , $i->token);
            $usuarios[] = $usuario;
        }
        return $usuarios;
	}
    public static function traer_un_usuarioId($id) 
	{
        $usuario = null;
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("SELECT * from usuarios where id = ?");
        $consulta->bindValue(1, $id, PDO::PARAM_INT);
        $consulta->execute();
        $usuarioBuscado= $consulta->fetchObject();
        if($usuarioBuscado != null){
            $usuario = new Usuario($usuarioBuscado->nombre, $usuarioBuscado->apellido, $usuarioBuscado->tipo, $usuarioBuscado->email, $usuarioBuscado->password, $usuarioBuscado->sub_tipo, $usuarioBuscado->sector,$usuarioBuscado->fechaRegistro, $usuarioBuscado->id ,  $usuarioBuscado->token);
        }
        return $usuario;
	}
    public static function traer_un_usuario_email($email) 
	{
        $usuario = null;
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("SELECT * from usuarios where email = ?");
        $consulta->bindValue(1, $email, PDO::PARAM_STR);
        $consulta->execute();
        $usuarioBuscado= $consulta->fetchObject();
        
        if($usuarioBuscado != null){
            // $usuario = new Usuario($usuarioBuscado->nombre, $usuarioBuscado->apellido, $usuarioBuscado->tipo, $usuarioBuscado->email, $usuarioBuscado->contraseña, $usuarioBuscado->sub_tipo, $usuarioBuscado->sector,$usuarioBuscado->fecha_registro, $usuarioBuscado->id ,  $usuarioBuscado->token);
            $usuario = new Usuario($usuarioBuscado->nombre, $usuarioBuscado->apellido, $usuarioBuscado->tipo, $usuarioBuscado->email);
            $usuario->id = $usuarioBuscado->id;
            $usuario->token = $usuarioBuscado->token;
            $usuario->subTipo = $usuarioBuscado->sub_tipo;
            $usuario->sector = $usuarioBuscado->sector;
            $usuario->fechaRegistro = $usuarioBuscado->fechaRegistro;
            $usuario->password = $usuarioBuscado->password;
        }
        return $usuario;
	}
    public function modificar_token_DB($data){
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("UPDATE usuarios set token = ? where id = ?");
        $consulta->bindValue(1, $data["token"], PDO::PARAM_STR);
        $consulta->bindValue(2, $this->id, PDO::PARAM_INT);
        return$consulta->execute();
    }

    public static function filtrar_para_mostrar($array){
        if(count($array) > 0){
            foreach($array as $i){
                unset($i->password);
                unset($i->token);
            }
            return $array;
        }
    }

    public static function filtrar_para_guardar($array){
        if(count($array) > 0){
            foreach($array as $i){
                unset($i['password']);
                unset($i['token']);
            }
            return $array;
        }
    }
}


?>