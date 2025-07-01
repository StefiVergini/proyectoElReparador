<?php
    include("../conexionPDO.php");
    include('../fpdf/fpdf.php');
    if (empty($_GET['rango'])) {
    die("Debe seleccionar un rango de fechas para generar el reporte.");
}


// Obtener filtros desde GET
$rango = $_GET['rango'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$tecnico = $_GET['tecnicos'] ?? '';

class PDF extends FPDF {
    public $filtro_fecha = ''; // Nueva propiedad

    // Método para calcular el número de líneas en una celda
    function NbLines($w, $txt) {
        $cw = $this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', (string) $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }

    function Header() {
        $this->Image('../static/images/logo.png',10,6,30);
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'Reporte de Reparaciones',0,1,'C');

        // Mostrar el rango de fechas
        $this->SetFont('Arial','',11);
        $this->Cell(0,10,'Rango de fechas: ' . $this->filtro_fecha,0,1,'C');

        $this->Ln(20); // Espacio antes de la tabla
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo(),0,0,'C');
    }

    function TablaHeader() {
        $this->SetFont('Arial','B',9);
        $this->SetFillColor(200,200,200);
        $this->Cell(12,7,'ID',1,0,'C',true);
        $this->Cell(28,7, 'Inicio reparacion',1,0,'C',true);
        $this->Cell(28,7,'F. Estimada fin',1,0,'C',true);
        $this->Cell(28,7,'Fin reparacion',1,0,'C',true);
        $this->Cell(28,7, 'Diferencia dias',1,0,'C',true);
        $this->Cell(25,7,'Estado',1,0,'C',true);
        $this->Cell(25,7,'Tipo',1,0,'C',true);
        $this->Cell(25,7,'Marca',1,0,'C',true);
        $this->Cell(30,7,'Tecnico',1,0,'C',true);
        $this->Cell(50,7,'Descripcion',1,0,'C',true);
        $this->Ln();
    }


    function TablaContenido($datos) {
    $this->SetFont('Arial','',8);
    foreach ($datos as $fila) {
        $fechaEstimada = $fila['fecha_fin_estimada'];
        $fechaFinal = $fila['fecha_finalizacion'];
        $diferenciaTexto = '—';

        if (!empty($fechaFinal)){
            if ($fechaEstimada && $fechaFinal) {
                $f1 = new DateTime($fechaEstimada);
                $f2 = new DateTime($fechaFinal);
                $dias = $f1->diff($f2)->days;
                $dias = intval($dias);

                if ($f2 > $f1) {
                    $diferenciaTexto = $dias . " dias de atraso";
                } elseif ($f2 < $f1) {
                    $diferenciaTexto = " " . $dias . " dias antes";
                } else {
                    $diferenciaTexto = "0 dias de diferencia";
                }
            }
        }else{
            $diferenciaTexto = 'Sigue en reparacion';
        }

        // Calcular altura de la celda basada en la cantidad de líneas que tiene MultiCell
        $maxAltura = max(
            $this->NbLines(50, $fila['observaciones']) * 6,  // Altura de la MultiCell
            6  // Altura mínima de las demás celdas
        );

        $this->Cell(12, $maxAltura, $fila['id_reparacion'], 1);
        $this->Cell(28, $maxAltura, $fila['fecha_inicio'], 1);
        $this->Cell(28, $maxAltura, $fechaEstimada, 1);
        $this->Cell(28, $maxAltura, $fechaFinal, 1);
        $this->Cell(28, $maxAltura, $diferenciaTexto, 1);
        $this->Cell(25, $maxAltura, ucwords($fila['estado_reparacion']), 1);
        $this->Cell(25, $maxAltura, ucwords($fila['nom_tipo']), 1);
        $this->Cell(25, $maxAltura, $fila['marca'], 1);
        $this->Cell(30, $maxAltura, ucwords($fila['nom_empleado'] . ' ' . $fila['ape_empleado']), 1);

        // MultiCell para la descripción con la altura correcta
        $x = $this->GetX();
        $y = $this->GetY();
        $this->MultiCell(50, 6, $fila['observaciones'], 1);
        $this->SetXY($x + 50, $y); // Ajustar posición después de MultiCell

        $this->Ln($maxAltura);  // Avanzar la fila completa con la altura máxima

    }
}

}

// Ejecutar consulta SQL con filtros
$datos = [];

if (!empty($rango)) {
    $fechas = explode(" to ", $rango);
    if (count($fechas) == 2) {
        $inicio = trim($fechas[0]);
        $fin = trim($fechas[1]);

        $sql = "SELECT r.id_reparacion, r.fecha_inicio, r.fecha_fin_estimada, r.fecha_finalizacion, r.estado_reparacion,
               t.nom_tipo, el.marca, el.modelo, e.nom_empleado, e.ape_empleado, a.observaciones
               FROM reparaciones r
               INNER JOIN electrodomesticos el ON r.idelectrodomesticos = el.idelectrodomesticos
               INNER JOIN tipo_electro t ON el.tipo_electro = t.idtipo_electro
               INNER JOIN atencion_presupuesto a ON r.id_reparacion = a.id_reparacion
               INNER JOIN empleados e ON r.id_tecnico = e.idempleados
               WHERE r.fecha_inicio BETWEEN :inicio AND :fin";


        if (!empty($tipo)) {
            $sql .= " AND el.tipo_electro = :tipo";
        }
        if (!empty($tecnico)) {
            $sql .= " AND r.id_tecnico = :tecnico";
        }

        $stmt = $base->prepare($sql);
        $stmt->bindParam(':inicio', $inicio);
        $stmt->bindParam(':fin', $fin);
        if (!empty($tipo)) $stmt->bindParam(':tipo', $tipo);
        if (!empty($tecnico)) $stmt->bindParam(':tecnico', $tecnico);
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Generar PDF
$filtro_fecha_texto = "$inicio al $fin";
$pdf = new PDF('L', 'mm', 'A4');
$pdf->filtro_fecha = $filtro_fecha_texto;
$pdf->AddPage();
$pdf->TablaHeader();
$pdf->TablaContenido($datos);
$pdf->Output();
?>
