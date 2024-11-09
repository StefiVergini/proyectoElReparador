<?php
    include("../conexionPDO.php");
    include('../fpdf/fpdf.php');

    $consulta = "SELECT * FROM clientes";
    $resultado = $base->query($consulta);

    $pdf = new FPDF();
    $pdf->AddPage('landscape');
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(10, 10,'Cod', 1, 0, 'L', 0);
    $pdf->Cell(25, 10,'DNI', 1, 0, 'L', 0);
    $pdf->Cell(30, 10,'Nombre', 1, 0, 'L', 0);
    $pdf->Cell(30, 10,'Apellido', 1, 0, 'L', 0);
    $pdf->Cell(30, 10, utf8_decode('Teléfono'), 1, 0, 'L', 0);
    $pdf->Cell(60, 10, utf8_decode('Dirección'), 1, 0, 'L', 0);
    $pdf->Cell(60, 10,'Email', 1, 0, 'L', 0);
    $pdf->Cell(16, 10,'Estado', 1, 1, 'L', 0);

    while($mostrar = $resultado ->fetch(PDO::FETCH_ASSOC)) {
        $pdf->Cell(10, 10, $mostrar['idclientes'], 1, 0, 'L', 0);
        $pdf->Cell(25, 10, $mostrar['dni_cliente'], 1, 0, 'L', 0);
        $pdf->Cell(30, 10, $mostrar['nom_cliente'], 1, 0, 'L', 0);
        $pdf->Cell(30, 10, $mostrar['ape_cliente'], 1, 0, 'L', 0);
        $pdf->Cell(30, 10, $mostrar['tel_cliente'], 1, 0, 'L', 0);
        $pdf->Cell(60, 10, $mostrar['dir_cliente'], 1, 0, 'L', 0);
        $pdf->Cell(60, 10, $mostrar['email_cliente'], 1, 0, 'L', 0);
        $pdf->Cell(16, 10, $mostrar['estado_cliente'], 1, 1, 'L', 0);
    }
    $pdf->Output();

?>

