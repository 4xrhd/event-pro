<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../../vendor/phpqrcode/qrlib.php';
require_once __DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php';

class TicketController extends Controller {
    public function bookView($eventId) {
        $this->ensureSession();
        $event = $this->loadEvent((int) $eventId);
        if (!$event) {
            $this->notFound();
            return;
        }

        $eventModel = $this->model('Event');
        $booked = $eventModel->getBookedTicketCount((int) $eventId);
        $remaining = $event['capacity'] !== null ? max(0, (int) $event['capacity'] - $booked) : null;

        $this->view('tickets/book', [
            'eventId' => (int) $eventId,
            'event' => $event,
            'remaining' => $remaining,
        ]);
    }

    public function book($eventId) {
        $this->ensureSession();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->notFound();
            return;
        }

        $event = $this->loadEvent((int) $eventId);
        if (!$event) {
            $this->notFound();
            return;
        }

        // Verify CSRF token
        if (
            !isset($_POST['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'] ?? '', (string) $_POST['csrf_token'])
        ) {
            $this->renderBookError($eventId, $event, 'Invalid CSRF token.');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $quantity = (int) ($_POST['quantity'] ?? 0);

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $quantity < 1 || $quantity > 10) {
            $this->renderBookError($eventId, $event, 'Please provide a valid name, email address, and a quantity between 1 and 10.', $name, $email, $quantity);
            return;
        }

        $eventModel = $this->model('Event');
        $booked = $eventModel->getBookedTicketCount((int) $eventId);
        if ($event['capacity'] !== null && ($booked + $quantity) > (int) $event['capacity']) {
            $remaining = max(0, (int) $event['capacity'] - $booked);
            $errorMsg = $remaining > 0 
                ? "Only {$remaining} ticket(s) remaining for this event." 
                : "Sorry, this event is sold out!";
            $this->renderBookError($eventId, $event, $errorMsg, $name, $email, $quantity);
            return;
        }

        $total = (float) $event['price'] * $quantity;
        $ticketModel = $this->model('Ticket');
        $ticketId = $ticketModel->create(
            (int) $eventId,
            $name,
            $email,
            $quantity,
            $total,
            $_SESSION['user_id'] ?? null
        );

        if (!$ticketId) {
            $this->renderBookError($eventId, $event, 'Ticket booking failed due to capacity limits or database error. Please try again.', $name, $email, $quantity);
            return;
        }

        // Store ticket ID in session so the user can access confirmation/download
        if (!isset($_SESSION['booked_tickets'])) {
            $_SESSION['booked_tickets'] = [];
        }
        $_SESSION['booked_tickets'][] = (int) $ticketId;

        $this->ensureQrCode($ticketId, $name, $event, $quantity, $total);

