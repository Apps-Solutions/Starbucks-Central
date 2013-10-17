<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

    <head>

        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <title>Reseller</title>

        <style type="text/css">
            @import url("js/jquery/css/smoothness/jquery-ui-1.8.22.custom.css");
            @import url("css/app.css");
            @import url("css/style.css");
            @import url('css/style_text.css');
            @import url('css/c-grey.css'); /* COLOR FILE CAN CHANGE TO c-blue.ccs, c-grey.ccs, c-orange.ccs, c-purple.ccs or c-red.ccs */
            @import url('css/datepicker.css');
            @import url('css/form.css');
            @import url('css/menu.css');
            @import url('css/messages.css');
            @import url('css/statics.css');
            @import url('css/tabs.css');
            @import url('css/wysiwyg.css');
            @import url('css/wysiwyg.modal.css');
            @import url('css/wysiwyg-editor.css');
            @import url('js/plugins/datatables/jquery.dataTables.css');
            @import url('js/plugins/datatables/jquery.dataTables_themeroller.css');
        </style>

        <script type="text/javascript" src="js/jquery/js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="js/jquery/js/jquery-ui-1.8.22.custom.min.js"></script>
        <script type="text/javascript" src="js/plugins/jquery.watermark.min.js"></script>
        <script type="text/javascript" src="js/plugins/fileupload/jquery.iframe-transport.js"></script>
        <script type="text/javascript" src="js/plugins/fileupload/jquery.fileupload.js"></script>
        <script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>

        <!--[if lte IE 8]>
                <script type="text/javascript" src="js/excanvas.min.js"></script>
	<![endif]-->

    </head>

    <body>

        <div class="wrapper">
            <div class="container">

                <!--[if !IE]> START TOP <![endif]-->
                <div class="top">
                    <div class="split" style="text-align: left;margin-top: 5px">
                        <img src="gfx/images/logo_alsea.png" />
                    </div>
                    <div class="split">
                        <div class="logout"><img src="gfx/icon-logout.gif" align="left" alt="Logout" /> <a href="cerrar_sesion.php">Salir</a></div>
                        <div><img src="gfx/icon-welcome.gif" align="left" alt="Bienvenido" />Bienvenido <?php print $_SESSION['NOMBRE_USUARIO']; ?></div>
                    </div>
                </div>
                <!--[if !IE]> END TOP <![endif]-->  

                <?php require_once 'menu_principal.php'; ?>