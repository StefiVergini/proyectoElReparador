<?php
    require '../conexionPDO.php';
    ob_start();
    require('../fpdf/fpdf.php');
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        try{
            $sql = "SELECT * FROM stock";
        
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
            
            $pdf->Cell(30,10,'ID Articulo', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'Descripcion', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'Cantidad', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'ID Proveedor', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'Cuit Proveedor', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'Tipo', 'B', 0, 'C',0);
            $pdf->Cell(30,10,'Estado', 'B', 1, 'C',0);
            $pdf->SetWidths(array(30, 30, 30, 30, 30, 30, 30, 30));

            for($i=0;$i<count($data);$i++){
                $pdf->SetX(50);
                //$pdf->SetY(50);
                $pdf->Ln(0.6); 
                $pdf->Row(array($data[$i]['idstock'], utf8_decode($data[$i]['descripcion_art']),$data[$i]['cantidad'], $data[$i]['idproveedores'],$data[$i]['cuit_prov'], utf8_decode($data[$i]['tipo_stock']), $data[$i]['estado_stock']),30);
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