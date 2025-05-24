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

            $ticketModel = $this->model('Ticket');
            $ticketId = $ticketModel->create($eventId, $name, $email);

            // Generate QR Code
            $qrPath = "../public/qrcodes/ticket_$ticketId.png";
            QRcode::png("TICKET_ID:$ticketId", $qrPath);

            $this->view('tickets/confirmation', [
                'ticketId' => $ticketId,
                'name' => $name,
                'email' => $email,
                'qrPath' => str_replace("../public", "", $qrPath)
            ]);
            return;
        }

        $this->view('tickets/book', ['eventId' => $eventId]);
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

        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'EventPro - Your Ticket', 0, 1, 'C');

        $pdf->Ln(5);
        $pdf->Cell(0, 10, "Name: " . $ticket['name'], 0, 1);
        $pdf->Cell(0, 10, "Email: " . $ticket['email'], 0, 1);
        $pdf->Cell(0, 10, "Ticket ID: " . $ticket['id'], 0, 1);

        $pdf->Ln(10);
        $qrPath = "../public/qrcodes/ticket_" . $ticket['id'] . ".png";
        if (file_exists($qrPath)) {
            $pdf->Image($qrPath, '', '', 50, 50, 'PNG');
        }

        $pdf->Output("ticket_" . $ticket['id'] . ".pdf", 'I');
    }
}
