<?php
require_once '../assets/fpdf186/fpdf.php'; // Update the path as necessary

class PDF extends FPDF
{
    private $dateRange;

    function setDateRange($dateRange)
    {
        $this->dateRange = $dateRange;
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Sunshine Tours', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'Timecard Export Report', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, $this->dateRange, 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    function CreateTable($header, $data, $totalHours)
    {
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial', 'B', 10);

        $pageWidth = $this->GetPageWidth() - 20;
        $numColumns = count($header);
        $columnWidth = $pageWidth / $numColumns;

        foreach ($header as $colHeader) {
            $this->Cell($columnWidth, 7, $colHeader, 1, 0, 'C', true);
        }
        $this->Ln();

        $this->SetFont('Arial', '', 10);
        foreach ($data as $row) {
            if ($row[0] === 'Total Hours') {
                $this->SetFont('Arial', 'B', 10);
            }
            foreach ($row as $col) {
                $this->Cell($columnWidth, 6, $col, 1, 0, 'C');
            }
            $this->Ln();

            if ($row[0] === 'Total Hours') {
                $this->SetFont('Arial', '', 10);
                $this->SetLineWidth(.3);
            }
        }

        $this->SetFont('Arial', 'B', 10);
        foreach ($totalHours as $total) {
            $this->Cell($columnWidth, 7, $total, 1, 0, 'C', true);
        }
        $this->Ln();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode($_POST['data'], true);
    $dateRange = $_POST['dateRange'];

    $pdf = new PDF();
    $pdf->setDateRange($dateRange);
    $pdf->AddPage();

    $header = ['Employee', 'Ordinary', 'Saturday', 'Sunday', 'Other'];
    $dataArray = [];
    $totalHours = ['Total Hours', '0', '0', '0', '0'];

    foreach ($data as $driver) {
        $dataArray[] = [
            $driver['driverName'],
            $driver['totalOrdinaryHours'],
            $driver['totalSaturdayHours'],
            $driver['totalSundayHours'],
            0
        ];

        $totalHours[1] += (float)$driver['totalOrdinaryHours'];
        $totalHours[2] += (float)$driver['totalSaturdayHours'];
        $totalHours[3] += (float)$driver['totalSundayHours'];
    }

    $pdf->CreateTable($header, $dataArray, $totalHours);

    $pdfFilename = 'timecard_report_' . time() . '.pdf';
    $pdfPath = '../assets/pdfs/' . $pdfFilename;
    $pdf->Output('F', $pdfPath);

    echo json_encode(['file' => $pdfPath]);
    exit();
}
?>
