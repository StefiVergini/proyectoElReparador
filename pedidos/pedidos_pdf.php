<?php
    require '../conexionPDO.php';
    ob_start();
    require('../fpdf/fpdf.php');
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        try{
            $sql = "SELECT pedidos.id_ped, pedidos.id_stock, pedidos.idempleados, pedidos.fecha_pedido, pedidos.fecha_ingreso, pedidos.cantidad_pedido, pedidos.precio_pedido, pedidos.estado_pedido, stock.idstock, stock.descripcion_art, empleados.idempleados, empleados.dni_empleado, empleados.nom_empleado, empleados.ape_empleado FROM pedidos INNER JOIN stock ON pedidos.id_stock = stock.idstock INNER JOIN empleados ON pedidos.idempleados = empleados.idempleados";
        
            $resultado=$base->prepare($sql);
            $resultado -> execute();
            $data = $resultado ->fetchall(PDO::FETCH_ASSOC);

            /*if($resultado){
                while($row = $resultado ->fetch(PDO::FETCH_ASSOC)){
                    $row['id_stock'];
                    $row['idempleados'];
                    $row['fecha_pedido']; 
                    $row['fecha_ingreso']; 
                    $row['cantidad_pedido'];
                    $row['precio_pedido'];
                    $row['estado_pedido'];
                }
            }*/

            //Clase para las multi-celdas
            class PDF extends FPDF
            {
                protected $widths;
                protected $aligns;

                function SetWidths($w)
                {
                    // Set the array of column widths
                    $this->widths = $w;
                }

                function SetAligns($a)
                {
                    // Set the array of column alignments
                    $this->aligns = $a;
                }

                function Row($data, $setX)
                {
                    // Calculate the height of the row
                    $nb = 0;
                    for($i=0;$i<count($data);$i++)
                        $nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));
                    $h = 5*$nb;
                    // Issue a page break first if needed
                    $this->CheckPageBreak($h);
                    // Draw the cells of the row
                    for($i=0;$i<count($data);$i++)
                    {
                        $w = $this->widths[$i];
                        $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                        // Save the current position
                        $x = $this->GetX();
                        $y = $this->GetY();
                        // Draw the border
                        $this->Rect($x,$y,$w,$h);
                        // Print the text
                        $this->MultiCell($w,5,$data[$i],0,$a);
                        // Put the position to the right of the cell
                        $this->SetXY($x+$w,$y);
                    }
                    // Go to the next line
                    $this->Ln($h);
                }

                function CheckPageBreak($h)
                {
                    // If the height h would cause an overflow, add a new page immediately
                    if($this->GetY()+$h>$this->PageBreakTrigger)
                        $this->AddPage($this->CurOrientation);
                }

                function NbLines($w, $txt)
                {
                    // Compute the number of lines a MultiCell of width w will take
                    if(!isset($this->CurrentFont))
                        $this->Error('No font has been set');
                    $cw = $this->CurrentFont['cw'];
                    if($w==0)
                        $w = $this->w-$this->rMargin-$this->x;
                    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
                    $s = str_replace("\r",'',(string)$txt);
                    $nb = strlen($s);
                    if($nb>0 && $s[$nb-1]=="\n")
                        $nb--;
                    $sep = -1;
                    $i = 0;
                    $j = 0;
                    $l = 0;
                    $nl = 1;
                    while($i<$nb)
                    {
                        $c = $s[$i];
                        if($c=="\n")
                        {
                            $i++;
                            $sep = -1;
                            $j = $i;
                            $l = 0;
                            $nl++;
                            continue;
                        }
                        if($c==' ')
                            $sep = $i;
                        $l += $cw[$c];
                        if($l>$wmax)
                        {
                            if($sep==-1)
                            {
                                if($i==$j)
                                    $i++;
                            }
                            else
                                $i = $sep+1;
                            $sep = -1;
                            $j = $i;
                            $l = 0;
                            $nl++;
                        }
                        else
                            $i++;
                    }
                    return $nl;
                }
            }
    
            //Generar Reporte PDF
            $pdf = new PDF('L','mm','A4');
            $pdf->AddPage();
            $pdf->setAutoPageBreak(true,20);
            $pdf->SetX(10);  
            $pdf->SetFont('Arial','B',10);
            
            $pdf->Cell(30,10,'ID Pedido', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'ID Stock', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'ID Empleados', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'Fecha Pedido', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'Fecha Cierre', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'Cantidad', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'Precio', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'Estado', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'ID Art.', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'Desc. Art.', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'ID Empleados', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'DNI Emp.', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'Nomb. Emp.', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'Ape. Emp.', 'B', 1, 'C',0);
            
            $pdf->SetWidths(array(30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30));

            for($i=0;$i<count($data);$i++){
                $pdf->SetX(50);
                //$pdf->SetY(50);
                $pdf->Ln(0.6); 
                $pdf->Row(array($data[$i]['id_ped'], $data[$i]['id_stock'],$data[$i]['idempleados'], $data[$i]['fecha_pedido'], $data[$i]['fecha_ingreso'], $data[$i]['cantidad_pedido'],  $data[$i]['precio_pedido'], $data[$i]['estado_pedido'], $data[$i]['idstock'], utf8_decode($data[$i]['descripcion_art']), $data[$i]['idempleados'], $data[$i]['dni_empleado'],utf8_decode($data[$i]['nom_empleado']),utf8_decode($data[$i]['ape_empleado'])),30);
            }
    
            $pdf->Output();
            ob_end_flush();
          }catch(Exception $e){
            echo $e-> getMessage();
            echo "Línea del error: " . $e->getLine();
          }finally{
              $base=null;
        }
    }
?>