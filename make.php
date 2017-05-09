<?php

set_time_limit(0);

require('fpdf/fpdf.php');
require('qr/qrlib.php');


    $pdf= new FPDF($orientation='P',$unit='pt',$format='A4');
    $pdf->SetAuthor('Vyacheslav Nadejdin');
    $pdf->SetTitle('QR Codes for marks the production');
    $pdf->SetFont('Helvetica','B',14);
    $pdf->SetTextColor(0,0,0);
    $pdf->AddPage('P');
    $pdf->SetDisplayMode('real','default');
    $qrClass = new QR;
    $pages = 20;

    // Banal "while" counter
    while($pages > 0){
        $c = 0;

        // Banal "for" counter
        for($i=20;$i<1000;$i=$i+116){
            $c++;

            // Generate QR code and info block on the PDF page
            $qr = $qrClass->createQrCode();
            $pdf->Image($qr[1],10,$i,100,100,'png');
            unlink($qr[1]);
            $pdf->SetXY(110,$i);
            $pdf->Cell(20, 40, $qr[0]);
            $pdf->Rect(114,$i+35, 150, 55);

            // Generate QR code and info block on the PDF page
            $qr = $qrClass->createQrCode();
            $pdf->Image($qr[1],290,$i,100,100,'png');
            unlink($qr[1]);
            $pdf->SetXY(390,$i);
            $pdf->Cell(20, 40, $qr[0]);
            $pdf->Rect(394,$i+35, 150, 55);
            $pdf->SetXY(10,$i);

            // Checking of the qr codes blocks in a page. If more 7, "break" and  the generate new page.
            if( $c >= 7 ) break; 

        }

        // Generate new page in the pdf file
        $pdf->AddPage('P');

        // One page less to or end
        $pages--;
    }
    $pdf->Output('I');
    $qrClass->clear();

    // Class for working with the QR-generator

    class QR{
        public $images = [];
        private function InArr( $image = false ){
            if( $image == false ) return true;
            $this->images[] = $image;
        }
        public function clear(){
            foreach( $this->images as $el )
                unlink( $el );
        }
        public function createQrCode(){
            $text_to_qr = "P-".rand(111111,999999)."-".rand(111111,999999);
            $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
            $PNG_WEB_DIR = 'temp/';
            if (!file_exists($PNG_TEMP_DIR))
                mkdir($PNG_TEMP_DIR);
            $errorCorrectionLevel = 'L';
            $matrixPointSize = 4;
            try{
                $filename = $PNG_TEMP_DIR.md5($text_to_qr.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
                QRcode::png($text_to_qr, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
            } catch (Exception $ex){

            }
            $this->InArr($filename);
            return [$text_to_qr,$filename];
        }
    }

?>