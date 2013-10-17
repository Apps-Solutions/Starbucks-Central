<?php
require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/Operaciones.php';
require_once 'privado/OperacionesCafes.php';
require_once 'privado/conecta_db.php';
Operaciones::comprobar_derechos('form_editar_cafe');
$id_producto = isset($_GET['id']) ? trim($_GET['id']) : '';
if (!is_numeric($id_producto)) {
    header('Location: listado_cafes.php');
    die();
}
require_once 'includes/cabecera.php';

$bd_link = conecta_db();
try {
    $datos_producto = Operaciones::informacion_producto($bd_link, $id_producto);
    if (count($datos_producto) == 0) {
        header('Location: listado_cafes.php');
    }

    $datos_cafe = OperacionesCafes::informacion_cafe($bd_link, $id_producto);
    $id_imagen_principal = Operaciones::dame_id_imagen_principal($bd_link, $id_producto);
    $ids_imagenes_galeria = Operaciones::dame_ids_imagenes_galeria($bd_link, $id_producto);
    $listado_sabores_cafes = OperacionesCafes::listado_sabores_cafes($bd_link, $id_producto);

    $listado_disponibilidad = Operaciones::listado_disponibilidad($bd_link);
    $listado_perfiles = OperacionesCafes::listado_perfiles($bd_link);
    $listado_formas = OperacionesCafes::listado_formas($bd_link);
    $listado_sabores = OperacionesCafes::listado_sabores($bd_link);
    $listado_estados = Operaciones::listado_estados($bd_link);
} catch (Exception $exc) {
    throw new Exception($exc->getMessage(), $exc->getCode());
    die();
}
?>


