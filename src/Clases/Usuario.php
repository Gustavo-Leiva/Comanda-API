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
    // public function insertarUsuario()
	// {
	// 	$objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
	// 	$consulta =$objetoAccesoDato->retornarConsulta("INSERT INTO usuarios (nombre,apellido,tipo, sector)values('$this->nombre','$this->apellido','$this->tipo', '$this->sector')");
	// 	$consulta->execute();
	// 	return $objetoAccesoDato->retornarUltimoIdInsertado();
	// }

    public function insertarUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerConexionDatos();
        $consulta = $objAccesoDatos->retornarConsulta("INSERT INTO usuarios (nombre, apellido,tipo, sector) VALUES (:nombre, :apellido,:tipo,:sector)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->retornarUltimoIdInsertado();
    }


    public static function obtenerTodos()
	{
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
       
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