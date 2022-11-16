<?php
require_once './classes/connection.php';
require_once './classes/user.php';
session_start();
unset($_SESSION['user']);
unset($_SESSION['id']);
header('location:./login.php');