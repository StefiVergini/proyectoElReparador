<?php
    include("../conexionPDO.php");
    include('../fpdf/fpdf.php');

    $consulta = "SELECT * FROM proveedores";
    $resultado = $base->query($consulta);

    $pdf = new FPDF();
    $pdf->AddPage('landscape');
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(10, 10,'Cod', 1, 0, 'L', 0);
    $pdf->Cell(30, 10,'CUIT', 1, 0, 'L', 0);
    $pdf->Cell(60, 10,'Nombre', 1, 0, 'L', 0);
    $pdf->Cell(30, 10, utf8_decode('Teléfono'), 1, 0, 'L', 0);
    $pdf->Cell(60, 10, utf8_decode('Dirección'), 1, 0, 'L', 0);
    $pdf->Cell(70, 10,'Email', 1, 0, 'L', 0);
    $pdf->Cell(15, 10,'Saldo', 1, 0, 'L', 0);
    $pdf->Cell(16, 10,'Estado', 1, 1, 'L', 0);

    while($mostrar = $resultado ->fetch(PDO::FETCH_ASSOC)) {
        $pdf->Cell(10, 10, $mostrar['idproveedores'], 1, 0, 'L', 0);
        $pdf->Cell(30, 10, $mostrar['cuit'], 1, 0, 'L', 0);
        $pdf->Cell(60, 10, $mostrar['nombre_prov'], 1, 0, 'L', 0);
        $pdf->Cell(30, 10, $mostrar['tel_prov'], 1, 0, 'L', 0);
        $pdf->Cell(60, 10, $mostrar['dir_prov'], 1, 0, 'L', 0);
        $pdf->Cell(70, 10, $mostrar['email_prov'], 1, 0, 'L', 0);
        $pdf->Cell(15, 10, $mostrar['saldo'], 1, 0, 'L', 0);
        $pdf->Cell(16, 10, $mostrar['estado_prov'], 1, 1, 'L', 0);
    }
    $pdf->Output();

?>