<div class="menu">
    <ul class="sf-js-enabled">
        <?php if (in_array($_SESSION['NIVEL_USUARIO'], array(1, 3))): ?>
            <li><a href="dashboard.php">HOME</a></li>
            <li class="break"></li>
            <li>
                <a href="listado_cafes.php">Caf&eacute;s</a>
                <ul>
                    <li><a href="form_add_cafe.php">A&ntilde;adir caf&eacute;</a></li>
                    <li class="break"></li>
                    <li><a href="listado_cafes.php">Listado de caf&eacute;s</a></li>
                </ul>
            </li>
            <li class="break"></li>
            <li>
                <a href="listado_comidas.php">Alimentos</a>
                <ul>
                    <li><a href="form_add_comida.php">A&ntilde;adir alimento</a></li>
                    <li class="break"></li>
                    <li><a href="listado_comidas.php">Listado de alimentos</a></li>
                </ul>
            </li>
            <li class="break"></li>
            <li>
                <a href="listado_bebidas.php">Bebidas</a>
                <ul>
                    <li><a href="form_add_bebida.php">A&ntilde;adir bebida</a></li>
                    <li class="break"></li>
                    <li><a href="listado_bebidas.php">Listado de bebidas</a></li>
                </ul>
            </li>
            <li class="break"></li>
            <li>
                <a href="listado_tiendas.php">Tiendas</a>
                <ul>
                    <li><a href="form_add_tienda.php">A&ntilde;adir tienda</a></li>
                    <li class="break"></li>
                    <li><a href="listado_tiendas.php">Listado de tiendas</a></li>
                </ul>
            </li>
            <li class="break"></li>
        <?php endif; ?>
        <?php if (in_array($_SESSION['NIVEL_USUARIO'], array(1, 2))): ?>
                <li>
                    <a href="#">Administraci&oacute;n</a>
                    <ul>
                        <li>
                            <a href="listado_usuarios.php">Usuarios</a>
                            <ul>
                                <li><a href="form_add_usuario.php">A&ntilde;adir usuario</a></li>
                                <li class="break"></li>
                                <li><a href="listado_usuarios.php">Listado de usuarios</a></li>
                            </ul>
                        </li>
                        <li class="break"></li>        
                        <li><a href="administracion_combos.php">Combos</a></li>
                    </ul>
                </li>
                <li class="break"></li>
        <?php endif; ?>
    </ul>
</div>