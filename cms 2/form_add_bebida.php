<?php
require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/Operaciones.php';
require_once 'privado/OperacionesBebidas.php';
require_once 'privado/conecta_db.php';
Operaciones::comprobar_derechos('form_add_bebida');
require_once 'includes/cabecera.php';

$bd_link = conecta_db();
$listado_disponibilidad = Operaciones::listado_disponibilidad($bd_link);
$listado_tipos_bebida = OperacionesBebidas::listado_tipos($bd_link);
$listado_categorias_ingredientes = OperacionesBebidas::listado_categorias_ingredientes($bd_link);
$listado_estados = Operaciones::listado_estados($bd_link);
?>


<script type="text/javascript">

    $(document).ready(function() {
        var imagen_principal = "";

        var registrar_evento_eliminar_ingrediente = function(){
            $("#listado_ingredientes > li > img").click(function() {
                $(this).parent().fadeOut(200,function(){
                    $(this).remove();
                });
            });

            $("#listado_ingredientes > li > img").hover(function() {
                $(this).css('cursor','pointer');
            }, function() {
                $(this).css('cursor','auto');
            });
        }

        $("#tipo").change(function(){
            var texto_tipo = $("#tipo option:selected").html();
            var id_tipo = $("#tipo option:selected").val();

            if (!$.isNumeric(id_tipo)){
                $('#categoria > option').remove();
                $('#subcategoria > option').remove();
                return false;
            }

            $.ajax({
                type: "POST",
                url: "bebidas.php",
                data: {
                    id_tipo: id_tipo,
                    accion: 'listado_categorias'
                }
            }).done(function(resultado) {
                resultado = jQuery.parseJSON(resultado);
                if (resultado.success == true){
                    $('#categoria > option').remove();
                    $('#subcategoria > option').remove();
                    $('#subcategoria').append('<option value=""></option>');

                    if (resultado.categorias.length > 0){
                        $('#categoria').append('<option value="">--seleccione--</option>');
                    }else{
                        $('#categoria').append('<option value=""></option>');
                    }

                    $.each(resultado.categorias, function(key, value) {
                        $('#categoria').append('<option value="'+value.id+'">' +value.nombre + '</option>');
                    });
                }else{
                    alert('No ha sido posible obtener el listado de categorias');
                }
            });
        });

        $("#categoria").change(function(){
            var texto_categoria = $("#categoria option:selected").html();
            var id_categoria = $("#categoria option:selected").val();

            if (!$.isNumeric(id_categoria)){
                $('#subcategoria > option').remove();
                return false;
            }

            $.ajax({
                type: "POST",
                url: "bebidas.php",
                data: {
                    id_categoria: id_categoria,
                    accion: 'listado_subcategorias'
                }
            }).done(function(resultado) {
                resultado = jQuery.parseJSON(resultado);
                if (resultado.success == true){
                    $('#subcategoria > option').remove();

                    if (resultado.subcategorias.length > 0){
                        $('#subcategoria').append('<option value="seleccione">--seleccione--</option>');
                    }else{
                        $('#subcategoria').append('<option value=""></option>');
                    }

                    $.each(resultado.subcategorias, function(key, value) {
                        $('#subcategoria').append('<option value="'+value.id+'">' +value.nombre + '</option>');
                    });
                }else{
                    alert('No ha sido posible obtener el listado de subcategorias');
                }
            });
        });

        $("#categoria_ingredientes").change(function(){
            var texto_categoria = $("#categoria_ingredientes option:selected").html();
            var id_categoria = $("#categoria_ingredientes option:selected").val();

            if (!$.isNumeric(id_categoria)){
                $('#ingredientes > option').remove();
                return false;
            }

            $.ajax({
                type: "POST",
                url: "bebidas.php",
                data: {
                    id_categoria: id_categoria,
                    accion: 'listado_ingredientes'
                }
            }).done(function(resultado) {
                resultado = jQuery.parseJSON(resultado);
                if (resultado.success == true){
                    $('#ingredientes > option').remove();

                    if (resultado.ingredientes.length > 0){
                        $('#ingredientes').append('<option value="">--seleccione ingrediente--</option>');
                    }else{
                        $('#ingredientes').append('<option value=""></option>');
                    }

                    $.each(resultado.ingredientes, function(key, value) {
                        $('#ingredientes').append('<option value="'+value.id+'">' +value.nombre + '</option>');
                    });
                }else{
                    alert('No ha sido posible obtener el listado de ingredientes');
                }
            });
        });

        $("#ingredientes").change(function(){
            var texto_categoria = $("#categoria_ingredientes option:selected").html();
            var texto_ingrediente = $("#ingredientes option:selected").html();
            var id_ingrediente = $("#ingredientes option:selected").val();
            var existe = false;

            if (!$.isNumeric(id_ingrediente)){
                return false;
            }

            $('#listado_ingredientes > li').each(function(index) {
                if ($(this).attr('idingrediente') == id_ingrediente){
                    existe = true;
                    return;
                }
            });

            if (!existe){
                $("#listado_ingredientes").append('<li idingrediente="'+id_ingrediente+'"><img src="gfx/images/eliminar_circulo_16.png" alt="" /> '+texto_categoria+' / '+texto_ingrediente+'</li>');
                registrar_evento_eliminar_ingrediente();
            }

            $("#ingredientes").val("");
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
                    imagen_principal = data.result.fichero;
                    $('#contenedor-imagen-principal').html('<img src="'+data.result.fichero+'" alt="" style="height: 56px"/>');
                }else{
                    if (imagen_principal != ""){
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
            var tipo = jQuery.trim($('#tipo').val());
            var categoria = jQuery.trim($('#categoria').val());
            var subcategoria = jQuery.trim($('#subcategoria').val());
            var marcado_decaf = jQuery.trim($('#marcado_decaf').val());
            var marcado_shots = jQuery.trim($('#marcado_shots').val());
            var marcado_jarabe = jQuery.trim($('#marcado_jarabe').val());
            var marcado_leche = jQuery.trim($('#marcado_leche').val());
            var marcado_pers = jQuery.trim($('#marcado_pers').val());
            var marcado_bebida = jQuery.trim($('#marcado_bebida').val());
            var shot = jQuery.trim($('#shot').val());
            var estado = jQuery.trim($('#estado').val());

            var ingredientes = new Array();
            $('#listado_ingredientes > li').each(function(index) {
                ingredientes.push($(this).attr('idingrediente'));
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
            }else if (tipo == 'seleccione'){
                alert('Debe especificar el tipo de bebida.');
                return false;
            }else if (categoria.length == 0){
                alert('Debe seleccionar una categoría.');
                return false;
            }else if (subcategoria == 'seleccione'){
                alert('Debe seleccionar una subcategoría.');
                return false;
            }else if (imagen_principal.length == 0){
                alert('Debe especificar la imagen principal.');
                return false;
            }

            $('#loader_guardando').show();

            $.ajax({
                type: "POST",
                url: "bebidas.php",
                data: {
                    nombre: nombre,
                    descripcion: descripcion,
                    descripcion_corta: descripcion_corta,
                    disponibilidad: disponibilidad,
                    tipo: tipo,
                    categoria: categoria,
                    subcategoria: subcategoria,
                    shot: shot,
                    imagen_principal: imagen_principal,
                    ingredientes: ingredientes,
                    marcado_decaf:marcado_decaf,
                    marcado_shots:marcado_shots,
                    marcado_jarabe:marcado_jarabe,
                    marcado_leche:marcado_leche,
                    marcado_pers:marcado_pers,
                    marcado_bebida:marcado_bebida,
                    estado: estado,
                    accion: 'alta_bebida'
                }
            }).done(function(resultado) {
                $('#loader_guardando').hide();
                resultado = jQuery.parseJSON(resultado);
                if (resultado.success == true){
                    document.location.href = 'listado_bebidas.php';
                }else{
                    alert('No ha sido posible dar de alta la bebida');
                }
            });

            return false;
        });

        registrar_evento_eliminar_ingrediente();

        $('#estado').val(2);
    });

</script>

<style>
    #sortable { list-style-type: none; margin: 0; padding: 0; }
    #sortable li { margin: 3px 3px 3px 3px; padding: 1px 1px 1px 1px; height: 76px;float: left; text-align: center; border: none }
</style>

<div class="holder">

    <table style="border: 1px solid #D3D3D3;width: 98%;margin: 10px;padding: 20px">
        <tr>
            <td colspan="2" style="text-align: center;font-weight: bold;font-size: 1.3em;padding-bottom: 30px"><span class="titulo_formulario">Formulario de alta de bebida</span></td>
        </tr>
        <tr>
            <td class="etiqueta_campo">Nombre <span class="color_campo_obligatorio">*</span></td>
            <td><input id="nombre" name="nombre" type="text"></td>
        </tr>

        <tr>
            <td class="etiqueta_campo">Descripci&oacute;n <span class="color_campo_obligatorio">*</span></td>
            <td colspan="2"><textarea id="descripcion" name="descripcion" style="width:350px;height:50px"></textarea><span id="caracteres_descripcion"> 0</span> caracteres</td>
        </tr>

        <tr>
            <td class="etiqueta_campo">Descripci&oacute;n corta <span class="color_campo_obligatorio">*</span></td>
            <td colspan="2"><textarea id="descripcion_corta" name="descripcion_corta" style="width:300px;height:50px"></textarea><span id="caracteres_descripcion_corta"> 0</span> caracteres</td>
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
                <td class="etiqueta_campo">Tipo de bebida <span class="color_campo_obligatorio">*</span></td>
                <td>
                    <select id="tipo" name="tipo">
                        <option value="seleccione">--seleccione--</option>
                    <?php foreach ($listado_tipos_bebida as $tipo_bebida): ?>
                            <option value="<?php print $tipo_bebida['FIIDTEMPERATURA']; ?>"><?php print $tipo_bebida['FCNOMBRE']; ?></option>
                    <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td class="etiqueta_campo">Categor&iacute;a <span class="color_campo_obligatorio">*</span></td>
                    <td>
                        <select id="categoria" name="categoria">
                            <option value=""></option>
                        </select>

                        <select id="subcategoria" name="subcategoria">
                            <option value=""></option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td class="etiqueta_campo">Marcado del vaso</td>
                    <td>
                        <table id="campos_marcado_vaso" style="padding: 0px;margin: 0px" cellpadding="0" cellspacing="0">
                            <thead>
                                <tr>
                                    <td>Decaf</td>
                                    <td>Shots</td>
                                    <td>Jarabe</td>
                                    <td>Leche</td>
                                    <td>Personalizado</td>
                                    <td>Bebida</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" id="marcado_decaf" style="width:40px"/></td>
                                    <td><input type="text" id="marcado_shots" style="width:40px"/></td>
                                    <td><input type="text" id="marcado_jarabe" style="width:40px"/></td>
                                    <td><input type="text" id="marcado_leche" style="width:40px"/></td>
                                    <td><input type="text" id="marcado_pers" style="width:40px"/></td>
                                    <td><input type="text" id="marcado_bebida" style="width:40px"/></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td class="etiqueta_campo">Shot</td>
                    <td>
                        <input type="text" id="shot" style="width:50px"/>
                    </td>
                </tr>

                <tr>
                    <td class="etiqueta_campo">Imagen principal <span class="color_campo_obligatorio">*</span></td>
                    <td>
                        <table style="padding: 0px;margin: 0px" cellpadding="0" cellspacing="0">
                            <tr>
                                <td><input id="input_imagen_principal" type="file" name="imagen" /></td>
                                <td id="contenedor-imagen-principal"> </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td class="etiqueta_campo">Ingredientes</td>
                    <td>
                        <select id="categoria_ingredientes" name="categoria_ingredientes">
                            <option value="">--seleccione categoría--</option>
                    <?php foreach ($listado_categorias_ingredientes as $categoria_ingredientes): ?>
                                <option value="<?php print $categoria_ingredientes['FIIDCATEGORIAINGREDIENTE']; ?>"><?php print $categoria_ingredientes['FCNOMBRE']; ?></option>
                    <?php endforeach; ?>
                            </select>

                            <select id="ingredientes" name="ingredientes">
                                <option value=""></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
                            <fieldset style="width: 200px">
                                <legend>Listado ingredientes</legend>
                                <ul id="listado_ingredientes" class="lista_sin_formato"></ul>
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
