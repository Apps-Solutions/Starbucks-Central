<?php
require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/Operaciones.php';
require_once 'privado/OperacionesComida.php';
require_once 'privado/conecta_db.php';
Operaciones::comprobar_derechos('form_editar_comida');
$id_producto = isset($_GET['id']) ? trim($_GET['id']) : '';
if (!is_numeric($id_producto)) {
    header('Location: listado_comidas.php');
    die();
}
require_once 'includes/cabecera.php';

$bd_link = conecta_db();
try {
    $datos_producto = Operaciones::informacion_producto($bd_link, $id_producto);
    if (count($datos_producto) == 0) {
        header('Location: listado_comidas.php');
    }

    $datos_comida = OperacionesComida::informacion_comida($bd_link, $id_producto);
    $id_imagen_principal = Operaciones::dame_id_imagen_principal($bd_link, $id_producto);

    $listado_alergenos_comida = OperacionesComida::listado_alergenos_comida($bd_link, $id_producto);
    $listado_combinaciones = OperacionesComida::listado_combinaciones($bd_link, $id_producto);

    $listado_disponibilidad = Operaciones::listado_disponibilidad($bd_link);
    $listado_alergenos = OperacionesComida::listado_alergenos($bd_link);
    $listado_categorias = OperacionesComida::listado_categorias($bd_link);
    $listado_cafes = OperacionesComida::listado_cafes_combina_con($bd_link);
    $listado_estados = Operaciones::listado_estados($bd_link);
} catch (Exception $exc) {
    throw new Exception($exc->getMessage(), $exc->getCode());
    die();
}
?>


