<?php

class Imagenes {

    public static function mover($ruta_origen, $ruta_destino) {
        return rename($ruta_origen, $ruta_destino);
    }

    public static function eliminar_del_dico($ruta_imagen) {
        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen);
        }
    }

}

?>
