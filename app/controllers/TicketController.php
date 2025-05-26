<?php
require_once '../app/core/Controller.php';
require_once '../vendor/phpqrcode/qrlib.php';
require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';

class TicketController extends Controller {

   public function bookView($eventId) {
    $eventModel = $this->model('Event');
    $event = $eventModel->getById($eventId); // fetch the event info

    $this->view('tickets/book', [
        'eventId' => $eventId,
        'event' => $event
    ]);
}


    public function book($eventId) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $quantity = (int) $_POST['quantity'];

        $eventModel = $this->model('Event');
        $event = $eventModel->getById($eventId);
        $price = $event['price'];
        $total = $price * $quantity;

        $ticketModel = $this->model('Ticket');
        $ticketId = $ticketModel->create($eventId, $name, $email, $quantity);

        // Generate QR Code
        $qrData = "Ticket ID: $ticketId\nName: $name\nEvent: {$event['title']}\nQty: $quantity\nTotal: $$total";
        $qrPath = "../public/qrcodes/ticket_$ticketId.png";
        QRcode::png($qrData, $qrPath);

        $this->view('tickets/confirmation', [
            'ticketId' => $ticketId,
            'name' => $name,
            'email' => $email,
            'event' => $event,
            'quantity' => $quantity,
            'total' => $total,
            'qrPath' => str_replace("../public", "", $qrPath)
        ]);
    }
}


    public function confirmView($ticketId) {
        $ticketModel = $this->model('Ticket');
        $ticket = $ticketModel->getById($ticketId);
        $this->view('tickets/confirmation', [
            'ticketId' => $ticketId,
            'name' => $ticket['name'],
            'email' => $ticket['email'],
            'qrPath' => "/qrcodes/ticket_$ticketId.png"
        ]);
    }

    public function download($ticketId) {
    $ticketModel = $this->model('Ticket');
    $ticket = $ticketModel->getById($ticketId);

    if (!$ticket) {
        die("Ticket not found.");
    }

    $eventModel = $this->model('Event');
    $event = $eventModel->getById($ticket['event_id']);
    $total = $event['price'] * $ticket['quantity'];

    // Create new PDF with smaller page size
    $pdf = new TCPDF('P', 'mm', array(80, 120), true, 'UTF-8', false);
    $pdf->SetTitle('EventPro Ticket #' . $ticket['id']);
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(false, 0);

    // Set colorful header with gradient
    $pdf->SetFillColor(41, 128, 185);  // Nice blue color
    $pdf->Rect(0, 0, 80, 15, 'F');
    
    // White text for header
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetXY(0, 5);
    $pdf->Cell(80, 5, 'EVENTPRO TICKET', 0, 0, 'C');

    // Reset text color
    $pdf->SetTextColor(0, 0, 0);

    // Logo - smaller and positioned better
    $logoPath = "../public/event-pro.png";
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, 58, 17, 25, 0, 'PNG');
    }

    // Ticket ID with colorful highlight
    $pdf->SetXY(5, 20);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(231, 76, 60);  // Red color
    $pdf->Cell(30, 5, 'Ticket #' . $ticket['id'], 0, 1);

    // Divider line
    // $pdf->SetDrawColor(41, 128, 185);
    // $pdf->Line(5, 27, 75, 27);

    // Attendee info with different colors
    $pdf->SetXY(5, 30);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetTextColor(0, 0, 0);  // Black
    $pdf->Cell(20, 4, 'Name:', 0, 0);
    $pdf->SetFont('helvetica', '', 7);
    $pdf->SetTextColor(44, 62, 80);  // Dark blue-gray
    $pdf->Cell(50, 4, $ticket['name'], 0, 1);

    $pdf->SetXY(5, 35);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(20, 4, 'Email:', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(44, 62, 80);
    $pdf->Cell(50, 4, $ticket['email'], 0, 1);

    // Event details section header
    $pdf->SetXY(5, 42);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(41, 128, 185);  // Blue
    $pdf->Cell(70, 5, 'EVENT DETAILS', 0, 1);

    // Event details
    $pdf->SetXY(5, 47);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(20, 4, 'Event:', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(44, 62, 80);
    $pdf->Cell(50, 4, $event['title'], 0, 1);

    $pdf->SetXY(5, 51);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetTextColor(0, 0, 14);
    $pdf->Cell(20, 4, 'Date:', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(44, 62, 80);
    $pdf->Cell(50, 4, $event['event_date'], 0, 1);

    $pdf->SetXY(5, 55);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(20, 4, 'Venue:', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(44, 62, 80);
    $pdf->Cell(50, 4, $event['venue'], 0, 1);

    $pdf->SetXY(5, 59);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(20, 4, 'Tickets:', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(44, 62, 80);
    $pdf->Cell(50, 4, $ticket['quantity'], 0, 1);

    $pdf->SetXY(5, 63);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetTextColor(39, 174, 96);  // Green for price
    $pdf->Cell(20, 4, 'Total:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(50, 4, '$' . number_format($total, 2), 0, 1);

    // QR code - smaller and positioned at bottom
    $qrPath = "../public/qrcodes/ticket_" . $ticket['id'] . ".png";
    if (file_exists($qrPath)) {
        $pdf->Image($qrPath, 50, 70, 25, 25, 'PNG');
        $pdf->SetXY(50, 95);
        $pdf->SetFont('helvetica', 'I', 6);
        $pdf->SetTextColor(149, 165, 166);  // Gray
        $pdf->Cell(25, 3, 'Scan at entry', 0, 1, 'C');
    }

    // Footer note
    $pdf->SetXY(5, 100);
    $pdf->SetFont('helvetica', 'I', 6);
    $pdf->SetTextColor(149, 165, 166);
    $pdf->Cell(70, 3, 'Thank you for using EventPro!', 0, 1, 'C');

    $pdf->Output("ticket_" . $ticket['id'] . ".pdf", 'I');
}

}