<script type="text/javascript">

    $(document).ready(function() {
        var imagen_principal = <?php print $id_imagen_principal; ?>;
        var imagen_principal_eliminada = '';

        var registrar_evento_eliminar_alergeno = function(){
            $("#listado_alergenos > li > img").click(function() {
                $(this).parent().fadeOut(200,function(){
                    $(this).remove();
                });
            });

            $("#listado_alergenos > li > img").hover(function() {
                $(this).css('cursor','pointer');
            }, function() {
                $(this).css('cursor','auto');
            });
        }

        var registrar_evento_eliminar_cafe = function(){
            $("#listado_cafes > li > img").click(function() {
                $(this).parent().fadeOut(200,function(){
                    $(this).remove();
                });
            });

            $("#listado_cafes > li > img").hover(function() {
                $(this).css('cursor','pointer');
            }, function() {
                $(this).css('cursor','auto');
            });
        }

        $("#alergeno").change(function(){
            var texto_alergeno = $("#alergeno option:selected").html();
            var id_alergeno = $("#alergeno option:selected").val();
            var existe = false;

            if (!$.isNumeric(id_alergeno)){
                return false;
            }

            $('#listado_alergenos > li').each(function(index) {
                if ($(this).attr('idalergeno') == id_alergeno){
                    existe = true;
                    return;
                }
            });

            if (!existe){
                $("#listado_alergenos").append('<li idalergeno="'+id_alergeno+'"><img src="gfx/images/eliminar_circulo_16.png" alt="" /> '+texto_alergeno+'</li>');
                registrar_evento_eliminar_alergeno();
            }

            $("#alergeno").val("");
            return false;
        });

        $("#cafe").change(function(){
            var texto_cafe = $("#cafe option:selected").html();
            var id_cafe = $("#cafe option:selected").val();
            var existe = false;

            if (!$.isNumeric(id_cafe)){
                return false;
            }

            $('#listado_cafes > li').each(function(index) {
                if ($(this).attr('idcafe') == id_cafe){
                    existe = true;
                    return;
                }
            });

            if (!existe){
                $("#listado_cafes").append('<li idcafe="'+id_cafe+'"><img src="gfx/images/eliminar_circulo_16.png" alt="" /> '+texto_cafe+'</li>');
                registrar_evento_eliminar_cafe();
            }

            $("#cafe").val("");
            return false;
        });

        $('#input_imagen_principal').fileupload({
            url: 'gestor_imagenes.php',
            type: 'POST',
            formData: {width: 512,height: 512},
            dataType: 'json',
            send: function (e, data) {
                $('#contenedor-imagen-principal').html('<img src="gfx/images/ajax-loader.gif"/>');
            },
            done: function (e, data) {
                $('#contenedor-imagen-principal').html('');
                if (data.result.success == true){
                    if (jQuery.isNumeric(imagen_principal)){
                        imagen_principal_eliminada = imagen_principal;
                    }
                    imagen_principal = data.result.fichero;
                    $('#contenedor-imagen-principal').html('<img src="'+data.result.fichero+'" alt="" style="height: 56px"/>');
                }else{
                    if (jQuery.isNumeric(imagen_principal)){
                        $('#contenedor-imagen-principal').html('<img src="imagen.php?id='+imagen_principal+'" alt="" style="height: 56px"/>');
                    }else{
                        $('#contenedor-imagen-principal').html('<img src="'+imagen_principal+'" alt="" style="height: 56px"/>');
                    }

                    if (data.result.msg != ""){
                        alert(data.result.msg);
                    }else{
                        alert('No ha sido posible subir la imagen');
                    }
                }
            },
            fail:function (e, data) {
                $('#contenedor-imagen-principal').html('');
                alert('No ha sido posible subir la imagen');
            }
        });

        var validar_caracteres_descripcion = function(){
            var caracteres = $('#descripcion').val();

            var longitud = caracteres.length;
            if (longitud >= 300) {
                $('#descripcion').val(caracteres.substring(0, 300));
                $('#caracteres_descripcion').text(' 300');
            } else {
                $('#caracteres_descripcion').text(' '+longitud);
            }

            caracteres = $('#descripcion_corta').val();

            longitud = caracteres.length;
            if (longitud >= 150) {
                $('#descripcion_corta').val(caracteres.substring(0, 150));
                $('#caracteres_descripcion_corta').text(' 150');
            } else {
                $('#caracteres_descripcion_corta').text(' '+longitud);
            }

        };

        $("#descripcion").keyup(function(){
            validar_caracteres_descripcion();
        });

        $("#descripcion_corta").keyup(function(){
            validar_caracteres_descripcion();
        });

        $("#guardar").button();
        $("#guardar").click(function() {
            var nombre = jQuery.trim($('#nombre').val());
            var descripcion = jQuery.trim($('#descripcion').val());
            var descripcion_corta = jQuery.trim($('#descripcion_corta').val());
            var disponibilidad = jQuery.trim($('#disponibilidad').val());
            var categoria = jQuery.trim($('#categoria').val());
            var estado = jQuery.trim($('#estado').val());

            var alergenos = new Array();
            $('#listado_alergenos > li').each(function(index) {
                alergenos.push($(this).attr('idalergeno'));
            });

            var cafes = new Array();
            $('#listado_cafes > li').each(function(index) {
                cafes.push($(this).attr('idcafe'));
            });

            if (nombre.length == 0){
                alert('El campo nombre es obligatorio.');
                return false;
            }else if (descripcion.length == 0){
                alert('El campo descripción es obligatorio.');
                return false;
            }else if (descripcion_corta.length == 0){
                alert('El campo descripción corta es obligatorio.');
                return false;
            }else if (disponibilidad.length == 0){
                alert('El campo disponibilidad es obligatorio.');
                return false;
            }else if (categoria.length == 0){
                alert('El campo categoria es obligatorio.');
                return false;
            }else if (imagen_principal.length == 0){
                alert('Debe especificar la imagen principal');
                return false;
            }

            $('#loader_guardando').show();

            $.ajax({
                type: "POST",
                url: "comidas.php",
                data: {
                    id: <?php print $id_producto; ?>,
                    nombre: nombre,
                    descripcion: descripcion,
                    descripcion_corta: descripcion_corta,
                    disponibilidad: disponibilidad,
                    categoria: categoria,
                    imagen_principal: imagen_principal,
                    imagen_principal_eliminada: imagen_principal_eliminada,
                    alergenos: alergenos,
                    cafes: cafes,
                    estado: estado,
                    accion: 'editar_comida'
                }
            }).done(function(resultado) {
                $('#loader_guardando').hide();
                resultado = jQuery.parseJSON(resultado);
                if (resultado.success == true){
                    document.location.href = 'listado_comidas.php';
                }else{
                    alert('No ha sido posible editar el alimento');
                }
            });

            return false;
        });

        registrar_evento_eliminar_alergeno();
        registrar_evento_eliminar_cafe();

        $('#disponibilidad').val(<?php print $datos_producto['id_disponibilidad']; ?>);
        $('#categoria').val(<?php print $datos_comida['id_categoria']; ?>);
        $('#estado').val(<?php print $datos_producto['id_estado']; ?>);
        validar_caracteres_descripcion();
    });

</script>

<style>
    #sortable { list-style-type: none; margin: 0; padding: 0; }
    #sortable li { margin: 3px 3px 3px 3px; padding: 1px 1px 1px 1px; height: 76px;float: left; text-align: center; border: none }
</style>

