<?php

header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-Disposition: inline; filename="files.json"');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

$respuesta = array('success' => false, 'fichero' => '', 'msg' => '');
$fichero_temporal = $_FILES['imagen']['tmp_name'];
$ancho_requerido = isset($_POST['width']) ? $_POST['width'] : '';
$alto_requerido = isset($_POST['height']) ? $_POST['height'] : '';
$nombre_fichero = time() . '_' . rand(1000, 9999);
$ruta_temporal = 'tmpupload/' . $nombre_fichero . '.png';

list($ancho, $alto, $tipo_imagen, $attr) = getimagesize($fichero_temporal);

if ($tipo_imagen != IMAGETYPE_JPEG) {
    $respuesta['msg'] = 'Solo se aceptan imágenes jpg/jpeg.';
} else if ($ancho != $ancho_requerido || $alto != $alto_requerido) {
    $respuesta['msg'] = 'La imagen debe tener ' . $ancho_requerido . 'px de ancho por ' . $alto_requerido . 'px de alto.';
} else if (move_uploaded_file($fichero_temporal, $ruta_temporal)) {
    $respuesta['success'] = true;
    $respuesta['fichero'] = $ruta_temporal;
}

print json_encode($respuesta);
?>
