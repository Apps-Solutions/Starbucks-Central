<?php

class Operaciones {

    public static function comprobar_login(PDO $bd_link, $usuario, $clave) {
        $_SESSION['ERROR'] = 'si';
        $_SESSION['LOGADO'] = false;

        $sql = "SELECT FIIDUSUARIO, FCNOMBRE, FCUSUARIO, FIIDPERFIL";
        $sql.= " FROM tausuarios";
        $sql.= " WHERE FCUSUARIO = " . $bd_link->quote($usuario);
        $sql.= " AND FCCLAVE = SHA1(" . $bd_link->quote($clave) . ");";

        $result = $bd_link->query($sql);

        if ($result === FALSE) {
            $detalles_error = $bd_link->errorInfo();
            throw new Exception($detalles_error[2]);
            die();
        }

        $row = $result->fetchObject();

        if ($row === FALSE) {
            return false;
        } else {
            $_SESSION['LOGADO'] = true;
            $_SESSION['ID_USUARIO'] = $row->FIIDUSUARIO;
            $_SESSION['NOMBRE_USUARIO'] = $row->FCNOMBRE;
            $_SESSION['CORREO_USUARIO'] = $row->FCUSUARIO;
            $_SESSION['NIVEL_USUARIO'] = $row->FIIDPERFIL;
            $_SESSION['ERROR'] = 'no';

            return true;
        }
    }

    public static function numero_paginas($total_registros, $registros_por_pagina) {
        $pagina = ceil($total_registros / $registros_por_pagina);
        return $pagina;
    }

    public static function insertar_producto(PDO $bd_link, $parametros) {
        $nombre = $parametros['nombre'];
        $descripcion = $parametros['descripcion'];
        $disponibilidad = $parametros['disponibilidad'];
        $estado = $parametros['estado'];

        $sql = "INSERT INTO taproductos (FCNOMBRE, FCDESCRIPCION, FIIDDISPONIBILIDAD,FIIDESTADO) VALUES (";
        $sql.= "'" . $nombre . "',";
        $sql.= "'" . $descripcion . "',";
        $sql.= $disponibilidad . ",";
        $sql.= $estado;
        $sql.= ");";

        if (!$bd_link->exec($sql)) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $bd_link->lastInsertId();
    }

    public static function total_productos(PDO $bd_link, $id_estado, $nombre) {
        $condiciones = array();
        $cantidad_productos = 0;

        $sql = "SELECT count(FIIDPRODUCTO) as 'total'";
        $sql.= " FROM taproductos";
        if (strlen($nombre) > 0) {
            $condiciones[] = " FCNOMBRE like '%" . $nombre . "%'";
        }
        if (strlen($id_estado) > 0) {
            $condiciones[] = " FIIDESTADO = " . $id_estado;
        }

        if (count($condiciones) > 0) {
            $sql.= " WHERE " . implode(' AND ', $condiciones);
        }

        $result = $bd_link->query($sql);
        $cantidad_productos = $result->fetchColumn();

        return $cantidad_productos;
    }

    public static function listado_productos(PDO $bd_link) {
        $datos = array();

        $sql = "SELECT FIIDPRODUCTO, FCNOMBRE, FCDESCRIPCION, FIIDESTADO";
        $sql.= " FROM taproductos";
        $sql.= " ORDER BY FIIDPRODUCTO DESC";

        $result = $bd_link->query($sql);

        while ($row = $result->fetchObject()) {
            $datos[] = array(
                'id_producto' => $row->FIIDPRODUCTO,
                'nombre' => $row->FCNOMBRE,
                'descripcion' => $row->FCDESCRIPCION,
                'id_estado' => $row->FIIDESTADO
            );
        }

        return $datos;
    }

    public static function eliminar_imagenes_del_disco(array $ids_imagenes) {
        foreach ($ids_imagenes as $id_imagen) {
            unlink('imagenes/productos/' . $id_imagen . '.png');
        }
    }

    public static function dame_ids_imagenes(PDO $bd_link, $id_producto) {
        $sql = "SELECT FIIDIMAGEN FROM taimagenes";
        $sql.= " WHERE FIIDPRODUCTO = " . $id_producto . " ORDER BY FIORDEN ASC;";

        $result = $bd_link->query($sql);

        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        $ids_imagenes = array();

        while ($fila = $result->fetchObject()) {
            $ids_imagenes[] = $fila->FIIDIMAGEN;
        }

        return $ids_imagenes;
    }

