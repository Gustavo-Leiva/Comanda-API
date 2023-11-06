
<?php

class Producto{

    public $id;
    public $nombre;
    public $sector;
    public $precio;

    public function __construct($id=null,$nombre='', $sector='', $precio=0)
    {
        
        $this->nombre = $nombre;
        $this->sector = $sector;
        $this->precio = $precio;
        if($id != null){
            $this->id = $id;
        }
       
    }

    // public function InsertarProducto()
	// {
	// 	$objetoAccesoDato = AccesoDatos::obtenerConexionDatos();  
        		// $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into productos (nombre, sector, precio)values('$this->nombre','$this->sector', '$this->precio')");
        // $consulta =$objetoAccesoDato->RetornarConsulta("INSERT INTO productos (nombre,sector,precio)values('$this->nombre','$this->sector','$this->precio')");

    //     $consulta->execute();
    //     // var_dump($consulta);
	// 	return $objetoAccesoDato->RetornarUltimoIdInsertado();
	// }


    public function InsertarProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerConexionDatos(); 
        $consulta = $objAccesoDatos->retornarConsulta("INSERT INTO productos (nombre, precio, sector) VALUES (:nombre, :precio, :sector)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->RetornarUltimoIdInsertado();
    }

    public static function obtenerTodos()
	{
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        // $consulta =$objetoAccesoDato->RetornarConsulta("select id as id, nombre as nombre, sector as sector, precio as precio from productos");
        $consulta =$objetoAccesoDato->RetornarConsulta("select id , nombre, sector, precio from productos");
        $consulta->execute();
        $arrayObtenido = array();
        $productos = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $producto = new Producto($i->id,$i->nombre, $i->sector, $i->precio);
            $productos[] = $producto;
        }
        return $productos;
	}


}

?>