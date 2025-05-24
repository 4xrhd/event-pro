<?php
class EventController extends Controller {
    public function indexView() {
        try {
            $eventModel = $this->model('Event');
            $events = $eventModel->getAll();
            
            if (empty($events)) {
                $this->view('events/index', [
                    'events' => [],
                    'message' => 'No events found. Create your first event!'
                ]);
                return;
            }
            
            $this->view('events/index', ['events' => $events]);
        } catch (Exception $e) {
            error_log("EventController Error: " . $e->getMessage());
            $this->notFound();
        }
    }

    // Show create form (GET)
    public function createView() {
        $this->view('events/create');
    }

    // Handle form submission (POST)
    public function create() {
        try {
            $title = trim(htmlspecialchars($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8'));
            $date = $_POST['date'] ?? '';
            $venue = trim(htmlspecialchars($_POST['venue'] ?? '', ENT_QUOTES, 'UTF-8'));
            $price = filter_var($_POST['price'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    
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
    
            $formattedDate = date('Y-m-d H:i:s', strtotime($date));
    
            // ✅ FIX: Initialize the event model before calling create
            $eventModel = $this->model('Event');
    
            $data = [
                'title' => $title,
                'event_date' => $formattedDate,
                'venue' => $venue,
                'price' => $price
            ];
    
            if ($eventModel->create($data)) {
                $_SESSION['success_message'] = 'Event created successfully!';
                header('Location: /index.php?url=events');
                exit;
            } else {
                throw new Exception('Failed to create event');
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->view('events/create', [
                'error' => $e->getMessage(),
                'old_input' => $_POST
            ]);
        }
    }
    
    public function viewEvent($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /index.php?url=auth/login");
            exit();
        }
        
        try {
            // Convert to integer if it's a numeric string
            $id = is_numeric($id) ? (int)$id : 0;
            
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
            
            $this->view('events/view', ['event' => $event]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->notFound();
        }
    }
    public function delete($id) {
        try {
            $id = is_numeric($id) ? (int)$id : 0;
    
            if ($id <= 0) {
                throw new Exception("Invalid event ID");
            }
    
            $eventModel = $this->model('Event');
            if ($eventModel->delete($id)) {
                $_SESSION['success_message'] = 'Event deleted successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to delete event.';
            }
            header('Location: /index.php?url=events');
            exit;
        } catch (Exception $e) {
            error_log("Delete Error: " . $e->getMessage());
            $_SESSION['error_message'] = 'An error occurred while deleting the event.';
            header('Location: /index.php?url=events');
            exit;
        }
    }
    
}