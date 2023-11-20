<?php
require_once '../src/Clases/Producto.php';

class Pedido
{
    public $id;
    public $numero_pedido;
    public $items;

    public function __construct($items = null, $numero_pedido = null, $id = null)
    {
        $this->items = array();
        if($items != null){
            $this->items = $items; 
        }
        if($numero_pedido == null){
            $this->numero_pedido = rand(1, 99999);
        }
        else{
            $this->numero_pedido = $numero_pedido;
        }
        if($id != null){
            $this->id = $id;
        }
    }

    public function alta_pedido()
	{
        $itemsJson = json_encode($this->items);
		$objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
		$consulta =$objetoAccesoDato->retornarConsulta("INSERT INTO pedidos (numero_pedido, items)VALUES('$this->numero_pedido','$itemsJson')");
		// $consulta = $objetoAccesoDato->retornarConsulta("INSERT INTO pedidos (numero_pedido, items) VALUES(:numero_pedido, :items)");
        // $consulta->bindValue(':numero_pedido', $this->numero_pedido, PDO::PARAM_INT);
        // $consulta->bindValue(':items', $itemsJson, PDO::PARAM_STR);

        $consulta->execute();
		return $objetoAccesoDato->retornarUltimoIdInsertado();
	}

    public static function obtener_todos_los_pedidos()
	{
        $pedido = null;
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("select id as id, numero_pedido as numero_pedido, items as items from pedidos");
        $consulta->execute();
        $arrayObtenido = array();
        $pedidos = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $itemsJson = json_decode($i->items);
            $pedido = new Pedido($itemsJson, $i->numero_pedido, $i->id );
            $pedidos[] = $pedido;
        }
        return $pedidos;
	}

    public static function traer_un_pedido_Id($id) 
	{
        $pedido = null;
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("select * from pedidos where id = ?");
        $consulta->bindValue(1, $id, PDO::PARAM_INT);
        $consulta->execute();
        $pedidoBuscado= $consulta->fetchObject();
        if($pedidoBuscado != null){
            $itemsJson = json_decode($pedidoBuscado->items);
            $pedido = new Pedido($itemsJson, $pedidoBuscado->numero_pedido, $pedidoBuscado->id,);
        }
        return $pedido;
	}

    public static function traer_un_pedido_numero_pedido($numero_pedido) 
	{
        $pedido = null;
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("select * from pedidos where numero_pedido = ?");
        $consulta->bindValue(1, $numero_pedido, PDO::PARAM_INT);
        $consulta->execute();
        $pedidoBuscado= $consulta->fetchObject();
        if($pedidoBuscado != null){
            $itemsJson = json_decode($pedidoBuscado->items);
            $pedido = new Pedido($itemsJson, $pedidoBuscado->numero_pedido, $pedidoBuscado->id,);
        }
        return $pedido;
	}

   
    public function cargar_nuevo_item($id)
    {
        $producto = Producto::traer_un_producto_Id($id);
        $producto_pedido = array(
            "nombre"=>$producto->nombre,
            "estado"=>0,
            "tiempo"=>0,
        );
        array_push($this->items,$producto_pedido);
    }

    public function cambiar_estado_item($id, $estado)
    {
        $sector = null;
        $producto = Producto::traer_un_producto_Id($id);
        foreach($this->items as $i){           
             if($i->nombre == $producto->nombre){  // Corregido aquí
                $i->estado = $estado;
                $sector = $producto->sector;
                return $sector;
            }
        }
        return -1; 
    }


    public function cambiarEstadoPedido($estado){
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("UPDATE pedidos set estado = ? where id = ?");
        $consulta->bindValue(1, $estado, PDO::PARAM_INT);
        $consulta->bindValue(2, $this->id, PDO::PARAM_INT);
        return$consulta->execute();
    }
    

      // if($i['nombre'] == $producto->nombre){
            //     $i['estado'] = $estado;


    public function agregar_tiempo_item($id_producto, $tiempo_minutos)
    {
        $ok = false;
        $producto = Producto::traer_un_producto_Id($id_producto);
        foreach($this->items as $i){
            if($i->nombre == $producto->nombre){
                $i->tiempo = $tiempo_minutos;
                $ok = true;
            }
        }
        return $ok;
    }

    public function calcular_tiempo_total_pedido(){
        $tiempo_demora = 0;
        foreach($this->items as $i){
            if($i->tiempo > $tiempo_demora){
                $tiempo_demora = $i->tiempo;
            }       
        }
        return $tiempo_demora;
    }
    
    public function actualizar_items_BD()
    {
        $itemsJson = json_encode($this->items);
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("UPDATE pedidos set items = ? where id = ?");
        $consulta->bindValue(1, $itemsJson, PDO::PARAM_STR);
        $consulta->bindValue(2, $this->id, PDO::PARAM_INT);
        return$consulta->execute();
    }

    // public static function mapear_para_mostrar($array){
    //     if(count($array) > 0){
    //         foreach($array as $i){
    //             if ($i->items) {  // Verificar si $i->items no es null
    //             foreach($i->items as $p){
    //                 if ($p) {  // Verificar si $p no es null
    //                 switch($p->estado){
    //                     case 0:
    //                         $p->estado = "Pendiente";
    //                     break;
    //                     case 1:
    //                         $p->estado = "En preparacion";
    //                     break;
    //                     case 2:
    //                         $p->estado = "Listo para servir";
    //                     break;
    //                 }
    //             }
                
    //         }
    //       }
    //    }
    //  }
    //     return $array;
    // }


    public static function mapear_para_mostrar($array){
        if(count($array) > 0){
            foreach($array as $i){
                foreach($i->items as $p){
                    if (isset($p->estado)) {
                        switch($p->estado){
                            case 0:
                                $p->estado = "Pendiente";
                                break;
                            case 1:
                                $p->estado = "En preparacion";
                                break;
                            case 2:
                                $p->estado = "Listo para servir";
                                break;
                        }
                    }
                }
            }
        }
        return $array;
    }
    

    public static function filtrar_segun_sector($array, $sector, $estado = null){
        $arrayFiltrado = array();
        $itemsFiltrado = array();
        
        if(count($array) > 0){
            foreach($array as $i){
                foreach($i->items as $p){
                    if (isset($p->estado)) {
                        $producto = Producto::traer_un_producto_nombre($p->nombre);
    
                        // Verificar si $producto no es null antes de acceder a la propiedad 'sector'
                        if ($producto !== null && $producto->sector == $sector) {
                            if($estado != null){
                                if($p->estado == $estado){
                                    array_push($itemsFiltrado, $p);
                                }
                            } else {
                                array_push($itemsFiltrado, $p);
                            }
                        }
                    }
                }
                
                $i->items = $itemsFiltrado;
                $itemsFiltrado = array();
                
                if(count($i->items) > 0){
                    array_push($arrayFiltrado, $i);
                }
            }
        }
        
        return $arrayFiltrado;
    }
    

    // public static function filtrar_segun_sector($array, $sector, $estado = null){
    //     $arrayFiltrado = array();
    //     $itemsFiltrado = array();

    //     if(count($array) > 0){
    //         foreach($array as $i){
    //             foreach($i->items as $p){
    //                 $producto = Producto::traer_un_producto_nombre($p->nombre);

    //                 if($producto->sector == $sector){
    //                     if($estado != null){
    //                         if($p->estado == $estado){
    //                             array_push($itemsFiltrado, $p);
    //                         }
                            
    //                     }
    //                     else{
    //                         array_push($itemsFiltrado, $p);
    //                     }
    //                 }
    //             }
    //             $i->items = $itemsFiltrado;
    //             $itemsFiltrado = array();
    //             if(count($i->items) > 0){
    //                 array_push($arrayFiltrado, $i);
    //             }
    //         }
    //     }
    //     return $arrayFiltrado;
    // }

    public static function filtrar_segun_estado($array, $estado){
        $arrayFiltrado = array();
        $itemsFiltrado = array();
        if(count($array) > 0){
            foreach($array as $i){
                foreach($i->items as $p){
                    if($p->estado == $estado){
                        
                        array_push($itemsFiltrado, $p);
                    }
                }
                $i->items = $itemsFiltrado;
                $itemsFiltrado = array();
                if(count($i->items) > 0){
                    array_push($arrayFiltrado, $i);
                }
            }
        }
        return $arrayFiltrado;
    }

    public static function comprobar_estado_pedido_listo($array){
        $arrayFiltrado = array();
        $itemsFiltrado = array();
        if(count($array) > 0){
            foreach($array as $i){
                $items_cantidad = count($i->items);
                foreach($i->items as $p){
                    if($p->estado == 2){
                        
                        array_push($itemsFiltrado, $p);
                    }
                }
                if($items_cantidad == count($itemsFiltrado)){
                    $i->items = $itemsFiltrado;
                    array_push($arrayFiltrado, $i);
                }
                $itemsFiltrado = array();
            }
        }
        return $arrayFiltrado;
    }
}

?>