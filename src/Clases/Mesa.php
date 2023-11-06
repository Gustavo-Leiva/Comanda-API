<?php
class Mesa
{
    public $id;
    public $estado;

    public function __construct($id = null, $estado='')
    {
        $this->estado = $estado;
        if($id != null){
            $this->id = $id;
        }
    }


    public function altaMesa()
    {
        $objetoAccesoDatos = AccesoDatos::obtenerConexionDatos();
        $consulta = $objetoAccesoDatos->RetornarConsulta("INSERT INTO mesas (estado) VALUES (:estado)");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();
        return $objetoAccesoDatos->RetornarUltimoIdInsertado();
    }
  

    public static function obtenerTodos()
	{
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("SELECT id as id, estado as estado from mesas");
        $consulta->execute();
        $arrayObtenido = array();
        $mesas = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $mesa = new Mesa($i->id , $i->estado);
            $mesas[] = $mesa;
        }
        return $mesas;
	}


    public function cambiarEstadoMesa($estado){
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("UPDATE mesas set estado = ? where id = ?");
        $consulta->bindValue(1, $estado, PDO::PARAM_INT);
        $consulta->bindValue(2, $this->id, PDO::PARAM_INT);
        return$consulta->execute();
    }
    public static function cambiarEstadoMesa_Id($estado, $id_mesa){
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("UPDATE mesas set estado = ? where id = ?");
        $consulta->bindValue(1, $estado, PDO::PARAM_INT);
        $consulta->bindValue(2, $id_mesa, PDO::PARAM_INT);
        return$consulta->execute();
    }
    
    public static function mapearParaMostrar($array){
        if(count($array) > 0){
            foreach($array as $i){
                switch($i->estado){
                    case 1:
                        $i->estado = "Con cliente esperando pedido";
                    break;
                    case 2:
                        $i->estado = "Con cliente comiendo";
                    break;
                    case 3:
                        $i->estado = "Con cliente pagando";
                    break;
                    case 4:
                        $i->estado = "Cerrada";
                    break;
                }
            }
        }
        return $array;
    }

}

?>