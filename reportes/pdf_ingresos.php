<?php
include("../conexionPDO.php");
include('../fpdf/fpdf.php');

if (empty($_GET['rango'])) {
    die("Debe seleccionar un rango de fechas para generar el reporte.");
}

// Parámetros
$rango = $_GET['rango'];
$medio_pago = $_GET['medio_pago'] ?? '';
$tipo_ingreso = $_GET['tipo_ingreso'] ?? '';
$tipo_electro = $_GET['tipo'] ?? '';

// Parsear fechas
$fechas = explode(" to ", $rango);
if (count($fechas) != 2) {
    die("Rango de fechas no válido.");
}
$inicio = trim($fechas[0]);
$fin = trim($fechas[1]);

class PDF extends FPDF {
    public $filtro_fecha = '';

    function Header() {
        $this->Image('../static/images/logo.png',10,6,30);
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'Reporte de Ingresos',0,1,'C');
        $this->SetFont('Arial','',11);
        $this->Cell(0,10,"Rango de fechas: {$this->filtro_fecha}",0,1,'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo(),0,0,'C');
    }

    function TablaHeader() {
        $this->SetFont('Arial','B',9);
        $this->SetFillColor(200,200,200);
        $this->Cell(10,7,'ID',1,0,'C',true);
        $this->Cell(40,7,'Cliente',1,0,'C',true);
        $this->Cell(30,7,'Electrodomestico',1,0,'C',true);
        $this->Cell(30,7,'Cobro Inicial',1,0,'C',true);
        $this->Cell(25,7,'Pago Inicial',1,0,'C',true);
        $this->Cell(30,7,'Cobro Final',1,0,'C',true);
        $this->Cell(25,7,'Pago Final',1,0,'C',true);
        $this->Cell(25,7,'Monto Fijo',1,0,'C',true);
        $this->Cell(25,7,'Saldo',1,1,'C',true);
    }

    function TablaContenido($datos, &$total_fijo, &$total_saldo) {
        $this->SetFont('Arial','',8);
        foreach ($datos as $fila) {
            $this->Cell(10,6,$fila['id_cobro'],1);
            $this->Cell(40,6,ucwords($fila['nom_cliente'] . ' ' . $fila['ape_cliente']),1);
            $this->Cell(30,6,ucwords($fila['tipo_electro_nombre']),1);
            $this->Cell(30,6,$fila['fecha_cobro_inicial'],1);
            $this->Cell(25,6,ucwords($fila['medio_pago_inicial']),1);
            $this->Cell(30,6,$fila['fecha_cobro_final'],1);
            $this->Cell(25,6,ucwords($fila['medio_pago_final']),1);
            $this->Cell(25,6,number_format($fila['arancel_fijo_cobrado'], 2, ',', '.'),1,0,'R');
            $this->Cell(25,6,number_format($fila['monto_final_repa'], 2, ',', '.'),1,1,'R');

            $total_fijo += floatval($fila['arancel_fijo_cobrado']);
            $total_saldo += floatval($fila['monto_final_repa']);
        }
    }
}

// Consulta a la base de datos
$query = "
    SELECT cobros.*, clientes.nom_cliente, clientes.ape_cliente, tipo_electro.nom_tipo AS tipo_electro_nombre
    FROM cobros
    JOIN reparaciones ON cobros.id_reparacion = reparaciones.id_reparacion
    JOIN electrodomesticos ON reparaciones.idelectrodomesticos = electrodomesticos.idelectrodomesticos
    JOIN clientes ON electrodomesticos.idclientes = clientes.idclientes
    JOIN tipo_electro ON electrodomesticos.tipo_electro = tipo_electro.idtipo_electro
    WHERE (
        (cobros.fecha_cobro_inicial BETWEEN :inicio AND :fin)
        OR 
        (cobros.fecha_cobro_final BETWEEN :inicio AND :fin)
    )
";

if (!empty($medio_pago)) {
    $query .= " AND (cobros.medio_pago_inicial = :medio_pago OR cobros.medio_pago_final = :medio_pago)";
}
if (!empty($tipo_ingreso)) {
    if ($tipo_ingreso === "fijo") {
        $query .= " AND cobros.arancel_fijo_cobrado > 0";
    } elseif ($tipo_ingreso === "saldo") {
        $query .= " AND cobros.monto_final_repa > 0";
    }
}
if (!empty($tipo_electro)) {
    $query .= " AND tipo_electro.idtipo_electro = :tipo_electro";
}

$stmt = $base->prepare($query);
$stmt->bindParam(':inicio', $inicio);
$stmt->bindParam(':fin', $fin);
if (!empty($medio_pago)) $stmt->bindParam(':medio_pago', $medio_pago);
if (!empty($tipo_electro)) $stmt->bindParam(':tipo_electro', $tipo_electro);
$stmt->execute();
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear PDF
$pdf = new PDF('L', 'mm', 'A4');
$pdf->filtro_fecha = "$inicio al $fin";
$pdf->AddPage();
$pdf->TablaHeader();

$total_fijo = 0;
$total_saldo = 0;

$pdf->TablaContenido($datos, $total_fijo, $total_saldo);

// Totales
$pdf->SetFont('Arial','B',9);
$pdf->Cell(190,7,'Totales:',1,0,'R');
$pdf->Cell(25,7,"$ " . number_format($total_fijo, 2, ',', '.'),1,0,'R');
$pdf->Cell(25,7,"$ " . number_format($total_saldo, 2, ',', '.'),1,1,'R');

$pdf->Output();
?>