        $this->view('tickets/confirmation', [
            'ticketId' => $ticketId,
            'name' => $name,
            'email' => $email,
            'event' => $event,
            'quantity' => $quantity,
            'total' => $total,
            'qrPath' => $this->getQrWebPath($ticketId),
        ]);
    }

    public function myTicketsView() {
        $this->ensureAuthenticated();
        
        $userId = (int) $_SESSION['user_id'];
        $ticketModel = $this->model('Ticket');
        $tickets = $ticketModel->getByUserId($userId);

        $this->view('tickets/my_tickets', [
            'tickets' => $tickets
        ]);
    }

    public function checkinView() {
        $this->ensureStaffOrAdmin();

        $this->view('tickets/checkin', [
            'successMessage' => $this->pullSessionMessage('checkin_success'),
            'errorMessage' => $this->pullSessionMessage('checkin_error'),
        ]);
    }

    public function checkin() {
        $this->ensureStaffOrAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->notFound();
            return;
        }

        if (
            !isset($_POST['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'] ?? '', (string) $_POST['csrf_token'])
        ) {
            $_SESSION['checkin_error'] = 'Invalid CSRF token';
            redirect('tickets/checkin');
        }

        $ticketId = (int) ($_POST['ticket_id'] ?? 0);
        if ($ticketId <= 0) {
            $_SESSION['checkin_error'] = 'Please provide a valid numeric Ticket ID.';
            redirect('tickets/checkin');
        }

        $ticketModel = $this->model('Ticket');
        $ticket = $ticketModel->getById($ticketId);

        if (!$ticket) {
            $_SESSION['checkin_error'] = "Ticket #{$ticketId} was not found in the system.";
            redirect('tickets/checkin');
        }

        if ($ticket['is_checked_in']) {
            $_SESSION['checkin_error'] = "Ticket #{$ticketId} was ALREADY checked in at {$ticket['checked_in_at']}!";
            redirect('tickets/checkin');
        }

        $staffUserId = (int) $_SESSION['user_id'];
        if ($ticketModel->checkInTicket($ticketId, $staffUserId)) {
            $_SESSION['checkin_success'] = "✅ Ticket #{$ticketId} successfully checked in for {$ticket['name']}!";
        } else {
            $_SESSION['checkin_error'] = "Failed to process check-in for ticket #{$ticketId}.";
        }

        redirect('tickets/checkin');
    }

    public function confirmView($ticketId) {
        $this->ensureSession();
        $ticketModel = $this->model('Ticket');
        $ticket = $ticketModel->getById((int) $ticketId);
        if (!$ticket) {
            $this->notFound();
            return;
        }

        if (!$this->canAccessTicket($ticket)) {
            $_SESSION['error_message'] = 'You do not have permission to view this ticket.';
            redirect('events');
        }

        $event = $this->loadEvent((int) $ticket['event_id']);
        if (!$event) {
            $this->notFound();
            return;
        }

        $total = isset($ticket['total_amount']) ? (float) $ticket['total_amount'] : (float) $event['price'] * (int) $ticket['quantity'];
        $this->ensureQrCode((int) $ticketId, $ticket['name'], $event, (int) $ticket['quantity'], $total);

        $this->view('tickets/confirmation', [
            'ticketId' => (int) $ticketId,
            'name' => $ticket['name'],
            'email' => $ticket['email'],
            'event' => $event,
            'quantity' => (int) $ticket['quantity'],
            'total' => $total,
            'qrPath' => $this->getQrWebPath((int) $ticketId),
        ]);
    }

    public function download($ticketId) {
        $this->ensureSession();
        $ticketModel = $this->model('Ticket');
        $ticket = $ticketModel->getById((int) $ticketId);
        if (!$ticket) {
            $this->notFound();
            return;
        }

        if (!$this->canAccessTicket($ticket)) {
            $_SESSION['error_message'] = 'You do not have permission to download this ticket.';
            redirect('events');
        }

        $event = $this->loadEvent((int) $ticket['event_id']);
        if (!$event) {
            $this->notFound();
            return;
        }

        $total = isset($ticket['total_amount']) ? (float) $ticket['total_amount'] : (float) $event['price'] * (int) $ticket['quantity'];
        $qrPath = $this->ensureQrCode((int) $ticket['id'], $ticket['name'], $event, (int) $ticket['quantity'], $total);

        $pdf = new TCPDF('P', 'mm', [80, 120], true, 'UTF-8', false);
        $pdf->SetTitle('EventPro Ticket #' . $ticket['id']);
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false, 0);

        $pdf->SetFillColor(41, 128, 185);
        $pdf->Rect(0, 0, 80, 15, 'F');

        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetXY(0, 5);
        $pdf->Cell(80, 5, 'EVENTPRO TICKET', 0, 0, 'C');

        $pdf->SetTextColor(0, 0, 0);

        $logoPath = APP_ROOT . "/public/event-pro.png";
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 58, 17, 25, 0, 'PNG');
        }

        $pdf->SetXY(5, 20);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(231, 76, 60);
        $pdf->Cell(30, 5, 'Ticket #' . $ticket['id'], 0, 1);

        $pdf->SetXY(5, 30);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 4, 'Name:', 0, 0);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->Cell(50, 4, $ticket['name'], 0, 1);

        $pdf->SetXY(5, 35);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 4, 'Email:', 0, 0);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->Cell(50, 4, $ticket['email'], 0, 1);

        $pdf->SetXY(5, 42);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(41, 128, 185);
        $pdf->Cell(70, 5, 'EVENT DETAILS', 0, 1);

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
        $pdf->Cell(50, 4, (int) $ticket['quantity'], 0, 1);

        $pdf->SetXY(5, 63);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(39, 174, 96);
        $pdf->Cell(20, 4, 'Total:', 0, 0);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(50, 4, '$' . number_format($total, 2), 0, 1);

        if (file_exists($qrPath)) {
            $pdf->Image($qrPath, 50, 70, 25, 25, 'PNG');
            $pdf->SetXY(50, 95);
            $pdf->SetFont('helvetica', 'I', 6);
            $pdf->SetTextColor(149, 165, 166);
            $pdf->Cell(25, 3, 'Scan at entry', 0, 1, 'C');
        }

        $pdf->SetXY(5, 100);
        $pdf->SetFont('helvetica', 'I', 6);
        $pdf->SetTextColor(149, 165, 166);
        $pdf->Cell(70, 3, 'Thank you for using EventPro!', 0, 1, 'C');

        $pdf->Output("ticket_" . $ticket['id'] . ".pdf", 'I');
    }

    private function canAccessTicket(array $ticket): bool {
        $role = getCurrentUserRole();
        if ($role === 'admin' || $role === 'staff') {
            return true;
        }

        $currentUserId = getCurrentUserId();
        if ($currentUserId !== null && isset($ticket['user_id']) && (int) $ticket['user_id'] === (int) $currentUserId) {
            return true;
        }

        if (!empty($_SESSION['booked_tickets']) && in_array((int) $ticket['id'], $_SESSION['booked_tickets'], true)) {
            return true;
        }

        return false;
    }

    private function renderBookError($eventId, $event, $errorMessage, $name = '', $email = '', $quantity = 1) {
        $eventModel = $this->model('Event');
        $booked = $eventModel->getBookedTicketCount((int) $eventId);
        $remaining = $event['capacity'] !== null ? max(0, (int) $event['capacity'] - $booked) : null;

        $this->view('tickets/book', [
            'eventId' => (int) $eventId,
            'event' => $event,
            'remaining' => $remaining,
            'error' => $errorMessage,
            'oldInput' => [
                'name' => $name,
                'email' => $email,
                'quantity' => max($quantity, 1),
            ],
        ]);
    }

    private function loadEvent(int $eventId) {
        if ($eventId <= 0) {
            return null;
        }

        $eventModel = $this->model('Event');
        return $eventModel->getById($eventId);
    }

    private function ensureQrCode(int $ticketId, string $name, array $event, int $quantity, float $total) {
        $directory = APP_ROOT . '/public/qrcodes';
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $qrPath = $directory . "/ticket_{$ticketId}.png";
        if (!file_exists($qrPath)) {
            $qrData = sprintf(
                "Ticket ID: %d\nName: %s\nEvent: %s\nQty: %d\nTotal: $%0.2f",
                $ticketId,
                $name,
                $event['title'],
                $quantity,
                $total
            );
            QRcode::png($qrData, $qrPath);
        }

        return $qrPath;
    }

    private function getQrWebPath(int $ticketId) {
        return asset("qrcodes/ticket_{$ticketId}.png");
    }

    private function ensureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    private function ensureAuthenticated() {
        $this->ensureSession();

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? url('events');
            redirect('auth/login');
        }
    }

    private function ensureStaffOrAdmin() {
        $this->ensureAuthenticated();

        $role = $_SESSION['user_role'] ?? null;
        if ($role !== 'admin' && $role !== 'staff') {
            $_SESSION['error_message'] = 'Only Staff or Admin users can access ticket check-in.';
            redirect('events');
        }
    }

    private function pullSessionMessage($key) {
        if (!isset($_SESSION[$key])) {
            return null;
        }

        $message = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $message;
    }
}