<script type="text/javascript">

    $(document).ready(function() {
        var imagen_principal = <?php print $id_imagen_principal; ?>;     
        var imagenes_eliminadas = new Array();

        $("#sortable").sortable();

        var registrar_evento_eliminar_sabor = function(){
            $("#listado_sabores > li > img").click(function() {
                $(this).parent().fadeOut(200,function(){
                    $(this).remove();
                });
            });

            $("#listado_sabores > li > img").hover(function() {
                $(this).css('cursor','pointer');
            }, function() {
                $(this).css('cursor','auto');
            });
        }

        var registrar_evento_eliminar_imagen = function(){
            $(".eliminar_imagen_cafe").click(function() {
                $(this).parent().fadeOut(300,function(){
                    if ($(this).attr('idimagen') != undefined && $(this).attr('idimagen') != ""){
                        imagenes_eliminadas.push($(this).attr('idimagen'));
                    }
                    $(this).remove();
                });
            });

            $(".eliminar_imagen_cafe").hover(function() {
                $(this).css('cursor','pointer');
            }, function() {
                $(this).css('cursor','auto');
            });

            $(".imagen_cafe").hover(function() {
                $(this).css('cursor','move');
            }, function() {
                $(this).css('cursor','auto');
            });
        }

        $("#sabor").change(function(){
            var texto_sabor = $("#sabor option:selected").html();
            var id_sabor = $("#sabor option:selected").val();
            var existe = false;

            if (!$.isNumeric(id_sabor)){
                return false;
            }

            $('#listado_sabores > li').each(function(index) {
                if ($(this).attr('idsabor') == id_sabor){
                    existe = true;
                    return;
                }
            });

            if (!existe){
                $("#listado_sabores").append('<li idsabor="'+id_sabor+'"><img src="gfx/images/eliminar_circulo_16.png" alt="" /> '+texto_sabor+'</li>');
                registrar_evento_eliminar_sabor();
            }

            $("#sabor").val("");
            return false;
        });

        $('#input_imagen_principal').fileupload({
            url: 'gestor_imagenes.php',
            type: 'POST',
            formData: {width: 640,height: 370},
            dataType: 'json',
            send: function (e, data) {
                $('#contenedor-imagen-principal').html('<img src="gfx/images/ajax-loader.gif"/>');
            },
            done: function (e, data) {
                $('#contenedor-imagen-principal').html('');
                if (data.result.success == true){
                    if (jQuery.isNumeric(imagen_principal)){
                        imagenes_eliminadas.push(imagen_principal);
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

        $('#input_imagen_galeria').fileupload({
            url: 'gestor_imagenes.php',
            type: 'POST',
            formData: {width: 455,height: 684},
            dataType: 'json',
            send: function (e, data) {
                $('#loading_imagen_galeria').html('<img src="gfx/images/ajax-loader.gif"/>');
            },
            done: function (e, data) {
                $('#loading_imagen_galeria').html('');
                if (data.result.success == true){
                    $('.contenedor-galeria-imagenes').append('<li idimagen=""><img idimagen="" src="'+data.result.fichero+'" style="height: 56px" alt="" class="imagen_cafe" /><br><img src="gfx/images/eliminar_16.png" alt="" class="eliminar_imagen_cafe"/></li>');
                    registrar_evento_eliminar_imagen();
                }else{
                    if (data.result.msg != ""){
                        alert(data.result.msg);
                    }else{
                        alert('No ha sido posible subir la imagen');
                    }
                }
            },
            fail:function (e, data) {
                $('#loading_imagen_galeria').html('');
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
        };

        $("#descripcion").keyup(function(){
            validar_caracteres_descripcion();
        });

        $("#guardar").button();
        $("#guardar").click(function() {
            var imagenes = new Array();

            var nombre = jQuery.trim($('#nombre').val());
            var descripcion = jQuery.trim($('#descripcion').val());
            var disponibilidad = jQuery.trim($('#disponibilidad').val());
            var perfil = jQuery.trim($('#perfil').val());
            var forma = jQuery.trim($('#forma').val());
            var estado = jQuery.trim($('#estado').val());

            $('.imagen_cafe').each(function(){
                if ($(this).attr('idimagen') != ""){
                    imagenes.push($(this).attr('idimagen'));
                }else{
                    imagenes.push($(this).attr('src'));
                }
            });

            var sabores = new Array();
            $('#listado_sabores > li').each(function(index) {
                sabores.push($(this).attr('idsabor'));
            });
  
            if (nombre.length == 0){
                alert('El campo nombre es obligatorio.');
                return false;
            }else if (descripcion.length == 0){
                alert('El campo descripción es obligatorio.');
                return false;
            }else if (disponibilidad.length == 0){
                alert('El campo disponibilidad es obligatorio.');
                return false;
            }else if (perfil.length == 0){
                alert('El campo perfil es obligatorio.');
                return false;
            }else if (forma.length == 0){
                alert('El campo forma es obligatorio.');
                return false;
            }else if (imagen_principal.length == 0){
                alert('Debe especificar la imagen principal');
                return false;
            }

            $('#loader_guardando').show();

            $.ajax({
                type: "POST",
                url: "cafes.php",
                data: {
                    id: <?php print $id_producto; ?>,
                    nombre: nombre,
                    descripcion: descripcion,
                    disponibilidad: disponibilidad,
                    perfil: perfil,
                    forma: forma,
                    imagen_principal: imagen_principal,
                    imagenes: imagenes,
                    imagenes_eliminadas: imagenes_eliminadas,
                    sabores: sabores,
                    estado: estado,
                    accion: 'editar_cafe'
                }
            }).done(function(resultado) {
                $('#loader_guardando').hide();
                resultado = jQuery.parseJSON(resultado);
                if (resultado.success == true){
                    document.location.href = 'listado_cafes.php';
                }else{
                    alert('No ha sido posible editar el café');
                }
            });

            return false;
        });

        registrar_evento_eliminar_sabor();
        registrar_evento_eliminar_imagen();

        $('#disponibilidad').val(<?php print $datos_producto['id_disponibilidad']; ?>);
        $('#perfil').val(<?php print $datos_cafe['id_perfil']; ?>);
        $('#forma').val(<?php print $datos_cafe['id_forma']; ?>);
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
            <td colspan="2" style="text-align: center;font-weight: bold;font-size: 1.3em;padding-bottom: 30px"><span class="titulo_formulario">Formulario de edici&oacute;n de caf&eacute;</span></td>
        </tr>
        <tr>
            <td class="etiqueta_campo">Nombre <span class="color_campo_obligatorio">*</span></td>
            <td><input id="nombre" name="nombre" type="text" value="<?php print $datos_producto['nombre']; ?>"></td>
        </tr>

        <tr>
            <td class="etiqueta_campo">Descripci&oacute;n <span class="color_campo_obligatorio">*</span></td>
            <td><textarea id="descripcion" name="descripcion" style="width:350px;height:50px"><?php print $datos_producto['descripcion']; ?></textarea><span id="caracteres_descripcion"> 0</span> caracteres</td>
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
                <td class="etiqueta_campo">Perfil <span class="color_campo_obligatorio">*</span></td>
                <td>
                    <select id="perfil" name="perfil">
                    <?php foreach ($listado_perfiles as $perfil): ?>
                            <option value="<?php print $perfil['FIIDPERFIL']; ?>"><?php print $perfil['FCNOMBRE']; ?></option>
                    <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td class="etiqueta_campo">Forma <span class="color_campo_obligatorio">*</span></td>
                    <td>
                        <select id="forma" name="forma">
                    <?php foreach ($listado_formas as $forma): ?>
                                <option value="<?php print $forma['FIIDFORMA']; ?>"><?php print $forma['FCNOMBRE']; ?></option>
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
                        <td class="etiqueta_campo">Imagen galer&iacute;a</td>
                        <td><input id="input_imagen_galeria" type="file" name="imagen" /><span id="loading_imagen_galeria"></span></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
                            <fieldset style="width: 465px">
                                <legend>Galer&iacute;a de im&aacute;genes. Puede cambiar el orden de las im&aacute;genes arrastr&aacute;ndolas</legend>
                                <ul id="sortable" class="contenedor-galeria-imagenes">
                        <?php foreach ($ids_imagenes_galeria as $id_imagen): ?>
                                    <li idimagen="<?php print $id_imagen['FIIDIMAGEN']; ?>"><img idimagen="<?php print $id_imagen['FIIDIMAGEN']; ?>" src="imagen.php?id=<?php print $id_imagen['FIIDIMAGEN']; ?>" style="height: 56px" alt="" class="imagen_cafe" /><br><img src="gfx/images/eliminar_16.png" alt="" class="eliminar_imagen_cafe"/></li>
                        <?php endforeach; ?>
                                </ul>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <td class="etiqueta_campo">Sabor</td>
                        <td>
                            <select id="sabor" name="sabor">
                                <option value="">--seleccione--</option>
                    <?php foreach ($listado_sabores as $sabor): ?>
                                        <option value="<?php print $sabor['FIIDSABOR']; ?>"><?php print $sabor['FCNOMBRE']; ?></option>
                    <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                                <td>
                                    <fieldset style="width: 200px">
                                        <legend>Sabores</legend>
                                        <ul id="listado_sabores" class="lista_sin_formato">
                        <?php foreach ($listado_sabores_cafes as $sabor_cafe): ?>
                                            <li idsabor="<?php print $sabor_cafe['FIIDSABOR']; ?>"><img src="gfx/images/eliminar_circulo_16.png" alt="" /> <?php print $sabor_cafe['FCNOMBRE'] ?></li>
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
