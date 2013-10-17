<?php

class OperacionesTiendas {

    public static function listado_provincias(PDO $bd_link) {
        $sql = "SELECT FIIDESTADO, FCNOMBRE FROM taestados ORDER BY FCNOMBRE;";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listado_dias(PDO $bd_link) {
        $sql = "SELECT FIIDDIA, FCNOMBRE FROM tadiassemana ORDER BY FIIDDIA;";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listado_servicios(PDO $bd_link) {
        $sql = "SELECT FIIDSERVICIO, FCNOMBRE FROM taservicios;";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insertar_tienda(PDO $bd_link, array $parametros) {
        $nombre = $parametros['nombre'];
        $direccion = $parametros['direccion'];
        $codigo_postal = $parametros['codigo_postal'];
        $ciudad = $parametros['ciudad'];
        $provincia = $parametros['provincia'];
        $zona = $parametros['zona'];
        $latitud = $parametros['latitud'];
        $longitud = $parametros['longitud'];

        $sql = "INSERT INTO tatiendas (FCNOMBRE,FCDIRECCION,FCCODIGOPOSTAL,FCZONA,FCCIUDAD,FIIDESTADO,FCLATITUD,FCLONGITUD)";
        $sql.= " VALUES ('" . $nombre . "','" . $direccion . "','" . $codigo_postal . "','" . $zona . "','" . $ciudad . "'," . $provincia . ",'" . $latitud . "','" . $longitud . "');";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        return $bd_link->lastInsertId();
    }

    public static function actualizar_tienda(PDO $bd_link, $id_tienda, array $parametros) {
        $nombre = $parametros['nombre'];
        $direccion = $parametros['direccion'];
        $codigo_postal = $parametros['codigo_postal'];
        $ciudad = $parametros['ciudad'];
        $provincia = $parametros['provincia'];
        $zona = $parametros['zona'];
        $latitud = $parametros['latitud'];
        $longitud = $parametros['longitud'];

        $sql = "UPDATE tatiendas SET";
        $sql.= " FCNOMBRE = '" . $nombre . "',";
        $sql.= " FCDIRECCION = '" . $direccion . "',";
        $sql.= " FCCODIGOPOSTAL = '" . $codigo_postal . "',";
        $sql.= " FCZONA = '" . $zona . "',";
        $sql.= " FCCIUDAD = '" . $ciudad . "',";
        $sql.= " FIIDESTADO = " . $provincia . ",";
        $sql.= " FCLATITUD = '" . $latitud . "',";
        $sql.= " FCLONGITUD = '" . $longitud . "'";
        $sql.= " WHERE FIIDTIENDA = " . $id_tienda . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function insertar_servicios(PDO $bd_link, $id_tienda, array $servicios) {

        foreach ($servicios as $id_servicio) {
            $sql = "INSERT INTO taserviciosxtienda (FIIDSERVICIO,FIIDTIENDA)";
            $sql.= " VALUES (" . $id_servicio . "," . $id_tienda . ");";

            if ($bd_link->exec($sql) === FALSE) {
                $mensaje_error = $bd_link->errorInfo();
                $mensaje_error = $mensaje_error[2];
                throw new Exception($mensaje_error);
            }
        }
    }

    public static function insertar_servicio(PDO $bd_link, $nombre) {
        $sql = "INSERT INTO taservicios (FCNOMBRE) VALUES ('" . $nombre . "');";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function actualizar_servicio(PDO $bd_link, $id, $nombre) {
        $sql = "UPDATE taservicios SET";
        $sql.= " FCNOMBRE = '" . $nombre . "'";
        $sql.= " WHERE FIIDSERVICIO = " . $id . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function eliminar_servicio(PDO $bd_link, $id) {
        $sql = "DELETE FROM taservicios WHERE FIIDSERVICIO = " . $id . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function actualizar_servicios(PDO $bd_link, $id_tienda, array $servicios) {
        # Eliminar servicios tienda
        $sql = "DELETE FROM taserviciosxtienda WHERE FIIDTIENDA = " . $id_tienda . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }


        self::insertar_servicios($bd_link, $id_tienda, $servicios);
    }

    public static function insertar_horario(PDO $bd_link, $id_tienda, array $horario) {

        foreach ($horario as $datos_horario) {
            if ($datos_horario == 'cerrado') {
                continue;
            }

            list($id_dia, $desde, $hasta) = explode('-', $datos_horario);

            $sql = "INSERT INTO tahorarios (FIIDDIA,FIIDTIENDA,FCHORAMINUTOSINICIO,FCHORAMINUTOSFIN)";
            $sql.= " VALUES (" . $id_dia . "," . $id_tienda . ",'" . $desde . "','" . $hasta . "');";

            if ($bd_link->exec($sql) === FALSE) {
                $mensaje_error = $bd_link->errorInfo();
                $mensaje_error = $mensaje_error[2];
                throw new Exception($mensaje_error);
            }
        }
    }

    public static function actualizar_horario(PDO $bd_link, $id_tienda, array $horario) {
        $sql = "DELETE FROM tahorarios WHERE FIIDTIENDA = " . $id_tienda . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        self::insertar_horario($bd_link, $id_tienda, $horario);
    }

    public static function listado_tiendas(PDO $bd_link) {
        $datos = array();

        $sql = "SELECT tt.FIIDTIENDA, tt.FCNOMBRE, tt.FCDIRECCION, tt.FCCODIGOPOSTAL, tt.FCCIUDAD, tp.FCNOMBRE AS 'PROVINCIA'";
        $sql.= " FROM tatiendas tt, taestados tp";
        $sql.= " WHERE tt.FIIDESTADO = tp.FIIDESTADO;";

        $result = $bd_link->query($sql);

        while ($row = $result->fetchObject()) {
            $datos[] = array(
                'id_tienda' => $row->FIIDTIENDA,
                'nombre' => $row->FCNOMBRE,
                'direccion' => $row->FCDIRECCION,
                'codigopostal' => $row->FCCODIGOPOSTAL,
                'ciudad' => $row->FCCIUDAD,
                'provincia' => $row->PROVINCIA
            );
        }

        return $datos;
    }

    public static function eliminar_tienda(PDO $bd_link, $id_tienda) {
        $sql = "DELETE FROM tatiendas WHERE FIIDTIENDA = " . $id_tienda . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function informacion_tienda(PDO $bd_link, $id_tienda) {
        $datos = array();

        $sql = "SELECT FIIDTIENDA, FCNOMBRE, FCDIRECCION, FCCODIGOPOSTAL, FCZONA, FCCIUDAD, FIIDESTADO, FCLATITUD, FCLONGITUD";
        $sql.= " FROM tatiendas WHERE FIIDTIENDA = " . $id_tienda . ";";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        $fila = $result->fetchObject();

        $datos = array(
            'id_tienda' => $fila->FIIDTIENDA,
            'nombre' => $fila->FCNOMBRE,
            'direccion' => $fila->FCDIRECCION,
            'codigopostal' => $fila->FCCODIGOPOSTAL,
            'zona' => $fila->FCZONA,
            'ciudad' => $fila->FCCIUDAD,
            'id_provincia' => $fila->FIIDESTADO,
            'latitud' => $fila->FCLATITUD,
            'longitud' => $fila->FCLONGITUD
        );

        return $datos;
    }

    public static function listado_horario(PDO $bd_link, $id_tienda) {
        $datos = array();

        $sql = "SELECT th.FIIDDIA, tds.FCNOMBRE AS 'DIA', th.FCHORAMINUTOSINICIO, th.FCHORAMINUTOSFIN";
        $sql.= " FROM tahorarios th, tadiassemana tds";
        $sql.= " WHERE th.FIIDDIA = tds.FIIDDIA";
        $sql.= " AND th.FIIDTIENDA = " . $id_tienda . ";";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        while ($fila = $result->fetchObject()) {
            $id_dia = $fila->FIIDDIA;
            $datosdia = $fila->FIIDDIA . '-' . $fila->FCHORAMINUTOSINICIO . '-' . $fila->FCHORAMINUTOSFIN;
            $texto = $fila->DIA . ' de ' . $fila->FCHORAMINUTOSINICIO . ' a ' . $fila->FCHORAMINUTOSFIN;

            $datos[] = array(
                'id_dia' => $id_dia,
                'datosdia' => $datosdia,
                'texto' => $texto
            );
        }

        return $datos;
    }

    public static function listado_servicios_tienda(PDO $bd_link, $id_tienda) {
        $datos = array();

        $sql = "SELECT tst.FIIDSERVICIO, ts.FCNOMBRE";
        $sql.= " FROM taservicios ts, taserviciosxtienda tst";
        $sql.= " WHERE ts.FIIDSERVICIO = tst.FIIDSERVICIO";
        $sql.= " AND tst.FIIDTIENDA = " . $id_tienda . ";";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        while ($fila = $result->fetchObject()) {
            $datos[] = array(
                'id_servicio' => $fila->FIIDSERVICIO,
                'nombre' => $fila->FCNOMBRE
            );
        }

        return $datos;
    }

}

?>
