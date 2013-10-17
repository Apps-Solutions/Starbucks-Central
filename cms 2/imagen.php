<?php

require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/conecta_db.php';
require_once 'privado/Operaciones.php';

$id_imagen = isset($_GET['id']) ? $_GET['id'] : '';
if (is_numeric($id_imagen)) {
    $bd_link = conecta_db();
    $imagen = Operaciones::imagen_blob($bd_link, $id_imagen);

    header("Content-Type: image/png");
    echo $imagen;
}
?>
