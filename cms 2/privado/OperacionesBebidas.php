<?php

class OperacionesBebidas {

    public static function listado_medidas(PDO $bd_link) {
        $sql = "SELECT FIIDMEDIDA, FCNOMBRE FROM tamedidas;";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listado_tipos(PDO $bd_link) {
        $sql = "SELECT FIIDTEMPERATURA, FCNOMBRE FROM tatemperaturas;";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listado_categorias(PDO $bd_link, $id_tipo) {
        $sql = "SELECT FIIDCATEGORIABEBIDA, FCNOMBRE FROM tacategoriasbebidas";
        $sql.= " WHERE FIIDTEMPERATURA = " . $id_tipo;
        $sql.= " AND FIIDCATEGORIAPADRE IS NULL;";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listado_subcategorias(PDO $bd_link, $id_categoria) {
        $sql = "SELECT FIIDCATEGORIABEBIDA, FCNOMBRE FROM tacategoriasbebidas";
        $sql.= " WHERE FIIDCATEGORIAPADRE = " . $id_categoria . ";";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listado_categorias_ingredientes(PDO $bd_link) {
        $sql = "SELECT FIIDCATEGORIAINGREDIENTE, FCNOMBRE FROM tacategoriasingredientes";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listado_ingredientes(PDO $bd_link, $id_categoria) {
        $sql = "SELECT FIIDINGREDIENTE, FCNOMBRE FROM taingredientes";
        $sql.= " WHERE FIIDCATEGORIAINGREDIENTE = " . $id_categoria . ";";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insertar_categoria(PDO $bd_link, $nombre, $id_temperatura, $id_categoria_padre = 'NULL') {

        $sql = "INSERT INTO tacategoriasbebidas (FCNOMBRE, FIIDTEMPERATURA, FIIDCATEGORIAPADRE)";
        $sql.= " VALUES ('" . $nombre . "'," . $id_temperatura . "," . $id_categoria_padre . ");";

        if (!$bd_link->exec($sql)) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $bd_link->lastInsertId();
    }

    public static function eliminar_categoria(PDO $bd_link, $id) {
        $sql = "DELETE FROM tacategoriasbebidas WHERE FIIDCATEGORIABEBIDA = " . $id . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function insertar_bebida(PDO $bd_link, $id_producto, $descripcion_corta, $marcado_decaf, $marcado_shots, $marcado_jarabe, $marcado_leche, $marcado_pers, $marcado_bebida, $shot, $id_categoria) {
        $shot = is_numeric($shot) ? $shot : 'NULL';
        $marcado_decaf = strlen($marcado_decaf) > 0 ? "'" . $marcado_decaf . "'" : 'NULL';
        $marcado_shots = strlen($marcado_shots) > 0 ? "'" . $marcado_shots . "'" : 'NULL';
        $marcado_jarabe = strlen($marcado_jarabe) > 0 ? "'" . $marcado_jarabe . "'" : 'NULL';
        $marcado_leche = strlen($marcado_leche) > 0 ? "'" . $marcado_leche . "'" : 'NULL';
        $marcado_pers = strlen($marcado_pers) > 0 ? "'" . $marcado_pers . "'" : 'NULL';
        $marcado_bebida = strlen($marcado_bebida) > 0 ? "'" . $marcado_bebida . "'" : 'NULL';

        $sql = "INSERT INTO tabebidas (FIIDPRODUCTO, FCDESCRIPCIONCORTA, FCMARCADODECAF, FCMARCADOSHOTS, FCMARCADOJARABE, FCMARCADOLECHE, FCMARCADOPERS, FCMARCADOBEBIDA, FISHOT, FIIDCATEGORIABEBIDA)";
        $sql.= " VALUES (" . $id_producto . ",'" . $descripcion_corta . "'," . strtoupper($marcado_decaf) . "," . strtoupper($marcado_shots) . "," . strtoupper($marcado_jarabe) . "," . strtoupper($marcado_leche) . "," . strtoupper($marcado_pers) . "," . strtoupper($marcado_bebida) . "," . $shot . "," . $id_categoria . ");";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function insertar_medidas(PDO $bd_link, $id_producto, array $medidas) {

        foreach ($medidas as $id_medida) {
            $sql = "INSERT INTO tabebidasmedidas (FIIDBEBIDA,FIIDMEDIDA)";
            $sql.= " VALUES (" . $id_producto . "," . $id_medida . ");";

            if ($bd_link->exec($sql) === FALSE) {
                $mensaje_error = $bd_link->errorInfo();
                $mensaje_error = $mensaje_error[2];
                throw new Exception($mensaje_error);
            }
        }
    }

    public static function insertar_ingredientes(PDO $bd_link, $id_producto, array $ingredientes) {

        foreach ($ingredientes as $id_ingrediente) {
            $sql = "INSERT INTO taingredientesxbebida (FIIDINGREDIENTE,FIIDBEBIDA)";
            $sql.= " VALUES (" . $id_ingrediente . "," . $id_producto . ");";

            if ($bd_link->exec($sql) === FALSE) {
                $mensaje_error = $bd_link->errorInfo();
                $mensaje_error = $mensaje_error[2];
                throw new Exception($mensaje_error);
            }
        }
    }

    public static function listado_bebidas(PDO $bd_link) {
        $datos = array();

        $sql = "SELECT p.FIIDPRODUCTO, p.FCNOMBRE, p.FCDESCRIPCION, p.FIIDESTADO";
        $sql.= " FROM taproductos p, tabebidas b";
        $sql.= " WHERE p.FIIDPRODUCTO = b.FIIDPRODUCTO";
        $sql.= " ORDER BY p.FIIDPRODUCTO DESC, p.FIIDESTADO ASC";

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

    public static function listado_ingredientes_bebida(PDO $bd_link, $id_producto) {

        $sql = "SELECT tbi.FIIDINGREDIENTE, ti.FCNOMBRE AS 'NOMBREINGREDIENTE', tci.FCNOMBRE AS 'NOMBRECATEGORIA'";
        $sql.= " FROM taingredientesxbebida tbi, taingredientes ti, tacategoriasingredientes tci";
        $sql.= " WHERE tbi.FIIDINGREDIENTE = ti.FIIDINGREDIENTE AND ti.FIIDCATEGORIAINGREDIENTE = tci.FIIDCATEGORIAINGREDIENTE";
        $sql.= " AND tbi.FIIDBEBIDA = " . $id_producto . ";";

        $result = $bd_link->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function informacion_bebida(PDO $bd_link, $id_producto) {
        $datos = array();

        $sql = "SELECT FCDESCRIPCIONCORTA, FCMARCADODECAF, FCMARCADOSHOTS, FCMARCADOJARABE, FCMARCADOLECHE, FCMARCADOPERS, FCMARCADOBEBIDA, FISHOT, FIIDCATEGORIABEBIDA";
        $sql.= " FROM tabebidas";
        $sql.= " WHERE FIIDPRODUCTO = " . $id_producto . ";";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        $fila = $result->fetchObject();
        $datos = array(
            'descripcion_corta' => $fila->FCDESCRIPCIONCORTA,
            'marcado_decaf' => $fila->FCMARCADODECAF,
            'marcado_shots' => $fila->FCMARCADOSHOTS,
            'marcado_jarabe' => $fila->FCMARCADOJARABE,
            'marcado_leche' => $fila->FCMARCADOLECHE,
            'marcado_pers' => $fila->FCMARCADOPERS,
            'marcado_bebida' => $fila->FCMARCADOBEBIDA,
            'shot' => $fila->FISHOT
        );

        $sql = "SELECT tcb_hijo.FIIDCATEGORIABEBIDA IDCATHIJO, tcb_hijo.FCNOMBRE NOMBREHIJO, tcb_padre.FIIDCATEGORIABEBIDA IDCATPADRE, tcb_padre.FCNOMBRE NOMBREPADRE, tcb_hijo.FIIDTEMPERATURA";
        $sql.= " FROM tacategoriasbebidas tcb_hijo LEFT JOIN tacategoriasbebidas tcb_padre";
        $sql.= " ON (tcb_hijo.FIIDCATEGORIAPADRE = tcb_padre.FIIDCATEGORIABEBIDA)";
        $sql.= " WHERE tcb_hijo.FIIDCATEGORIABEBIDA = " . $fila->FIIDCATEGORIABEBIDA . ";";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        $fila = $result->fetchObject();

        if (strlen($fila->IDCATPADRE) == 0) {
            $id_categoria = $fila->IDCATHIJO;
            $nombre_categoria = $fila->NOMBREHIJO;
        } else {
            $id_categoria = $fila->IDCATPADRE;
            $nombre_categoria = $fila->NOMBREPADRE;
            $id_subcategoria = $fila->IDCATHIJO;
            $nombre_subcategoria = $fila->NOMBREHIJO;
        }

        $datos['id_categoria'] = $id_categoria;
        $datos['nombre_categoria'] = $nombre_categoria;
        $datos['id_subcategoria'] = isset($id_subcategoria) ? $id_subcategoria : '';
        $datos['nombre_subcategoria'] = isset($nombre_subcategoria) ? $nombre_subcategoria : '';
        $datos['id_tipo'] = $fila->FIIDTEMPERATURA;

        return $datos;
    }

    public static function actualizar_bebida(PDO $bd_link, $id_producto, $descripcion_corta, $marcado_decaf, $marcado_shots, $marcado_jarabe, $marcado_leche, $marcado_pers, $marcado_bebida, $shot, $id_categoria) {
        $shot = is_numeric($shot) ? $shot : 'NULL';
        $marcado_decaf = strlen($marcado_decaf) > 0 ? "'" . strtoupper($marcado_decaf) . "'" : 'NULL';
        $marcado_shots = strlen($marcado_shots) > 0 ? "'" . strtoupper($marcado_shots) . "'" : 'NULL';
        $marcado_jarabe = strlen($marcado_jarabe) > 0 ? "'" . strtoupper($marcado_jarabe) . "'" : 'NULL';
        $marcado_leche = strlen($marcado_leche) > 0 ? "'" . strtoupper($marcado_leche) . "'" : 'NULL';
        $marcado_pers = strlen($marcado_pers) > 0 ? "'" . strtoupper($marcado_pers) . "'" : 'NULL';
        $marcado_bebida = strlen($marcado_bebida) > 0 ? "'" . strtoupper($marcado_bebida) . "'" : 'NULL';

        $sql = "UPDATE tabebidas SET";
        $sql.= " FCDESCRIPCIONCORTA = '" . $descripcion_corta . "',";
        $sql.= " FCMARCADODECAF = " . $marcado_decaf . ",";
        $sql.= " FCMARCADOSHOTS = " . $marcado_shots . ",";
        $sql.= " FCMARCADOJARABE = " . $marcado_jarabe . ",";
        $sql.= " FCMARCADOLECHE = " . $marcado_leche . ",";
        $sql.= " FCMARCADOPERS = " . $marcado_pers . ",";
        $sql.= " FCMARCADOBEBIDA = " . $marcado_bebida . ",";
        $sql.= " FISHOT = " . $shot . ",";
        $sql.= " FIIDCATEGORIABEBIDA = " . $id_categoria;
        $sql.= " WHERE FIIDPRODUCTO = " . $id_producto . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function actualizar_ingredientes(PDO $bd_link, $id_producto, $ingredientes) {
        # Eliminar ingredientes
        $sql = "DELETE FROM taingredientesxbebida WHERE FIIDBEBIDA = " . $id_producto . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        self::insertar_ingredientes($bd_link, $id_producto, $ingredientes);
    }

}

?>