<div class="holder">

    <table style="border: 1px solid #D3D3D3;width: 98%;margin: 10px;padding: 20px">
        <tr>
            <td colspan="2" style="text-align: center;font-weight: bold;font-size: 1.3em;padding-bottom: 30px"><span class="titulo_formulario">Formulario de edici&oacute;n de alimento</span></td>
        </tr>
        <tr>
            <td class="etiqueta_campo">Nombre <span class="color_campo_obligatorio">*</span></td>
            <td><input id="nombre" name="nombre" type="text" value="<?php print $datos_producto['nombre']; ?>" ></td>
        </tr>

        <tr>
            <td class="etiqueta_campo">Descripci&oacute;n <span class="color_campo_obligatorio">*</span></td>
            <td colspan="2"><textarea id="descripcion" name="descripcion" style="width:350px;height:50px"><?php print $datos_producto['descripcion']; ?></textarea><span id="caracteres_descripcion"> 0</span> caracteres</td>
        </tr>

        <tr>
            <td class="etiqueta_campo">Descripci&oacute;n corta <span class="color_campo_obligatorio">*</span></td>
            <td colspan="2"><textarea id="descripcion_corta" name="descripcion_corta" style="width:300px;height:50px"><?php print $datos_comida['descripcion_corta']; ?></textarea><span id="caracteres_descripcion_corta"> 0</span> caracteres</td>
        </tr>

        <tr>
            <td class="etiqueta_campo">Disponibilidad <span class="color_campo_obligatorio">*</span></td>
            <td>
                <select id="disponibilidad" name="disponibilidad">
                    <?php foreach ($listado_disponibilidad as $disponibilidad): ?>
                        <option value="<?php print $disponibilidad['FIIDDISPONIBILIDAD']; ?>"><?php print $disponibilidad['FCNOMBRE']; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td class="etiqueta_campo">Categor&iacute;a <span class="color_campo_obligatorio">*</span></td>
                <td>
                    <select id="categoria" name="categoria">
                    <?php foreach ($listado_categorias as $categoria): ?>
                            <option value="<?php print $categoria['FIIDCATEGORIACOMIDA']; ?>"><?php print $categoria['FCNOMBRE']; ?></option>
                    <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td class="etiqueta_campo">Imagen principal <span class="color_campo_obligatorio">*</span></td>
                    <td>
                        <table style="padding: 0px;margin: 0px" cellpadding="0" cellspacing="0">
                            <tr>
                                <td><input id="input_imagen_principal" type="file" name="imagen" /></td>
                                <td id="contenedor-imagen-principal"><img src="imagen.php?id=<?php print $id_imagen_principal; ?>" alt="" style="height: 56px"/></td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td class="etiqueta_campo">Al&eacute;rgenos</td>
                    <td>
                        <select id="alergeno" name="alergeno">
                            <option value="">--seleccione--</option>
                    <?php foreach ($listado_alergenos as $alergeno): ?>
                                <option value="<?php print $alergeno['FIIDALERGENO']; ?>"><?php print $alergeno['FCNOMBRE']; ?></option>
                    <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
                            <fieldset style="width: 200px">
                                <legend>Listado de al&eacute;rgenos</legend>
                                <ul id="listado_alergenos" class="lista_sin_formato">
                        <?php foreach ($listado_alergenos_comida as $alergeno_comida): ?>
                                    <li idalergeno="<?php print $alergeno_comida['FIIDALERGENO']; ?>"><img src="gfx/images/eliminar_circulo_16.png" alt="" /> <?php print $alergeno_comida['FCNOMBRE']; ?></li>
                        <?php endforeach; ?>
                                </ul>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <td class="etiqueta_campo">Combina con</td>
                        <td>
                            <select id="cafe" name="cafe">
                                <option value="">--seleccione caf&eacute;--</option>
                    <?php foreach ($listado_cafes as $cafe): ?>
                                        <option value="<?php print $cafe['id_producto']; ?>"><?php print $cafe['nombre']; ?></option>
                    <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                                <td>
                                    <fieldset style="width: 200px">
                                        <legend>Listado caf&eacute;s</legend>
                                        <ul id="listado_cafes" class="lista_sin_formato">
                        <?php foreach ($listado_combinaciones as $combinacion): ?>
                                            <li idcafe="<?php print $combinacion['FIIDCAFE']; ?>"><img src="gfx/images/eliminar_circulo_16.png" alt="" /> <?php print $combinacion['FCNOMBRE']; ?></li>
                        <?php endforeach; ?>
                                        </ul>
                                    </fieldset>
                                </td>
                            </tr>

                            <tr>
                                <td class="etiqueta_campo">Estado <span class="color_campo_obligatorio">*</span></td>
                                <td>
                                    <select id="estado" name="estado">
                    <?php foreach ($listado_estados as $estado): ?>
                                                <option value="<?php print $estado['FIIDESTADO']; ?>"><?php print $estado['FCNOMBRE']; ?></option>
                    <?php endforeach; ?>
                                            </select>
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
