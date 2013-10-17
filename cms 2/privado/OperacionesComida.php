<?php

class OperacionesComida {

    public static function listado_alergenos(PDO $bd_link) {
        $sql = "SELECT FIIDALERGENO, FCNOMBRE FROM taalergenos";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listado_categorias(PDO $bd_link) {
        $sql = "SELECT FIIDCATEGORIACOMIDA, FCNOMBRE FROM tacategoriascomidas;";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insertar_categoria(PDO $bd_link, $nombre) {
        $sql = "INSERT INTO tacategoriascomidas (FCNOMBRE) VALUES ('" . $nombre . "');";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function actualizar_categoria(PDO $bd_link, $id, $nombre) {
        $sql = "UPDATE tacategoriascomidas SET";
        $sql.= " FCNOMBRE = '" . $nombre . "'";
        $sql.= " WHERE FIIDCATEGORIACOMIDA = " . $id . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function eliminar_categoria(PDO $bd_link, $id) {
        $sql = "DELETE FROM tacategoriascomidas WHERE FIIDCATEGORIACOMIDA = " . $id . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function listado_cafes_combina_con(PDO $bd_link) {
        $datos = array();

        $sql = "SELECT p.FIIDPRODUCTO, p.FCNOMBRE";
        $sql.= " FROM taproductos p, tacafes c";
        $sql.= " WHERE p.FIIDPRODUCTO = c.FIIDPRODUCTO";
        $sql.= " AND p.FIIDESTADO = 1";
        $sql.= " ORDER BY p.FCNOMBRE";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        while ($row = $result->fetchObject()) {
            $datos[] = array(
                'id_producto' => $row->FIIDPRODUCTO,
                'nombre' => $row->FCNOMBRE
            );
        }

        return $datos;
    }

    public static function insertar_comida(PDO $bd_link, $id_producto, $descripcion_corta, $id_categoria) {

        $sql = "INSERT INTO tacomidas (FIIDPRODUCTO, FCDESCRIPCIONCORTA, FIIDCATEGORIACOMIDA)";
        $sql.= " VALUES (" . $id_producto . ",'" . $descripcion_corta . "'," . $id_categoria . ");";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function insertar_alergenos(PDO $bd_link, $id_producto, array $alergenos) {

        foreach ($alergenos as $id_alergeno) {
            $sql = "INSERT INTO taalergenosxcomida (FIIDCOMIDA, FIIDALERGENO)";
            $sql.= " VALUES (" . $id_producto . "," . $id_alergeno . ");";

            if ($bd_link->exec($sql) === FALSE) {
                $mensaje_error = $bd_link->errorInfo();
                $mensaje_error = $mensaje_error[2];
                throw new Exception($mensaje_error);
            }
        }
    }

    public static function insertar_alergeno(PDO $bd_link, $nombre) {
        $sql = "INSERT INTO taalergenos (FCNOMBRE) VALUES ('" . $nombre . "');";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function actualizar_alergeno(PDO $bd_link, $id, $nombre) {
        $sql = "UPDATE taalergenos SET";
        $sql.= " FCNOMBRE = '" . $nombre . "'";
        $sql.= " WHERE FIIDALERGENO = " . $id . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function eliminar_alergeno(PDO $bd_link, $id) {
        $sql = "DELETE FROM taalergenos WHERE FIIDALERGENO = " . $id . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function insertar_combinaciones(PDO $bd_link, $id_producto, array $combinaciones) {

        foreach ($combinaciones as $id_cafe) {
            $sql = "INSERT INTO tacombinacionescomidas (FIIDCOMIDA, FIIDCAFE)";
            $sql.= " VALUES (" . $id_producto . "," . $id_cafe . ");";

            if ($bd_link->exec($sql) === FALSE) {
                $mensaje_error = $bd_link->errorInfo();
                $mensaje_error = $mensaje_error[2];
                throw new Exception($mensaje_error);
            }
        }
    }

    public static function listado_comida(PDO $bd_link) {
        $datos = array();

        $sql = "SELECT p.FIIDPRODUCTO, p.FCNOMBRE, p.FCDESCRIPCION, p.FIIDESTADO";
        $sql.= " FROM taproductos p, tacomidas c";
        $sql.= " WHERE p.FIIDPRODUCTO = c.FIIDPRODUCTO";
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

    public static function informacion_comida(PDO $bd_link, $id_producto) {
        $datos = array();

        $sql = "SELECT FCDESCRIPCIONCORTA, FIIDCATEGORIACOMIDA";
        $sql.= " FROM tacomidas";
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
            'id_categoria' => $fila->FIIDCATEGORIACOMIDA
        );

        return $datos;
    }

    public static function listado_alergenos_comida(PDO $bd_link, $id_producto) {

        $sql = "SELECT tca.FIIDALERGENO, ta.FCNOMBRE";
        $sql.= " FROM taalergenosxcomida tca, taalergenos ta";
        $sql.= " WHERE tca.FIIDALERGENO = ta.FIIDALERGENO";
        $sql.= " AND tca.FIIDCOMIDA = " . $id_producto . ";";

        $result = $bd_link->query($sql);

        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        $datos = $result->fetchAll();
        return $datos;
    }

    public static function listado_combinaciones(PDO $bd_link, $id_producto) {

        $sql = "SELECT tc.FIIDCAFE, tp.FCNOMBRE";
        $sql.= " FROM tacombinacionescomidas tc, taproductos tp";
        $sql.= " WHERE tc.FIIDCAFE = tp.FIIDPRODUCTO";
        $sql.= " AND tc.FIIDCOMIDA = " . $id_producto . ";";

        $result = $bd_link->query($sql);

        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        $datos = $result->fetchAll();
        return $datos;
    }

    public static function actualizar_comida(PDO $bd_link, $id_producto, $descripcion_corta, $id_categoria) {

        $sql = "UPDATE tacomidas SET";
        $sql.= " FCDESCRIPCIONCORTA = '" . $descripcion_corta . "',";
        $sql.= " FIIDCATEGORIACOMIDA = " . $id_categoria;
        $sql.= " WHERE FIIDPRODUCTO = " . $id_producto . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function actualizar_alergenos(PDO $bd_link, $id_producto, $alergenos) {
        # Eliminar alergenos
        $sql = "DELETE FROM taalergenosxcomida WHERE FIIDCOMIDA = " . $id_producto . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        self::insertar_alergenos($bd_link, $id_producto, $alergenos);
    }

    public static function actualizar_combinaciones(PDO $bd_link, $id_producto, $combinaciones) {
        # Eliminar combinaciones
        $sql = "DELETE FROM tacombinacionescomidas WHERE FIIDCOMIDA = " . $id_producto . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        self::insertar_combinaciones($bd_link, $id_producto, $combinaciones);
    }

}

?>
