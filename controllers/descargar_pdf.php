<?php
session_start();
include("../includes/conexion.php");
require_once '../dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if(!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de reporte no especificado.");
}
$id = (int)$_GET['id'];

?>