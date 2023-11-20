<?php
require_once '../src/Clases/Pedido.php';

class Comanda
{
    public $id;
    public $nombre_cliente;
    public $numero_pedido;
    public $id_mesa;
    public $fecha_alta;
    public $tiempo_alta;
    public $puntuacion_mesa;
    public $puntuacion_restaurante;
    public $puntuacion_mozo;
    public $puntuacion_cocinero;
    public $reseña;

    public function __construct($nombre_cliente, $id_mesa, $numero_pedido = null, $fecha_alta = null, $tiempo_alta = null,
    $puntuacion_mesa = null, $puntuacion_restaurante = null, $puntuacion_mozo = null,
    $puntuacion_cocinero = null, $reseña = null, $id = null)
    {
        $this->nombre_cliente = $nombre_cliente;
        $this->id_mesa = $id_mesa;
        if($numero_pedido != null){
            $this->numero_pedido = $numero_pedido;
        }
        if($fecha_alta == null){
            $this->fecha_alta = date("Y-m-d");
        }
        else{
            $this->fecha_alta = $fecha_alta;
        }
        if($tiempo_alta == null){
            $this->tiempo_alta = time();
        }
        else{
            $this->tiempo_alta = $tiempo_alta;
        }
        if($puntuacion_mesa != null){
            $this->puntuacion_mesa = $puntuacion_mesa;
        }
        if($puntuacion_restaurante != null){
            $this->puntuacion_restaurante = $puntuacion_restaurante;
        }
        if($puntuacion_mozo != null){
            $this->puntuacion_mozo = $puntuacion_mozo;
        }
        if($puntuacion_cocinero != null){
            $this->puntuacion_cocinero = $puntuacion_cocinero;
        }
        if($reseña != null){
            $this->reseña = $reseña;
        }
        if($id != null){
            $this->id = $id;
        }
    }
    public function alta_de_comanda()
    {
		// $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
		// $consulta =$objetoAccesoDato->retornarConsulta("insert into comandas (nombre_cliente, numero_pedido, id_mesa, fecha_alta, tiempo_alta)values('$this->nombre_cliente','$this->numero_pedido','$this->id_mesa', '$this->fecha_alta', '$this->tiempo_alta')");
		$objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta = $objetoAccesoDato->retornarConsulta("INSERT INTO comandas (nombre_cliente, numero_pedido, id_mesa, fecha_alta, tiempo_alta) VALUES(:nombre_cliente, :numero_pedido, :id_mesa, :fecha_alta, :tiempo_alta)");
        $consulta->bindValue(':nombre_cliente', $this->nombre_cliente, PDO::PARAM_STR);
        $consulta->bindValue(':numero_pedido', $this->numero_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_alta', $this->fecha_alta, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_alta', $this->tiempo_alta, PDO::PARAM_STR);        
        $consulta->execute();
		return $objetoAccesoDato->retornarUltimoIdInsertado();
    }
    public static function traer_todas_las_comandas()
	{
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
        $consulta =$objetoAccesoDato->retornarConsulta("select nombre_cliente as nombre_cliente, numero_pedido as numero_pedido, id_mesa as id_mesa, fecha_alta as fecha_alta, puntuacion_mesa as puntuacion_mesa, puntuacion_restaurante as puntuacion_restaurante, puntuacion_mozo as puntuacion_mozo, puntuacion_cocinero as puntuacion_cocinero, reseña as reseña, tiempo_alta as tiempo_alta, id as id from comandas");
        $consulta->execute();
        $arrayObtenido = array();
        $comandas = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $comanda = new Comanda($i->nombre_cliente, $i->id_mesa, $i->numero_pedido, $i->fecha_alta, $i->tiempo_alta, $i->puntuacion_mesa, $i->puntuacion_restaurante, $i->puntuacion_mozo, $i->puntuacion_cocinero, $i->reseña,$i->id );
            $comandas[] = $comanda;
        }
        return $comandas;
	}
    public static function ver_tiempo_restante($numeroPedido){
        $ahora = time();
        $minutos = null;
        $pedido = Pedido::traer_un_pedido_numero_pedido($numeroPedido);
        $minutos = $pedido->calcular_tiempo_total_pedido();
        
        $tiempoObjetivo = $ahora + ($minutos * 60);
        $diferencia = $tiempoObjetivo - $ahora;
        $minutosRestantes = $diferencia / 60;
        return $minutosRestantes;
    }
    public function definir_destino_imagen($ruta){
        $destino = $ruta."\\".$this->id_mesa."-".$this->numero_pedido."-".$this->nombre_cliente.".png";
        return $destino;
    }
    public static function traer_una_comanda_numero_pedido($numero_pedido)
	{
        $comanda = null;
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("select * from comandas where numero_pedido = ?");
        $consulta->bindValue(1, $numero_pedido, PDO::PARAM_INT);
        $consulta->execute();
        $comandaBuscarda= $consulta->fetchObject();
        if($comandaBuscarda != null){
            $comanda = new Comanda($comandaBuscarda->nombre_cliente, $comandaBuscarda->id_mesa, $comandaBuscarda->numero_pedido);
        }
        return $comanda;
	}
    public function comanda_cargar_encuesta()
	{
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("update comandas set puntuacion_mesa = ?, puntuacion_restaurante = ?, puntuacion_mozo = ?, puntuacion_cocinero = ?, reseña = ? where numero_pedido = ?");
        $consulta->bindValue(1, $this->puntuacion_mesa, PDO::PARAM_INT);
        $consulta->bindValue(2, $this->puntuacion_restaurante, PDO::PARAM_INT);
        $consulta->bindValue(3, $this->puntuacion_mozo, PDO::PARAM_INT);
        $consulta->bindValue(4, $this->puntuacion_cocinero, PDO::PARAM_INT);
        $consulta->bindValue(5, $this->reseña, PDO::PARAM_STR);
        $consulta->bindValue(6, $this->numero_pedido, PDO::PARAM_INT);
        return $consulta->execute();
	}
    public static function mapeo_mejores_comentarios($arrray){
        
        $arrayMostrar = array();
        $buffer = array();
        $promedioMayor = 0;
        foreach($arrray as $i){
            $acum_puntos = 0;
            $promedio_puntos = 0;
            if($i->reseña != null){
                $acum_puntos += $i->puntuacion_mesa;
                $acum_puntos += $i->puntuacion_restaurante;
                $acum_puntos += $i->puntuacion_mozo;
                $acum_puntos += $i->puntuacion_cocinero;
                $promedio_puntos = $acum_puntos / 4;
                $mapeo = array(
                    "id_comanda"=>$i->id,
                    "numero_pedido"=>$i->numero_pedido,
                    "id_mesa"=>$i->id_mesa,
                    "promedio_de_puntuaciones"=>$promedio_puntos,
                    "reseña"=>$i->reseña,
                );
                array_push($buffer, $mapeo);
            }
        }
        foreach($buffer as $i){
            if($i['promedio_de_puntuaciones'] > $promedioMayor){
                $promedioMayor = $i['promedio_de_puntuaciones'];
                $arrayMostrar = array();
                array_push($arrayMostrar, $i);
            }
        }
        return $arrayMostrar;
    }
    public static function mesa_mas_usada($array){
        $masUsada = array();
        $frecuenciaMaxima = 0;
        $mesasUsadas = array();
        foreach($array as $i){
            array_push($mesasUsadas, $i->id_mesa);
        }
        $frecuencias = array_count_values($mesasUsadas);
        $frecuenciaMaxima = max($frecuencias);
        $masUsada = array_keys($frecuencias, $frecuenciaMaxima);
    
        return $masUsada;
    }

}