<?php
class EventController extends Controller {
    public function indexView() {
        $this->ensureSession();

        $eventModel = $this->model('Event');
        $events = $eventModel->getAll();

        // Enrich events with capacity and booked count
        foreach ($events as &$event) {
            $event['booked_tickets'] = $eventModel->getBookedTicketCount((int) $event['id']);
        }
        unset($event);

        $this->view('events/index', [
            'events' => $events,
            'message' => empty($events) ? 'No events found. Create your first event!' : null,
            'successMessage' => $this->pullSessionMessage('success_message'),
            'errorMessage' => $this->pullSessionMessage('error_message'),
        ]);
    }

    public function createView() {
        $this->ensureAdmin();
        $this->view('events/create');
    }

    public function create() {
        $this->ensureAdmin();

        try {
            if (
                !isset($_POST['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'] ?? '', (string) $_POST['csrf_token'])
            ) {
                throw new Exception('Invalid CSRF token');
            }

            $title = trim(htmlspecialchars($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8'));
            $description = trim(htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8'));
            $date = $_POST['date'] ?? '';
            $venue = trim(htmlspecialchars($_POST['venue'] ?? '', ENT_QUOTES, 'UTF-8'));
            $price = filter_var(
                $_POST['price'] ?? 0,
                FILTER_SANITIZE_NUMBER_FLOAT,
                FILTER_FLAG_ALLOW_FRACTION
            );
            $capacity = isset($_POST['capacity']) && $_POST['capacity'] !== '' ? (int) $_POST['capacity'] : null;

            if (empty($title) || strlen($title) > 255) {
                throw new Exception('Event title must be between 1-255 characters');
            }

            if (empty($date) || !strtotime($date)) {
                throw new Exception('Invalid event date');
            }

            if (empty($venue) || strlen($venue) > 255) {
                throw new Exception('Venue must be between 1-255 characters');
            }

            if ($price <= 0) {
                throw new Exception('Price must be greater than 0');
            }

            if ($capacity !== null && $capacity < 1) {
                throw new Exception('Capacity must be at least 1 if specified');
            }

            $eventModel = $this->model('Event');
            $data = [
                'user_id' => (int) $_SESSION['user_id'],
                'title' => $title,
                'description' => $description ?: null,
                'event_date' => date('Y-m-d H:i:s', strtotime($date)),
                'venue' => $venue,
                'price' => $price,
                'capacity' => $capacity,
                'status' => 'published',
            ];

            if (!$eventModel->create($data)) {
                throw new Exception('Failed to create event');
            }

            $_SESSION['success_message'] = 'Event created successfully!';
            redirect('events');
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->view('events/create', [
                'error' => $e->getMessage(),
                'old_input' => $_POST,
            ]);
        }
    }

    public function editView($id) {
        $this->ensureAdmin();

        $id = is_numeric($id) ? (int) $id : 0;
        if ($id <= 0) {
            $this->notFound();
            return;
        }

        $eventModel = $this->model('Event');
        $event = $eventModel->getById($id);
        if (!$event) {
            $this->notFound();
            return;
        }

        $this->view('events/edit', [
            'event' => $event
        ]);
    }

    public function edit($id) {
        $this->ensureAdmin();

        try {
            $id = is_numeric($id) ? (int) $id : 0;
            if ($id <= 0) {
                throw new Exception('Invalid Event ID');
            }

            if (
                !isset($_POST['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'] ?? '', (string) $_POST['csrf_token'])
            ) {
                throw new Exception('Invalid CSRF token');
            }

            $title = trim(htmlspecialchars($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8'));
            $description = trim(htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8'));
            $date = $_POST['date'] ?? '';
            $venue = trim(htmlspecialchars($_POST['venue'] ?? '', ENT_QUOTES, 'UTF-8'));
            $price = filter_var($_POST['price'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $capacity = isset($_POST['capacity']) && $_POST['capacity'] !== '' ? (int) $_POST['capacity'] : null;
            $status = $_POST['status'] ?? 'published';

            if (empty($title) || strlen($title) > 255) {
                throw new Exception('Event title must be between 1-255 characters');
            }

            if (empty($date) || !strtotime($date)) {
                throw new Exception('Invalid event date');
            }

            if (empty($venue) || strlen($venue) > 255) {
                throw new Exception('Venue must be between 1-255 characters');
            }

            if ($price <= 0) {
                throw new Exception('Price must be greater than 0');
            }

            $eventModel = $this->model('Event');
            $data = [
                'title' => $title,
                'description' => $description ?: null,
                'event_date' => date('Y-m-d H:i:s', strtotime($date)),
                'venue' => $venue,
                'price' => $price,
                'capacity' => $capacity,
                'status' => $status,
            ];

            if (!$eventModel->updateEvent($id, $data)) {
                throw new Exception('Failed to update event');
            }

            $_SESSION['success_message'] = 'Event updated successfully!';
            redirect('events');
        } catch (Exception $e) {
            error_log("Edit Error: " . $e->getMessage());
            $eventModel = $this->model('Event');
            $event = $eventModel->getById((int) $id) ?? [];
            $this->view('events/edit', [
                'error' => $e->getMessage(),
                'event' => array_merge($event, $_POST),
            ]);
        }
    }

    public function viewEvent($id) {
        $this->ensureAuthenticated();

        $id = is_numeric($id) ? (int) $id : 0;

        if ($id <= 0) {
            $this->notFound();
            return;
        }

        $eventModel = $this->model('Event');
        $event = $eventModel->getById($id);

        if (!$event) {
            $this->notFound();
            return;
        }

        $event['booked_tickets'] = $eventModel->getBookedTicketCount($id);

        $this->view('events/view', ['event' => $event]);
    }

    public function delete($id) {
        $this->ensureAdmin();

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Invalid request method. Deletion must be via POST.");
            }

            if (
                !isset($_POST['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'] ?? '', (string) $_POST['csrf_token'])
            ) {
                throw new Exception('Invalid CSRF token');
            }

            $id = is_numeric($id) ? (int) $id : 0;

            if ($id <= 0) {
                throw new Exception("Invalid event ID");
            }

            $eventModel = $this->model('Event');
            $event = $eventModel->getById($id);
            if (!$event) {
                throw new Exception("Event not found");
            }

            if ($eventModel->delete($id)) {
                $_SESSION['success_message'] = 'Event deleted successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to delete event.';
            }

            redirect('events');
        } catch (Exception $e) {
            error_log("Delete Error: " . $e->getMessage());
            $_SESSION['error_message'] = $e->getMessage();
            redirect('events');
        }
    }

    private function ensureAuthenticated() {
        $this->ensureSession();

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? url('events');
            redirect('auth/login');
        }
    }

    private function ensureAdmin() {
        $this->ensureAuthenticated();

        $userRole = $_SESSION['user_role'] ?? null;
        if ($userRole !== 'admin') {
            $_SESSION['error_message'] = 'Only administrators can manage events.';
            redirect('events');
        }
    }

    private function ensureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
