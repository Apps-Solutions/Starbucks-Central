<?php

require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/conecta_db.php';
require_once 'privado/Operaciones.php';
require_once 'privado/Imagenes.php';
require_once 'privado/OperacionesBebidas.php';
require_once 'privado/GenerarXML.php';

$respuesta = array('success' => false, 'msg' => '');
$ac = isset($_REQUEST['accion']) ? $_REQUEST['accion'] : '';

$bd_link = conecta_db();

if ($ac == 'alta_bebida') {

    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $descripcion_corta = isset($_POST['descripcion_corta']) ? $_POST['descripcion_corta'] : '';
    $disponibilidad = isset($_POST['disponibilidad']) ? $_POST['disponibilidad'] : '';
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
    $subcategoria = isset($_POST['subcategoria']) ? $_POST['subcategoria'] : '';
    $marcado_decaf = isset($_POST['marcado_decaf']) ? $_POST['marcado_decaf'] : '';
    $marcado_shots = isset($_POST['marcado_shots']) ? $_POST['marcado_shots'] : '';
    $marcado_jarabe = isset($_POST['marcado_jarabe']) ? $_POST['marcado_jarabe'] : '';
    $marcado_leche = isset($_POST['marcado_leche']) ? $_POST['marcado_leche'] : '';
    $marcado_pers = isset($_POST['marcado_pers']) ? $_POST['marcado_pers'] : '';
    $marcado_bebida = isset($_POST['marcado_bebida']) ? $_POST['marcado_bebida'] : '';
    $shot = isset($_POST['shot']) ? $_POST['shot'] : '';
    $imagen_principal = isset($_POST['imagen_principal']) ? $_POST['imagen_principal'] : '';
    $ingredientes = isset($_POST['ingredientes']) ? $_POST['ingredientes'] : array();
    $estado = isset($_POST['estado']) ? $_POST['estado'] : 2;

    $subcategoria = is_numeric($subcategoria) ? $subcategoria : $categoria;

    $parametros = array(
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'disponibilidad' => $disponibilidad,
        'estado' => $estado
    );

    $bd_link->beginTransaction();

    try {

        $id_producto = Operaciones::insertar_producto($bd_link, $parametros);
        OperacionesBebidas::insertar_bebida($bd_link, $id_producto, $descripcion_corta, $marcado_decaf, $marcado_shots, $marcado_jarabe, $marcado_leche, $marcado_pers, $marcado_bebida, $shot, $subcategoria);
        if (is_array($ingredientes) && count($ingredientes) > 0) {
            OperacionesBebidas::insertar_ingredientes($bd_link, $id_producto, $ingredientes);
        }

        Operaciones::insertar_imagen_principal($bd_link, $id_producto, $imagen_principal);
        Imagenes::eliminar_del_dico($imagen_principal);

        $bd_link->commit();
        GenerarXML::xml_bebidas($bd_link);
        $respuesta['success'] = true;
    } catch (Exception $exc) {
        $bd_link->rollBack();
        throw new Exception($exc->getMessage(), $exc->getCode());
    }

    print json_encode($respuesta);
} else if ($ac == 'editar_bebida') {
    $id_producto = isset($_POST['id']) ? $_POST['id'] : '';
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $descripcion_corta = isset($_POST['descripcion_corta']) ? $_POST['descripcion_corta'] : '';
    $disponibilidad = isset($_POST['disponibilidad']) ? $_POST['disponibilidad'] : '';
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
    $subcategoria = isset($_POST['subcategoria']) ? $_POST['subcategoria'] : '';
    $marcado_decaf = isset($_POST['marcado_decaf']) ? $_POST['marcado_decaf'] : '';
    $marcado_shots = isset($_POST['marcado_shots']) ? $_POST['marcado_shots'] : '';
    $marcado_jarabe = isset($_POST['marcado_jarabe']) ? $_POST['marcado_jarabe'] : '';
    $marcado_leche = isset($_POST['marcado_leche']) ? $_POST['marcado_leche'] : '';
    $marcado_pers = isset($_POST['marcado_pers']) ? $_POST['marcado_pers'] : '';
    $marcado_bebida = isset($_POST['marcado_bebida']) ? $_POST['marcado_bebida'] : '';
    $shot = isset($_POST['shot']) ? $_POST['shot'] : '';
    $imagen_principal = isset($_POST['imagen_principal']) ? $_POST['imagen_principal'] : '';
    $imagen_principal_eliminada = isset($_POST['imagen_principal_eliminada']) ? $_POST['imagen_principal_eliminada'] : '';
    $ingredientes = isset($_POST['ingredientes']) ? $_POST['ingredientes'] : array();
    $estado = isset($_POST['estado']) ? $_POST['estado'] : 2;

    $subcategoria = is_numeric($subcategoria) ? $subcategoria : $categoria;

    if (!is_numeric($id_producto)) {
        print json_encode($respuesta);
        die();
    }

    $bd_link->beginTransaction();
    try {
        Operaciones::actualizar_producto($bd_link, $id_producto, $nombre, $descripcion, $disponibilidad, $estado);
        OperacionesBebidas::actualizar_bebida($bd_link, $id_producto, $descripcion_corta, $marcado_decaf, $marcado_shots, $marcado_jarabe, $marcado_leche, $marcado_pers, $marcado_bebida, $shot, $subcategoria);
        OperacionesBebidas::actualizar_ingredientes($bd_link, $id_producto, $ingredientes);

        if (!is_numeric($imagen_principal)) {
            Operaciones::insertar_imagen_principal($bd_link, $id_producto, $imagen_principal);
        }

        if (is_numeric($imagen_principal_eliminada)) {
            Operaciones::eliminar_imagenes($bd_link, $id_producto, array($imagen_principal_eliminada));
        }

        # Si no ha ocurrido ningun error y se ha subido una nueva imagen principal, eliminamos la imagen temporal
        if (!is_numeric($imagen_principal)) {
            Imagenes::eliminar_del_dico($imagen_principal);
        }

        $bd_link->commit();
        GenerarXML::xml_bebidas($bd_link);
        $respuesta['success'] = true;
    } catch (Exception $exc) {
        $bd_link->rollBack();
        throw new Exception($exc->getMessage(), $exc->getCode());
    }

    print json_encode($respuesta);
} else if ($ac == 'eliminar_bebida') {
    $id_producto = isset($_GET['id']) ? $_GET['id'] : '';

    if (!is_numeric($id_producto)) {
        header('Location: listado_bebidas.php');
    }

    $bd_link->beginTransaction();
    try {
        Operaciones::eliminar_producto($bd_link, $id_producto);
        $bd_link->commit();
        GenerarXML::xml_bebidas($bd_link);
    } catch (Exception $exc) {
        $bd_link->rollBack();
        throw new Exception($exc->getMessage(), $exc->getCode());
    }

    header('Location: listado_bebidas.php');
} else if ($ac == 'listado_categorias') {
    $respuesta['categorias'] = array();
    $id_tipo = isset($_POST['id_tipo']) ? $_POST['id_tipo'] : '';

    $categorias = OperacionesBebidas::listado_categorias($bd_link, $id_tipo);

    foreach ($categorias as $categoria) {
        $respuesta['categorias'][] = array(
            'id' => $categoria['FIIDCATEGORIABEBIDA'],
            'nombre' => $categoria['FCNOMBRE']
        );
    }

    $respuesta['success'] = true;
    print json_encode($respuesta);
} else if ($ac == 'listado_subcategorias') {
    $respuesta['subcategorias'] = array();
    $id_categoria = isset($_POST['id_categoria']) ? $_POST['id_categoria'] : '';

    $subcategorias = OperacionesBebidas::listado_subcategorias($bd_link, $id_categoria);

    foreach ($subcategorias as $subcategoria) {
        $respuesta['subcategorias'][] = array(
            'id' => $subcategoria['FIIDCATEGORIABEBIDA'],
            'nombre' => $subcategoria['FCNOMBRE']
        );
    }

    $respuesta['success'] = true;
    print json_encode($respuesta);
} else if ($ac == 'listado_ingredientes') {
    $respuesta['ingredientes'] = array();
    $id_categoria = isset($_POST['id_categoria']) ? $_POST['id_categoria'] : '';

    $ingredientes = OperacionesBebidas::listado_ingredientes($bd_link, $id_categoria);

    foreach ($ingredientes as $ingrediente) {
        $respuesta['ingredientes'][] = array(
            'id' => $ingrediente['FIIDINGREDIENTE'],
            'nombre' => $ingrediente['FCNOMBRE']
        );
    }

    $respuesta['success'] = true;
    print json_encode($respuesta);
}
?>
