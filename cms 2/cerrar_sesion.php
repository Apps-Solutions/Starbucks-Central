<?php

session_start();
if (isset($_SESSION['LOGADO'])) {
    session_destroy();
}

header('Location: index.php');
?>
