<?php
include_once 'cabses.php';
session_destroy();
header("Location: ../../login.php");
die();