<?php

class OperacionesCafes {

    public static function listado_perfiles(PDO $bd_link) {
        $sql = "SELECT FIIDPERFIL, FCNOMBRE FROM taperfiles";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insertar_perfil(PDO $bd_link, $nombre) {
        $sql = "INSERT INTO taperfiles (FCNOMBRE) VALUES ('" . $nombre . "');";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function actualizar_perfil(PDO $bd_link, $id, $nombre) {
        $sql = "UPDATE taperfiles SET";
        $sql.= " FCNOMBRE = '" . $nombre . "'";
        $sql.= " WHERE FIIDPERFIL = " . $id . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function eliminar_perfil(PDO $bd_link, $id) {
        $sql = "DELETE FROM taperfiles WHERE FIIDPERFIL = " . $id . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function insertar_forma(PDO $bd_link, $nombre) {
        $sql = "INSERT INTO taformas (FCNOMBRE) VALUES ('" . $nombre . "');";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function actualizar_forma(PDO $bd_link, $id, $nombre) {
        $sql = "UPDATE taformas SET";
        $sql.= " FCNOMBRE = '" . $nombre . "'";
        $sql.= " WHERE FIIDFORMA = " . $id . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function eliminar_forma(PDO $bd_link, $id) {
        $sql = "DELETE FROM taformas WHERE FIIDFORMA = " . $id . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function listado_formas(PDO $bd_link) {
        $sql = "SELECT FIIDFORMA, FCNOMBRE FROM taformas";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listado_sabores(PDO $bd_link) {
        $sql = "SELECT FIIDSABOR, FCNOMBRE FROM tasabores ORDER BY FCNOMBRE";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insertar_cafe(PDO $bd_link, $id_producto, $id_perfil, $id_forma) {

        $sql = "INSERT INTO tacafes (FIIDPRODUCTO,FIIDPERFIL,FIIDFORMA) VALUES (";
        $sql.= $id_producto . ",";
        $sql.= $id_perfil . ",";
        $sql.= $id_forma;
        $sql.= ");";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function listado_cafes(PDO $bd_link) {
        $datos = array();

        $sql = "SELECT p.FIIDPRODUCTO, p.FCNOMBRE, p.FCDESCRIPCION, p.FIIDESTADO";
        $sql.= " FROM taproductos p, tacafes c";
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

    public static function insertar_imagenes_galeria(PDO $bd_link, $id_producto, array $imagenes) {
        $orden = 2;

        foreach ($imagenes as $ruta_imagen) {
            $imagen = Operaciones::imagen_to_blob($ruta_imagen);

            $sql = "INSERT INTO taimagenes (FBIMAGEN,FIORDEN,FIIDPRODUCTO,FDFECHA) VALUES (";
            $sql.= "'" . $imagen . "',";
            $sql.= $orden++ . ",";
            $sql.= $id_producto . ",";
            $sql.= "SYSDATE()";
            $sql.= ");";

            if ($bd_link->exec($sql) === FALSE) {
                $mensaje_error = $bd_link->errorInfo();
                $mensaje_error = $mensaje_error[2];
                throw new Exception($mensaje_error);
            }
        }
    }

    public static function actualizar_imagenes_galeria(PDO $bd_link, $id_producto, array $imagenes) {
        $orden = 2;

        foreach ($imagenes as $ruta_imagen) {
            if (is_numeric($ruta_imagen)) {
                $sql = "UPDATE taimagenes SET FIORDEN = " . $orden++ . " WHERE FIIDIMAGEN = " . $ruta_imagen . " AND FIIDPRODUCTO = " . $id_producto . ";";
            } else {
                $imagen = Operaciones::imagen_to_blob($ruta_imagen);

                $sql = "INSERT INTO taimagenes (FBIMAGEN,FIORDEN,FIIDPRODUCTO) VALUES (";
                $sql.= "'" . $imagen . "',";
                $sql.= $orden++ . ",";
                $sql.= $id_producto;
                $sql.= ");";
            }

            if ($bd_link->exec($sql) === FALSE) {
                $mensaje_error = $bd_link->errorInfo();
                $mensaje_error = $mensaje_error[2];
                throw new Exception($mensaje_error);
            }
        }
    }

    public static function insertar_sabores(PDO $bd_link, $id_producto, array $sabores) {

        foreach ($sabores as $id_sabor) {
            $sql = "INSERT INTO tasaboresxcafe (FIIDCAFE,FIIDSABOR) VALUES (";
            $sql.= $id_producto . ",";
            $sql.= $id_sabor;
            $sql.= ");";

            if ($bd_link->exec($sql) === FALSE) {
                $mensaje_error = $bd_link->errorInfo();
                $mensaje_error = $mensaje_error[2];
                throw new Exception($mensaje_error);
            }
        }
    }

    public static function insertar_sabor(PDO $bd_link, $nombre) {
        $sql = "INSERT INTO tasabores (FCNOMBRE) VALUES ('" . $nombre . "');";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function actualizar_sabor(PDO $bd_link, $id, $nombre) {
        $sql = "UPDATE tasabores SET";
        $sql.= " FCNOMBRE = '" . $nombre . "'";
        $sql.= " WHERE FIIDSABOR = " . $id . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function eliminar_sabor(PDO $bd_link, $id) {
        $sql = "DELETE FROM tasabores WHERE FIIDSABOR = " . $id . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function informacion_cafe(PDO $bd_link, $id_producto) {
        $datos = array();

        $sql = "SELECT FIIDPERFIL, FIIDFORMA";
        $sql.= " FROM tacafes";
        $sql.= " WHERE FIIDPRODUCTO = " . $id_producto . ";";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        $fila = $result->fetchObject();

        $datos = array(
            'id_perfil' => $fila->FIIDPERFIL,
            'id_forma' => $fila->FIIDFORMA
        );

        return $datos;
    }

    public static function listado_sabores_cafes(PDO $bd_link, $id_producto) {

        $sql = "SELECT tsc.FIIDSABOR, s.FCNOMBRE";
        $sql.= " FROM tasaboresxcafe tsc, tasabores s";
        $sql.= " WHERE tsc.FIIDSABOR = s.FIIDSABOR";
        $sql.= " AND tsc.FIIDCAFE = " . $id_producto . ";";

        $result = $bd_link->query($sql);

        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        $datos = $result->fetchAll();
        return $datos;
    }

    public static function actualizar_cafe(PDO $bd_link, $id_producto, $id_perfil, $id_forma) {

        $sql = "UPDATE tacafes SET";
        $sql.= " FIIDPERFIL = " . $id_perfil . ",";
        $sql.= " FIIDFORMA = " . $id_forma;
        $sql.= " WHERE FIIDPRODUCTO = " . $id_producto . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function actualizar_sabores(PDO $bd_link, $id_producto, $sabores) {
        # Eliminar sabores
        $sql = "DELETE FROM tasaboresxcafe WHERE FIIDCAFE = " . $id_producto . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        self::insertar_sabores($bd_link, $id_producto, $sabores);
    }

}

?>
