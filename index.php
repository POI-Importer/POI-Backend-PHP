<?php
session_start();
if(!isset($_SESSION['secret'])) {
    header('Location: login.php');
    exit();
}
?>