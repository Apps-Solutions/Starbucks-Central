<?php
require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/Operaciones.php';
require_once 'privado/OperacionesCafes.php';
require_once 'privado/OperacionesComida.php';
require_once 'privado/OperacionesTiendas.php';
require_once 'privado/conecta_db.php';
Operaciones::comprobar_derechos('form_editar_categorias_tipos_bebidas');

$bd_link = conecta_db();
$titulo_formulario = isset($_GET['titulo']) ? $_GET['titulo'] : '';
$tipo_bebida = '';

require_once 'includes/cabecera.php';
?>


<script type="text/javascript">

    $(document).ready(function() {

        var registrar_evento_eliminar_subcategoria = function(){
            $("#listado_subcategorias > li > img").click(function() {
                $(this).parent().fadeOut(200,function(){
                    $(this).remove();
                });
            });

            $("#listado_subcategorias > li > img").hover(function() {
                $(this).css('cursor','pointer');
            }, function() {
                $(this).css('cursor','auto');
            });
        }

        $("#add_subcategoria").click(function(){
            var nombre_subcategoria = jQuery.trim($("#nombre_subcategoria").val());
            var existe = false;

            if (nombre_subcategoria.length == 0){
                return false;
            }

            $('#listado_subcategorias > li').each(function(index) {
                if (jQuery.trim($(this).text()) == nombre_subcategoria){
                    existe = true;
                    return;
                }
            });

            if (!existe){
                $("#listado_subcategorias").append('<li idsubcategoria=""><img src="gfx/images/eliminar_circulo_16.png" alt="" /> '+nombre_subcategoria+'</li>');
                registrar_evento_eliminar_subcategoria();
            }

            $("#nombre_subcategoria").val("");
            return false;
        });

        $("#guardar").button();
        $("#guardar").click(function() {
            var nombre_categoria = jQuery.trim($('#nombre_categoria').val());
  
            if (nombre_categoria.length == 0){
                alert('El campo nombre categoría es obligatorio.');
                return false;
            }

            var subcategorias = new Array();
            $('#listado_subcategorias > li').each(function(index) {
                subcategorias.push(jQuery.trim($(this).text()));
            });

            $('#loader_guardando').show();

            $.ajax({
                type: "POST",
                url: "combos.php",
                data: {
                    nombre_categoria: nombre_categoria,
                    subcategorias: subcategorias,
                    id_tipo_bebida: '<?php print $tipo_bebida; ?>',
                    accion: 'alta_categoria_tipo_bebida'
                }
            }).done(function(resultado) {
                $('#loader_guardando').hide();
                resultado = jQuery.parseJSON(resultado);
                if (resultado.success == true){
                    document.location.href = 'administracion_combos.php?combo=tipos_bebida&id_tipo_bebida=<?php print $tipo_bebida; ?>';
                }else{
                    alert('No ha sido posible dar de alta la categoría.');
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
            <td class="etiqueta_campo" style="width:155px">Nombre categor&iacute;a <span class="color_campo_obligatorio">*</span></td>
            <td><input id="nombre_categoria" name="nombre_categoria" type="text" style="width:250px"></td>
        </tr>

        <tr>
            <td class="etiqueta_campo">Nombre subcategor&iacute;a <span class="color_campo_obligatorio">*</span></td>
            <td>
                <input type="text" id="nombre_subcategoria" name="nombre_subcategoria" style="width:250px">
                <button id="add_subcategoria">A&ntilde;adir</button>
            </td>
        </tr>

        <tr>
            <td></td>
            <td>
                <fieldset style="width: 200px">
                    <legend>Listado de subcategor&iacute;as</legend>
                    <ul id="listado_subcategorias" class="lista_sin_formato"></ul>
                </fieldset>
            </td>
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
