<?php
session_start();

function checkAdmin() {
    if (!isset($_SESSION['admin'])) {
        header("Location: /admin/login.php");
        exit;
    }
}
