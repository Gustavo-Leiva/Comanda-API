<?php

class Usuario{
    public $id;
    public $nombre;
    public $apellido;
    public $tipo;
    public $sector;
   
    public function __construct($id = null,$nombre ='', $apellido ='', $tipo ='', $sector = '')
    {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->tipo = $tipo;
        $this->sector = $sector;
       
        if($id != null){
            $this->id = $id;
        }
    }


    //ok 
    public function insertarUsuario()
	{
		$objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
		$consulta =$objetoAccesoDato->retornarConsulta("INSERT INTO usuarios (nombre,apellido,tipo, sector)values('$this->nombre','$this->apellido','$this->tipo', '$this->sector')");
		$consulta->execute();
		return $objetoAccesoDato->retornarUltimoIdInsertado();
	}


    public static function obtenerTodos()
	{
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        // $consulta =$objetoAccesoDato->RetornarConsulta("select id as id, nombre as nombre, apellido as apellido, tipo as tipo, sub_tipo as subTipo, sector as sector, email as email, contraseña as password, token as token, fecha_registro as fechaRegistro from usuarios");
        $consulta =$objetoAccesoDato->retornarConsulta("select id , nombre, apellido, tipo,sector from usuarios");
        $consulta->execute();
        $arrayObtenido = array();
        $usuarios = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $usuario = new Usuario($i->id,$i->nombre, $i->apellido, $i->tipo, $i->sector);
            $usuarios[] = $usuario;
        }
        return $usuarios;
	}


  
}



?>