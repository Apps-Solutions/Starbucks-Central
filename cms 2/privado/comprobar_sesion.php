<?php
session_start();

if (!isset($_SESSION['LOGADO']) || $_SESSION['LOGADO'] != true) {
    header('Location: index.php');
}
?>
