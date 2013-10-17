<?php
require_once 'privado/comprobar_sesion.php';
require_once 'privado/config.php';
require_once 'privado/Operaciones.php';
require_once 'privado/OperacionesCombos.php';
require_once 'privado/OperacionesBebidas.php';
require_once 'privado/conecta_db.php';
Operaciones::comprobar_derechos('administracion_combos');
require_once 'includes/cabecera.php';

$combo_seleccionado = isset($_GET['combo']) ? $_GET['combo'] : '';
$id_tipo_bebida = isset($_GET['id_tipo_bebida']) ? $_GET['id_tipo_bebida'] : '';
$bd_link = conecta_db();
$listado_tipos_bebidas = OperacionesBebidas::listado_tipos($bd_link);
?>

<script type="text/javascript">

    function confirmacion(){
        if(!confirm("Por favor, confirme que desea eliminar la opción.")) {
            return false;
        }
    }

    function tabla_disponibilidad(datos_json){
        var contenido_html = '<table id="tabla_listado">';

        contenido_html += '<thead><tr>';
        contenido_html += '<th>Nombre</th>';
        contenido_html += '<th></th>';
        contenido_html += '</tr></thead>';

        contenido_html += '<tbody>';
        for (var index = 0; index < datos_json.length; index++){
            contenido_html += '<tr>';
            contenido_html += '<td>'+datos_json[index].FCNOMBRE+'</td>';
            contenido_html += '<td style="width:50px;text-align: center">';
            contenido_html += '   <a href="form_editar_opcion_combo_simple.php?tipo=disponibilidad_todo&id='+datos_json[index].FIIDDISPONIBILIDAD+'&nombre='+datos_json[index].FCNOMBRE+'&titulo=Formulario de edición de disponibilidad"><img src="gfx/images/editar_16.png" alt="Editar opci&oacute;n" /></a> &nbsp;';
            contenido_html += '   <a href="combos.php?accion=eliminar_opcion&tipo=disponibilidad_todo&id='+datos_json[index].FIIDDISPONIBILIDAD+'"><img src="gfx/images/eliminar_16_2.png" alt="Eliminar opci&oacute;n" onclick="return confirmacion(this);"/></a>';
            contenido_html += '</td>';
            contenido_html += '</tr>';
        }
        contenido_html += '</tbody>';
        
        contenido_html += '</table>';

        $('#contenedor_tabla_listado').css('width','560px').html(contenido_html).each(function(){
            aplicar_estilo_tabla();
        });
    }

    function tabla_perfiles_cafe(datos_json){
        var contenido_html = '<table id="tabla_listado">';

        contenido_html += '<thead><tr>';
        contenido_html += '<th>Nombre</th>';
        contenido_html += '<th></th>';
        contenido_html += '</tr></thead>';

        contenido_html += '<tbody>';
        for (var index = 0; index < datos_json.length; index++){
            contenido_html += '<tr>';
            contenido_html += '<td>'+datos_json[index].FCNOMBRE+'</td>';
            contenido_html += '<td style="width:50px;text-align: center">';
            contenido_html += '   <a href="form_editar_opcion_combo_simple.php?tipo=perfiles_cafe&id='+datos_json[index].FIIDPERFIL+'&nombre='+datos_json[index].FCNOMBRE+'&titulo=Formulario de edición de perfil"><img src="gfx/images/editar_16.png" alt="Editar opci&oacute;n" /></a> &nbsp;';
            contenido_html += '   <a href="combos.php?accion=eliminar_opcion&tipo=perfiles_cafe&id='+datos_json[index].FIIDPERFIL+'"><img src="gfx/images/eliminar_16_2.png" alt="Eliminar opci&oacute;n" onclick="return confirmacion(this);"/></a>';
            contenido_html += '</td>';
            contenido_html += '</tr>';
        }
        contenido_html += '</tbody>';

        contenido_html += '</table>';

        $('#contenedor_tabla_listado').css('width','560px').html(contenido_html).each(function(){
            aplicar_estilo_tabla();
        });
    }

    function tabla_formas_cafe(datos_json){
        var contenido_html = '<table id="tabla_listado">';

        contenido_html += '<thead><tr>';
        contenido_html += '<th>Nombre</th>';
        contenido_html += '<th></th>';
        contenido_html += '</tr></thead>';

        contenido_html += '<tbody>';
        for (var index = 0; index < datos_json.length; index++){
            contenido_html += '<tr>';
            contenido_html += '<td>'+datos_json[index].FCNOMBRE+'</td>';
            contenido_html += '<td style="width:50px;text-align: center">';
            contenido_html += '   <a href="form_editar_opcion_combo_simple.php?tipo=formas_cafe&id='+datos_json[index].FIIDFORMA+'&nombre='+datos_json[index].FCNOMBRE+'&titulo=Formulario de edición de forma"><img src="gfx/images/editar_16.png" alt="Editar opci&oacute;n" /></a> &nbsp;';
            contenido_html += '   <a href="combos.php?accion=eliminar_opcion&tipo=formas_cafe&id='+datos_json[index].FIIDFORMA+'"><img src="gfx/images/eliminar_16_2.png" alt="Eliminar opci&oacute;n" onclick="return confirmacion(this);"/></a>';
            contenido_html += '</td>';
            contenido_html += '</tr>';
        }
        contenido_html += '</tbody>';

        contenido_html += '</table>';

        $('#contenedor_tabla_listado').css('width','560px').html(contenido_html).each(function(){
            aplicar_estilo_tabla();
        });
    }

    function tabla_sabores_cafe(datos_json){
        var contenido_html = '<table id="tabla_listado">';

        contenido_html += '<thead><tr>';
        contenido_html += '<th>Nombre</th>';
        contenido_html += '<th></th>';
        contenido_html += '</tr></thead>';

        contenido_html += '<tbody>';
        for (var index = 0; index < datos_json.length; index++){
            contenido_html += '<tr>';
            contenido_html += '<td>'+datos_json[index].FCNOMBRE+'</td>';
            contenido_html += '<td style="width:50px;text-align: center">';
            contenido_html += '   <a href="form_editar_opcion_combo_simple.php?tipo=sabores_cafe&id='+datos_json[index].FIIDSABOR+'&nombre='+datos_json[index].FCNOMBRE+'&titulo=Formulario de edición de sabor"><img src="gfx/images/editar_16.png" alt="Editar opci&oacute;n" /></a> &nbsp;';
            contenido_html += '   <a href="combos.php?accion=eliminar_opcion&tipo=sabores_cafe&id='+datos_json[index].FIIDSABOR+'"><img src="gfx/images/eliminar_16_2.png" alt="Eliminar opci&oacute;n" onclick="return confirmacion(this);"/></a>';
            contenido_html += '</td>';
            contenido_html += '</tr>';
        }
        contenido_html += '</tbody>';

        contenido_html += '</table>';

        $('#contenedor_tabla_listado').css('width','560px').html(contenido_html).each(function(){
            aplicar_estilo_tabla();
        });
    }

    function tabla_alergenos_comida(datos_json){
        var contenido_html = '<table id="tabla_listado">';

        contenido_html += '<thead><tr>';
        contenido_html += '<th>Nombre</th>';
        contenido_html += '<th></th>';
        contenido_html += '</tr></thead>';

        contenido_html += '<tbody>';
        for (var index = 0; index < datos_json.length; index++){
            contenido_html += '<tr>';
            contenido_html += '<td>'+datos_json[index].FCNOMBRE+'</td>';
            contenido_html += '<td style="width:50px;text-align: center">';
            contenido_html += '   <a href="form_editar_opcion_combo_simple.php?tipo=alergenos_comida&id='+datos_json[index].FIIDALERGENO+'&nombre='+datos_json[index].FCNOMBRE+'&titulo=Formulario de edición de alérgeno"><img src="gfx/images/editar_16.png" alt="Editar opci&oacute;n" /></a> &nbsp;';
            contenido_html += '   <a href="combos.php?accion=eliminar_opcion&tipo=alergenos_comida&id='+datos_json[index].FIIDALERGENO+'"><img src="gfx/images/eliminar_16_2.png" alt="Eliminar opci&oacute;n" onclick="return confirmacion(this);"/></a>';
            contenido_html += '</td>';
            contenido_html += '</tr>';
        }
        contenido_html += '</tbody>';

        contenido_html += '</table>';

        $('#contenedor_tabla_listado').css('width','560px').html(contenido_html).each(function(){
            aplicar_estilo_tabla();
        });
    }

    function tabla_categorias_comida(datos_json){
        var contenido_html = '<table id="tabla_listado">';

        contenido_html += '<thead><tr>';
        contenido_html += '<th>Nombre</th>';
        contenido_html += '<th></th>';
        contenido_html += '</tr></thead>';

        contenido_html += '<tbody>';
        for (var index = 0; index < datos_json.length; index++){
            contenido_html += '<tr>';
            contenido_html += '<td>'+datos_json[index].FCNOMBRE+'</td>';
            contenido_html += '<td style="width:50px;text-align: center">';
            contenido_html += '   <a href="form_editar_opcion_combo_simple.php?tipo=categorias_comida&id='+datos_json[index].FIIDCATEGORIACOMIDA+'&nombre='+datos_json[index].FCNOMBRE+'&titulo=Formulario de edición de categoría de comida"><img src="gfx/images/editar_16.png" alt="Editar opci&oacute;n" /></a> &nbsp;';
            contenido_html += '   <a href="combos.php?accion=eliminar_opcion&tipo=categorias_comida&id='+datos_json[index].FIIDCATEGORIACOMIDA+'"><img src="gfx/images/eliminar_16_2.png" alt="Eliminar opci&oacute;n" onclick="return confirmacion(this);"/></a>';
            contenido_html += '</td>';
            contenido_html += '</tr>';
        }
        contenido_html += '</tbody>';

        contenido_html += '</table>';

        $('#contenedor_tabla_listado').css('width','560px').html(contenido_html).each(function(){
            aplicar_estilo_tabla();
        });
    }

    function tabla_servicios_tienda(datos_json){
        var contenido_html = '<table id="tabla_listado">';

        contenido_html += '<thead><tr>';
        contenido_html += '<th>Nombre</th>';
        contenido_html += '<th></th>';
        contenido_html += '</tr></thead>';

        contenido_html += '<tbody>';
        for (var index = 0; index < datos_json.length; index++){
            contenido_html += '<tr>';
            contenido_html += '<td>'+datos_json[index].FCNOMBRE+'</td>';
            contenido_html += '<td style="width:50px;text-align: center">';
            contenido_html += '   <a href="form_editar_opcion_combo_simple.php?tipo=servicios_tienda&id='+datos_json[index].FIIDSERVICIO+'&nombre='+datos_json[index].FCNOMBRE+'&titulo=Formulario de edición de servicio de tienda"><img src="gfx/images/editar_16.png" alt="Editar opci&oacute;n" /></a> &nbsp;';
            contenido_html += '   <a href="combos.php?accion=eliminar_opcion&tipo=servicios_tienda&id='+datos_json[index].FIIDSERVICIO+'"><img src="gfx/images/eliminar_16_2.png" alt="Eliminar opci&oacute;n" onclick="return confirmacion(this);"/></a>';
            contenido_html += '</td>';
            contenido_html += '</tr>';
        }
        contenido_html += '</tbody>';

        contenido_html += '</table>';

        $('#contenedor_tabla_listado').css('width','560px').html(contenido_html).each(function(){
            aplicar_estilo_tabla();
        });
    }

    function tabla_categorias_tipo_bebida(datos_json, tipo_bebida){
        var contenido_html = '<table id="tabla_listado">';

        contenido_html += '<thead><tr>';
        contenido_html += '<th>Categor&iacute;a</th>';
        contenido_html += '<th></th>';
        contenido_html += '</tr></thead>';

        contenido_html += '<tbody>';
        for (var index = 0; index < datos_json.length; index++){
            contenido_html += '<tr>';
            contenido_html += '<td>'+datos_json[index].FCNOMBRE+'</td>';
            contenido_html += '<td style="width:50px;text-align: center">';
            contenido_html += '   <a href="form_editar_categorias_tipos_bebidas.php?id='+datos_json[index].FIIDCATEGORIABEBIDA+'&titulo=Formulario de edición de categoría"><img src="gfx/images/editar_16.png" alt="Editar opci&oacute;n" /></a> &nbsp;';
            contenido_html += '   <a href="combos.php?accion=eliminar_categoria_tipos_bebidas&tipo_bebida='+tipo_bebida+'&id='+datos_json[index].FIIDCATEGORIABEBIDA+'"><img src="gfx/images/eliminar_16_2.png" alt="Eliminar opci&oacute;n" onclick="return confirmacion(this);"/></a>';
            contenido_html += '</td>';
            contenido_html += '</tr>';
        }
        contenido_html += '</tbody>';

        contenido_html += '</table>';

        $('#contenedor_tabla_listado').css('width','560px').html(contenido_html).each(function(){
            aplicar_estilo_tabla();
        });
    }

    function aplicar_estilo_tabla(){

        $('#tabla_listado').dataTable({
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "oLanguage": {
                "sProcessing":   "Procesando...",
                "sLengthMenu":   "Mostrar _MENU_ registros",
                "sZeroRecords":  "No se encontraron resultados",
                "sInfo":         "Mostrando desde _START_ hasta _END_ de _TOTAL_ registros",
                "sInfoEmpty":    "Mostrando desde 0 hasta 0 de 0 registros",
                "sInfoFiltered": "(filtrado de _MAX_ registros en total)",
                "sInfoPostFix":  "",
                "sSearch":       "Buscar:",
                "sUrl":          "",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sPrevious": "Anterior",
                    "sNext":     "Siguiente",
                    "sLast":     "Último"
                }
            }
        });
    }

    function obtener_datos(tipo, id){
        if (tipo.length == 0){
            return false;
        }

        $('#loader_guardando').show();

        $.ajax({
            type: "POST",
            url: "combos.php",
            data: {
                id: id,
                tipo: tipo,
                accion: 'datos_combo'
            }
        }).done(function(resultado) {
            $('#loader_guardando').hide();
            resultado = jQuery.parseJSON(resultado);
            if (resultado.success == true){
                if (tipo == 'disponibilidad_todo'){
                    tabla_disponibilidad(resultado.datos);
                }else if (tipo == 'perfiles_cafe'){
                    tabla_perfiles_cafe(resultado.datos);
                }else if (tipo == 'formas_cafe'){
                    tabla_formas_cafe(resultado.datos);
                }else if (tipo == 'sabores_cafe'){
                    tabla_sabores_cafe(resultado.datos);
                }else if (tipo == 'alergenos_comida'){
                    tabla_alergenos_comida(resultado.datos);
                }else if (tipo == 'categorias_comida'){
                    tabla_categorias_comida(resultado.datos);
                }else if (tipo == 'servicios_tienda'){
                    tabla_servicios_tienda(resultado.datos);
                }else if (tipo == 'categorias_tipo_bebida'){
                    tabla_categorias_tipo_bebida(resultado.datos, id);
                }
            }else{
                alert('No ha sido posible obtener el listado de opciones');
            }
        });
    }

    $(document).ready(function() {

        $("#combos").change(function(){
            var id_combo = $("#combos option:selected").val();

            if (id_combo.length == 0){
                $('#contenedor_tabla_listado').html('');
                $('#contenedor_tipo_bebida').hide();
                return false;
            }

            if (id_combo != 'tipos_bebida'){
                $('#contenedor_tipo_bebida').hide();
            }else if (id_combo == 'tipos_bebida'){
                $('#tipo_bebida').val('');
                $('#contenedor_tabla_listado').html('');
                $('#contenedor_tipo_bebida').show();
            }

            if (id_combo == 'disponibilidad_todo'){
                obtener_datos('disponibilidad_todo');
            }else if (id_combo == 'perfiles_cafe'){
                obtener_datos('perfiles_cafe');
            }else if (id_combo == 'formas_cafe'){
                obtener_datos('formas_cafe');
            }else if (id_combo == 'sabores_cafe'){
                obtener_datos('sabores_cafe');
            }else if (id_combo == 'alergenos_comida'){
                obtener_datos('alergenos_comida');
            }else if (id_combo == 'categorias_comida'){
                obtener_datos('categorias_comida');
            }else if (id_combo == 'servicios_tienda'){
                obtener_datos('servicios_tienda');
            }
        });

        $("#tipo_bebida").change(function(){
            var id_tipo = $("#tipo_bebida option:selected").val();

            if (id_tipo.length == 0){
                $('#contenedor_tabla_listado').html('');
                return false;
            }

            obtener_datos('categorias_tipo_bebida',id_tipo);
        });

        $("#boton_add").hover(function() {
            $(this).css('cursor','pointer');
        }, function() {
            $(this).css('cursor','auto');
        });

        $("#boton_add").click(function() {
            var id_combo = $("#combos option:selected").val();
            var id_tipo = $("#tipo_bebida option:selected").val();

            if (id_combo == ""){
                alert('Para añadir una nueva opción primero debe seleccionar un combo de la lista.');
                return false;
            }

            if (id_combo == "tipos_bebida" && id_tipo == ""){
                alert('Para añadir una categoría primero debe seleccionar el tipo de bebida.');
                return false;
            }

            if (id_combo == 'disponibilidad_todo' || id_combo == 'perfiles_cafe' || id_combo == 'formas_cafe' || id_combo == 'sabores_cafe' || id_combo == 'alergenos_comida' || id_combo == 'categorias_comida' || id_combo == 'servicios_tienda'){
                var titulo = "";

                if (id_combo == 'disponibilidad_todo'){
                    titulo = 'Formulario de alta de disponibilidad';
                }else if (id_combo == 'perfiles_cafe'){
                    titulo = 'Formulario de alta de perfil';
                }else if (id_combo == 'formas_cafe'){
                    titulo = 'Formulario de alta de forma';
                }else if (id_combo == 'sabores_cafe'){
                    titulo = 'Formulario de alta de sabor';
                }else if (id_combo == 'alergenos_comida'){
                    titulo = 'Formulario de alta de alérgeno';
                }else if (id_combo == 'categorias_comida'){
                    titulo = 'Formulario de alta de categoría de comida';
                }else if (id_combo == 'servicios_tienda'){
                    titulo = 'Formulario de alta de servicio de tienda';
                }

                document.location.href = "form_add_opcion_combo_simple.php?tipo="+id_combo+"&titulo="+titulo;
            }

            if (id_combo == "tipos_bebida"){
                document.location.href = "form_add_categorias_tipos_bebidas.php?tipo_bebida="+id_tipo+"&titulo=Formulario de alta de categoría";
            }

            return false;
        });

        $('#combos').val('<?php print $combo_seleccionado; ?>');

<?php if (is_numeric($id_tipo_bebida)): ?>
                    $('#tipo_bebida').val(<?php print $id_tipo_bebida; ?>);
                    $('#contenedor_tipo_bebida').show();
                    obtener_datos('categorias_tipo_bebida',<?php print $id_tipo_bebida; ?>);
<?php else: ?>
                        obtener_datos('<?php print $combo_seleccionado; ?>');
<?php endif; ?>
                    });

        </script>

        <div class="holder">

            <table cellspacing="0" border="0" style="margin-left: auto;margin-right: auto; margin-top: 30px">
                <tr>
                    <td class="etiqueta_campo">Combos: </td>
                    <td>
                        <select id="combos" name="combos">
                            <option value="">--seleccione el combo a modificar--</option>
                            <optgroup label="Secciones Caf&eacute;s, Alimentos, Bebidas">
                                <option value="disponibilidad_todo">Disponibilidad</option>
                            </optgroup>
                            <optgroup label="Secci&oacute;n Caf&eacute;s">
                                <option value="perfiles_cafe">Perfiles</option>
                                <option value="formas_cafe">Formas</option>
                                <option value="sabores_cafe">Sabores</option>
                            </optgroup>
                            <optgroup label="Secci&oacute;n Alimentos">
                                <option value="alergenos_comida">Al&eacute;rgenos</option>
                                <option value="categorias_comida">Categor&iacute;as</option>
                            </optgroup>
                            <optgroup label="Secci&oacute;n Bebidas">
                                <option value="tipos_bebida">Tipos de bebida y categor&iacute;as</option>
                                <option value="ingredientes_bebida">Ingredientes</option>
                            </optgroup>
                            <optgroup label="Secci&oacute;n Tiendas">
                                <option value="servicios_tienda">Servicios</option>
                            </optgroup>
                        </select>
                        &nbsp;
                        <img id="boton_add" src="gfx/images/add.png" />
                        &nbsp;&nbsp;
                        <img id="loader_guardando" src="gfx/images/ajax-loader.gif" style="display: none" />
                    </td>
                </tr>
                <tr id="contenedor_tipo_bebida" style="display: none">
                    <td class="etiqueta_campo">Tipo bebida</td>
                    <td>
                        <select id="tipo_bebida" name="tipo_bebida">
                            <option value="">--seleccione el tipo de bebida--</option>
                    <?php foreach ($listado_tipos_bebidas as $tipo_bebida): ?>
                        <option value="<?php print $tipo_bebida['FIIDTEMPERATURA']; ?>"><?php print $tipo_bebida['FCNOMBRE']; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

        <div id="contenedor_tabla_listado" cellpadding="0" cellspacing="0" border="0" class="display" style="width:800px;margin-left: auto;margin-right: auto; margin-top: 10px"></div>
    </div>

<?php require_once 'includes/pie.php'; ?>
