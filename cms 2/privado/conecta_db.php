<?php

function conecta_db() {
    $bd_link = new PDO('mysql:host=' . HOST_BD . ';dbname=' . NOMBRE_DB, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
    return $bd_link;
}

?>
