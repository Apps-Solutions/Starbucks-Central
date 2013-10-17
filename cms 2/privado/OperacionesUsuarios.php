<?php

class OperacionesUsuarios {

    public static function listado_perfiles(PDO $bd_link) {
        $sql = "SELECT FIIDPERFIL, FCNOMBRE FROM taperfilesusuarios;";

        $result = $bd_link->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insertar(PDO $bd_link, $nombre, $usuario, $clave, $perfil) {
        $sql = "INSERT INTO tausuarios (FCNOMBRE, FCUSUARIO, FCCLAVE, FIIDPERFIL, FDFECHA)";
        $sql.= " VALUES ('" . $nombre . "','" . $usuario . "',SHA1('" . $clave . "')," . $perfil . ",SYSDATE())";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function actualizar(PDO $bd_link, $id_usuario, $nombre, $usuario, $clave, $perfil) {
        $sql = "UPDATE tausuarios SET";
        $sql.= " FCNOMBRE = '" . $nombre . "',";
        if (strlen($clave) > 0) {
            $sql.= " FCCLAVE = SHA1('" . $clave . "'),";
        }
        $sql.= " FCUSUARIO = '" . $usuario . "',";
        $sql.= " FIIDPERFIL = " . $perfil;
        $sql.= " WHERE FIIDUSUARIO = " . $id_usuario;

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function eliminar(PDO $bd_link, $id_usuario) {
        # Eliminar usuario
        $sql = "DELETE FROM tausuarios";
        $sql.= " WHERE FIIDUSUARIO = " . $id_usuario . ";";

        if ($bd_link->exec($sql) === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }
    }

    public static function listado_usuarios(PDO $bd_link) {
        $datos = array();

        $sql = "SELECT tu.FIIDUSUARIO, tu.FCNOMBRE, tu.FCUSUARIO, tpu.FCNOMBRE AS 'NOMBREPERFIL' , tu.FDFECHA";
        $sql.= " FROM tausuarios tu, taperfilesusuarios tpu";
        $sql.= " WHERE tu.FIIDPERFIL = tpu.FIIDPERFIL";
        $sql.= " ORDER BY FCNOMBRE;";

        $result = $bd_link->query($sql);

        while ($row = $result->fetchObject()) {
            $datos[] = array(
                'id_usuario' => $row->FIIDUSUARIO,
                'nombre' => $row->FCNOMBRE,
                'usuario' => $row->FCUSUARIO,
                'perfil' => $row->NOMBREPERFIL,
                'fecha' => $row->FDFECHA
            );
        }

        return $datos;
    }

    public static function informacion_usuario(PDO $bd_link, $id_usuario) {
        $datos = array();

        $sql = "SELECT FIIDUSUARIO, FCNOMBRE, FCUSUARIO, FIIDPERFIL";
        $sql.= " FROM tausuarios";
        $sql.= " WHERE FIIDUSUARIO = " . $id_usuario . ";";

        $result = $bd_link->query($sql);
        if ($result === FALSE) {
            $mensaje_error = $bd_link->errorInfo();
            $mensaje_error = $mensaje_error[2];
            throw new Exception($mensaje_error);
        }

        $fila = $result->fetchObject();

        $datos = array(
            'id_usuario' => $fila->FIIDUSUARIO,
            'nombre' => $fila->FCNOMBRE,
            'usuario' => $fila->FCUSUARIO,
            'id_perfil' => $fila->FIIDPERFIL
        );

        return $datos;
    }

}

?>