    public static function eliminar_producto(PDO $bd_link, $id_producto) {
        # Eliminar producto en cascada
        $sql = "DELETE FROM taproductos WHERE FIIDPRODUCTO = " . $id_producto . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function informacion_producto(PDO $bd_link, $id_producto) {
        $datos = array();

        $sql = "SELECT FCNOMBRE, FCDESCRIPCION, FIIDDISPONIBILIDAD, FIIDESTADO";
        $sql.= " FROM taproductos";
        $sql.= " WHERE FIIDPRODUCTO = " . $id_producto . ";";

        $result = $bd_link->query($sql);

        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        $fila = $result->fetchObject();

        if ($fila !== FALSE) {
            $datos = array(
                'nombre' => $fila->FCNOMBRE,
                'descripcion' => $fila->FCDESCRIPCION,
                'id_disponibilidad' => $fila->FIIDDISPONIBILIDAD,
                'id_estado' => $fila->FIIDESTADO
            );
        }

        return $datos;
    }

    public static function listado_estados(PDO $bd_link) {
        $sql = "SELECT FIIDESTADO, FCNOMBRE FROM taestadosproductos";

        $result = $bd_link->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function dame_id_imagen_principal(PDO $bd_link, $id_producto) {

        $sql = "SELECT FIIDIMAGEN FROM taimagenes WHERE FIIDPRODUCTO = " . $id_producto . " AND FIORDEN = 1";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        $id_imagen_principal = $result->fetchColumn();
        return $id_imagen_principal;
    }

    public static function imagen_to_blob($ruta_imagen) {
        $fp = fopen($ruta_imagen, "rb");
        $imagen = fread($fp, filesize($ruta_imagen));
        $imagen = addslashes($imagen);
        fclose($fp);
        return $imagen;
    }

    public static function insertar_imagen_principal(PDO $bd_link, $id_producto, $ruta_imagen) {
        $imagen = self::imagen_to_blob($ruta_imagen);

        $sql = "INSERT INTO taimagenes (FBIMAGEN,FIORDEN,FIIDPRODUCTO,FDFECHA) VALUES (";
        $sql.= "'" . $imagen . "',";
        $sql.= "1,";
        $sql.= $id_producto . ",";
        $sql.= "SYSDATE()";
        $sql.= ");";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function eliminar_imagenes(PDO $bd_link, $id_producto, $ids_imagenes) {

        foreach ($ids_imagenes as $id_imagen) {
            $sql = "DELETE FROM taimagenes WHERE FIIDPRODUCTO = " . $id_producto . " AND FIIDIMAGEN = " . $id_imagen;

            if ($bd_link->exec($sql) === FALSE) {
                $mensaje_error = $bd_link->errorInfo();
                $mensaje_error = $mensaje_error[2];
                throw new Exception($mensaje_error);
            }
        }
    }

    public static function dame_ids_imagenes_galeria(PDO $bd_link, $id_producto) {
        $sql = "SELECT FIIDIMAGEN FROM taimagenes";
        $sql.= " WHERE FIIDPRODUCTO = " . $id_producto;
        $sql.= " AND FIORDEN != 1 ORDER BY FIORDEN ASC;";

        $result = $bd_link->query($sql);

        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        $ids_imagenes = $result->fetchAll();
        return $ids_imagenes;
    }

    public static function actualizar_producto(PDO $bd_link, $id_producto, $nombre, $descripcion, $id_disponibilidad, $id_estado) {

        $sql = "UPDATE taproductos SET";
        $sql.= " FCNOMBRE = '" . $nombre . "',";
        $sql.= " FCDESCRIPCION = '" . $descripcion . "',";
        $sql.= " FIIDDISPONIBILIDAD = " . $id_disponibilidad . ",";
        $sql.= " FIIDESTADO = " . $id_estado;
        $sql.= " WHERE FIIDPRODUCTO = " . $id_producto . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function listado_disponibilidad(PDO $bd_link) {
        $sql = "SELECT FIIDDISPONIBILIDAD, FCNOMBRE FROM tadisponibilidades";

        $result = $bd_link->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insertar_disponibilidad(PDO $bd_link, $nombre) {
        $sql = "INSERT INTO tadisponibilidades (FCNOMBRE) VALUES ('" . $nombre . "');";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function actualizar_disponibilidad(PDO $bd_link, $id, $nombre) {
        $sql = "UPDATE tadisponibilidades SET";
        $sql.= " FCNOMBRE = '" . $nombre . "'";
        $sql.= " WHERE FIIDDISPONIBILIDAD = " . $id . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function eliminar_disponibilidad(PDO $bd_link, $id) {
        $sql = "DELETE FROM tadisponibilidades WHERE FIIDDISPONIBILIDAD = " . $id . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function imagen_blob(PDO $bd_link, $id_imagen) {
        $sql = "SELECT FBIMAGEN FROM taimagenes WHERE FIIDIMAGEN = " . $id_imagen . ";";

        $result = $bd_link->query($sql);
        return $result->fetchColumn();
    }

    public static function comprobar_derechos($pagina_actual, $redireccionamiento = 'dashboard.php') {
        $paginas_denegadas_marca = array('form_add_usuario', 'form_editar_usuario', 'usuarios', 'listado_usuarios', 'combos', 'administracion_combos', 'form_add_opcion_combo_simple', 'form_editar_opcion_combo_simple','form_add_categorias_tipos_bebidas','form_editar_categorias_tipos_bebidas');
        $paginas_permitidas_it = array('form_add_usuario', 'form_editar_usuario', 'usuarios', 'listado_usuarios', 'combos', 'administracion_combos', 'form_add_opcion_combo_simple', 'form_editar_opcion_combo_simple','form_add_categorias_tipos_bebidas','form_editar_categorias_tipos_bebidas');

        if (in_array($pagina_actual, $paginas_denegadas_marca) && !in_array($_SESSION['NIVEL_USUARIO'], array(1, 2))) {
            header('Location: ' . $redireccionamiento);
            die();
        } else if (!in_array($pagina_actual, $paginas_permitidas_it) && $_SESSION['NIVEL_USUARIO'] == 2) {
            header('Location: ' . $redireccionamiento);
            die();
        }
    }

}

?>
