<?php
require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/Operaciones.php';
require_once 'privado/OperacionesCafes.php';
require_once 'privado/OperacionesComida.php';
require_once 'privado/OperacionesTiendas.php';
require_once 'privado/conecta_db.php';
Operaciones::comprobar_derechos('form_editar_opcion_combo_simple');

$bd_link = conecta_db();
$tipos_validos = array('disponibilidad_todo', 'perfiles_cafe', 'formas_cafe', 'sabores_cafe', 'alergenos_comida', 'categorias_comida', 'servicios_tienda');
$titulo_formulario = isset($_GET['titulo']) ? $_GET['titulo'] : '';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';

if (!in_array($tipo, $tipos_validos)) {
    header('Location: administracion_combos.php');
    die();
}

require_once 'includes/cabecera.php';
?>


<script type="text/javascript">

    $(document).ready(function() {
        $("#guardar").button();
        $("#guardar").click(function() {
            var nombre = jQuery.trim($('#nombre').val());
  
            if (nombre.length == 0){
                alert('El campo nombre es obligatorio.');
                return false;
            }

            $('#loader_guardando').show();

            $.ajax({
                type: "POST",
                url: "combos.php",
                data: {
                    id: '<?php print $id;?>',
                    nombre: nombre,
                    tipo: '<?php print $tipo; ?>',
                    accion: 'actualizar_opcion'
                }
            }).done(function(resultado) {
                $('#loader_guardando').hide();
                resultado = jQuery.parseJSON(resultado);
                if (resultado.success == true){
                    document.location.href = 'administracion_combos.php?combo=<?php print $tipo; ?>';
                }else{
                    alert('No ha sido posible dar de alta la opción');
                }
            });

            return false;
        });
    });

</script>

<div class="holder">

    <table style="border: 1px solid #D3D3D3;width: 98%;margin: 10px;padding: 20px">
        <tr>
            <td colspan="2" style="text-align: center;font-weight: bold;font-size: 1.3em;padding-bottom: 30px"><span class="titulo_formulario"><?php print $titulo_formulario; ?></span></td>
        </tr>

        <tr>
            <td class="etiqueta_campo" style="width:100px">Nombre <span class="color_campo_obligatorio">*</span></td>
            <td><input id="nombre" name="nombre" type="text" value="<?php print $nombre; ?>" style="width:250px"></td>
        </tr>

        <tr>
            <td colspan="2" class="color_campo_obligatorio" style="text-align: right">* Obligatorio</td>
        </tr>

        <tr>
            <td></td>
            <td><br/><button id="guardar">Guardar</button><img id="loader_guardando" src="gfx/images/ajax-loader.gif" style="display: none" /><br/><br/></td>
        </tr>

    </table>

</div>

<?php require_once 'includes/pie.php'; ?>
