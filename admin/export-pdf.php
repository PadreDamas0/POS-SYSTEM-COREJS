<?php
require_once '../config.php';
require_once '../functions.php';
require_admin();

// Check if TCPDF class exists, if not create a simple alternative
if (!class_exists('TCPDF')) {
    // Create a simple PDF class for basic PDF generation
    class SimplePDF {
        private $pdf_content = "";
        private $y_position = 10;
        private $x_position = 10;
        private $page_height = 277;
        private $page_width = 210;
        
        public function __construct() {
            $this->pdf_content = "%PDF-1.4\n";
            $this->addObject(1, "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n");
        }
        
        private function addObject($num, $content) {
            $this->pdf_content .= $content;
        }
        
        public function generatePDF($html, $filename) {
            // Convert HTML to PDF using a workaround
            $html2pdf_content = "
%PDF-1.4
1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj
2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj
3 0 obj<</Type/Page/MediaBox[0 0 595 842]/Parent 2 0 R/Resources<</Font<</F1 4 0 R>>>>/Contents 5 0 R>>endobj
4 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj
5 0 obj<</Length 500>>stream
BT
/F1 12 Tf
50 750 Td
(DAMBALASEK Sales Report) Tj
0 -30 Td
(Generated: " . date('M d, Y H:i:s') . ") Tj
ET
endstream
endobj
xref
0 6
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
0000000244 00000 n 
0000000333 00000 n 
trailer<</Size 6/Root 1 0 R>>
startxref
883
%%EOF
";
            return $html2pdf_content;
        }
    }
}


$date_start = $_GET['date_start'] ?? '';
$date_end = $_GET['date_end'] ?? '';

$orders = get_orders($date_start, $date_end);


$total_revenue = 0;
foreach ($orders as $order) {
    $total_revenue += $order['total_amount'];
}


$filename = 'DAMBALASEK_Report_' . date('YmdHis') . '.pdf';

// PDF
$pdf = "%PDF-1.4\n";
$pdf .= "1 0 obj\n<</Type/Catalog/Pages 2 0 R>>\nendobj\n";
$pdf .= "2 0 obj\n<</Type/Pages/Kids[3 0 R]/Count 1>>\nendobj\n";


$page_content = "BT\n";
$page_content .= "/F1 20 Tf\n";
$page_content .= "50 750 Td\n";
$page_content .= "(DAMBALASEK Sales Report) Tj\n";
$page_content .= "0 -30 Td\n";
$page_content .= "/F1 10 Tf\n";
$page_content .= "(Generated: " . date('M d, Y H:i:s') . ") Tj\n";

if ($date_start || $date_end) {
    $page_content .= "0 -20 Td\n";
    $page_content .= "(Period: " . ($date_start ?: 'All') . " to " . ($date_end ?: 'Today') . ") Tj\n";
}

$page_content .= "0 -40 Td\n";
$page_content .= "/F1 9 Tf\n";

$y_offset = 0;
$page_content .= "(ORDER #  | CASHIER | AMOUNT | PAYMENT | CHANGE | DATE) Tj\n";

foreach ($orders as $order) {
    $y_offset += 15;
    $page_content .= "0 -15 Td\n";
    $line = sprintf(
        "%s | %s | %.2f | %.2f | %.2f | %s",
        substr($order['order_number'], 0, 8),
        substr($order['username'], 0, 10),
        $order['total_amount'],
        $order['payment_amount'],
        $order['change_amount'],
        date('M d, Y', strtotime($order['date_added']))
    );
    $page_content .= "(" . addslashes($line) . ") Tj\n";
}

$page_content .= "0 -30 Td\n";
$page_content .= "/F1 12 Tf\n";
$page_content .= "(TOTAL REVENUE: ₱" . number_format($total_revenue, 2) . ") Tj\n";
$page_content .= "0 -15 Td\n";
$page_content .= "/F1 10 Tf\n";
$page_content .= "(Total Orders: " . count($orders) . ") Tj\n";
$page_content .= "ET\n";

$pdf .= "3 0 obj\n<</Type/Page/MediaBox[0 0 595 842]/Parent 2 0 R/Resources<</Font<</F1 4 0 R>>>>/Contents 5 0 R>>\nendobj\n";
$pdf .= "4 0 obj\n<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>\nendobj\n";
$pdf .= "5 0 obj\n<</Length " . strlen($page_content) . ">>\nstream\n" . $page_content . "endstream\nendobj\n";

$xref = strlen($pdf);
$pdf .= "xref\n";
$pdf .= "0 6\n";
$pdf .= "0000000000 65535 f \n";
$pdf .= sprintf("%010d 00000 n \n", 9);
$pdf .= sprintf("%010d 00000 n \n", 58);
$pdf .= sprintf("%010d 00000 n \n", 115);
$pdf .= sprintf("%010d 00000 n \n", 214);
$pdf .= sprintf("%010d 00000 n \n", 303);
$pdf .= "trailer\n<</Size 6/Root 1 0 R>>\n";
$pdf .= "startxref\n" . $xref . "\n";
$pdf .= "%%EOF\n";


header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('Content-Length: ' . strlen($pdf));

echo $pdf;
exit;
?>
