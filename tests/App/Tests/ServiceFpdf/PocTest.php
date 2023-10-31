<?php

declare(strict_types=1);

namespace App\Tests\ServiceFpdf;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use setasign\Fpdi\Fpdi;
use mikehaertl\pdftk\Pdf;

class PocTest extends KernelTestCase
{
    public function testFoo()
    {
        //global $argc, $argv;
        //require('/var/project/vendor/setasign/fpdf/makefont/makefont.php');
        //MakeFont('/var/project/HeronSerif-Bold.otf');

        //$fontfile = '/var/project/Raleway-Regular.ttf';
        //$output=null;
        //$retval=null;
        //$command = 'php /var/project/vendor/setasign/fpdf/makefont/makefont.php ' . $fontfile . ' cp1252 true true';

        //exec($command, $output, $retval);
        //echo "Returned with status $retval and output:\n";
        //print_r($output);

        //return;

        $texts = [
            'Čeégf',
            'La cédille (ç)',
            'Welt',
            'Lorem Ipsum',
            'Max Mausermann',
            'Test 123',
            'Hallo',
            //'Welt',
            //'Lorem Ipsum',
            //'Max Mausermann',
            //'Test 123',
        ];

        $font     = 'Raleway';
        $fontSize = 8;

        $this->assertEquals(1, 1);
        // initiate FPDI
        $pdf = new Fpdi(unit: 'mm');

        $pdf->AddFont('Saddlebag', 'b', 'Saddlebag-bold.php');
        $pdf->AddFont('Raleway', '', 'Raleway-Regular.php');
        // add a page
        $pdf->AddPage('P', [50, 100]);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        // set the source file

        $pdf->setSourceFile('Vorlage_Bereich_zwei_Zeilen_ohne.pdf');
        // import page 1
        $tplId = $pdf->importPage(1);
        $pdf->useTemplate($tplId, 0, 0, 50, 100, false);


        $pdf->SetTextColor(150, 100, 50);
        $pdf->SetFont($font, '', $fontSize);


        $yPos = 30;

        foreach ($texts as $text) {
            $cellHeight = $this->getCellHeight($font, $fontSize);
            $pdf->SetXY(6, $yPos);
            $pdf->Cell(40, $cellHeight, $text, 1);
            $yPos += $cellHeight;
        }

        $tmp = 'tmp' . time() . '.pdf';
        $pdf->Output($tmp, 'F', true);

        $orientation = 50 > $yPos ? 'L' : 'P';
        $pdf         = new Fpdi();
        $pdf->AddPage($orientation, [50, $yPos]);
        $pdf->SetMargins(0, 0, 0);
        $pdf->setSourceFile($tmp);
        // import page 1
        $tplId = $pdf->importPage(1);
        // use the imported page and place it at point 10,10 with a width of 100 mm
        $pdf->useTemplate($tplId, 0, 0, 50, 100, false);

        unlink($tmp);
        $tmp = 'output' . time() . '.pdf';
        $pdf->Output($tmp, 'F', true);
    }


    public function testStamp()
    {
        $name    = 'test-' . time();
        $file    = $name . '.pdf';
        $file2   = $name . '-2.pdf';
        $stamp   = $name . '-stamp.pdf';
        $stamped = $name . '-stamped.pdf';
        copy('template.pdf', $file);

        // generate the stamp mounted on the full size transparent template
        $pdf = new Fpdi(unit: 'mm');
        // add a page
        $pdf->AddPage('P', [50, 100]);
        $pdf->SetMargins(0, 80, 0);
        $pdf->SetAutoPageBreak(false, 0);
        // set the source file
        $pdf->setSourceFile('Blatt.pdf');
        // import page 1
        $tplId = $pdf->importPage(1);
        $pdf->useTemplate($tplId, 0, 35, 50, 10, false);
        $pdf->Output($stamp, 'F', true);


        $pdf = new Fpdi(unit: 'mm');
        // add a page
        $pdf->AddPage('P', [50, 100]);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        // set the source file

        $pdf->setSourceFile($file);
        // import page 1
        $tplId = $pdf->importPage(1);
        $pdf->useTemplate($tplId, 0, 0, 50, 100, false);
        $pdf->Output($file2, 'F', true);


        $pdf    = new Pdf($file2);
        $result = $pdf->stamp($stamp)
            ->saveAs('/var/project/' . $stamped);
        if (false === $result) {
            $error = $pdf->getError();
            echo $error;
        }
    }


    private function getCellHeight(string $font, int $fontSize): int
    {
        return ceil($fontSize * $this->getFontFactor($font));
    }

    private function getFontFactor(string $font): float
    {
        switch ($font) {
            case 'Arial':
                return 0.4;
            case 'Saddlebag':
                return 0.35;
            default:
                return 0.5;
        }
    }
}
