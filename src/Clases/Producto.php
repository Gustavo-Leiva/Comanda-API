
<?php

class Producto
{
    public $id;
    public $nombre;
    public $sector;
    public $precio;

    public function __construct($nombre ='', $sector ='', $precio =0,$id = null,)
    {
        $this->nombre = $nombre;
        $this->sector = $sector;
        $this->precio = $precio;
              
        if($id != null){
            $this->id = $id;
        }
    }

    public function insertarProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerConexionDatos();
        $consulta =$objAccesoDatos->retornarConsulta("INSERT INTO productos (nombre, sector, precio)values('$this->nombre','$this->sector','$this->precio')");

        // $consulta = $objAccesoDatos->retornarConsulta("INSERT INTO productos (descripcion, sector, precio) VALUES (:descripcion, :sector,:precio)");
        // $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        // $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        // $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->retornarUltimoIdInsertado();
    }



    public static function obtenerTodos()
	{
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        // $consulta =$objetoAccesoDato->RetornarConsulta("select id as id, nombre as nombre, sector as sector, precio as precio from productos");
        $consulta =$objetoAccesoDato->retornarConsulta("select id , nombre, sector, precio from productos");
        $consulta->execute();
        $arrayObtenido = array();
        $productos = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $producto = new Producto($i->nombre, $i->sector, $i->precio,$i->id);
            $productos[] = $producto;
        }
        return $productos;
	}


    public static function traer_un_producto_Id($id) 
	{
        $producto = null;
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("select * from productos where id = ?");
        $consulta->bindValue(1, $id, PDO::PARAM_INT);
        $consulta->execute();
        $productoBuscado= $consulta->fetchObject();
        if($productoBuscado != null){
            // $producto = new Producto($productoBuscado->id_producto,$productoBuscado->nombre, $productoBuscado->sector, $productoBuscado->precio);
            $producto = new Producto($productoBuscado->nombre, $productoBuscado->sector, $productoBuscado->precio, $productoBuscado->id);
        
        }
       
        return $producto;
	}



    public static function traer_precio_nombre($nombre) 
	{
        $precio = null;
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("select precio from productos where nombre = ?");
        $consulta->bindValue(1, $nombre, PDO::PARAM_STR);
        $consulta->execute();
        $precio= $consulta->fetchObject();
        return $precio;
	}
    public static function traer_un_producto_nombre($nombre_producto) 
	{
        $producto = null;
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("select * from productos where nombre = ?");
        $consulta->bindValue(1, $nombre_producto, PDO::PARAM_STR);
        $consulta->execute();
        $productoBuscado= $consulta->fetchObject();
        if($productoBuscado != null){
            // $producto = new Producto($productoBuscado->id_producto,$productoBuscado->nombre, $productoBuscado->sector, $productoBuscado->precio);
            $producto = new Producto($productoBuscado->nombre, $productoBuscado->sector, $productoBuscado->precio, $productoBuscado->id);

        }
        return $producto;
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

    public static function mapear_para_mostrar($array){
        if(count($array) > 0){
            foreach($array as $i){
                switch($i->sector){
                    case 1:
                        $i->sector = "Barra de tragos";
                    break;
                    case 2:
                        $i->sector = "Barra de choperas";
                    break;
                    case 3:
                        $i->sector = "Cocina";
                    break;
                    case 4:
                        $i->sector = "Candy bar";
                    break;
                }
            }
        }
        return $array;
    }

}
?>