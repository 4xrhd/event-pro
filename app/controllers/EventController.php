<?php
class EventController extends Controller {
    public function index() {
        $eventModel = $this->model('Event');
        $events = $eventModel->getAll();
        $this->view('events/index', ['events' => $events]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $date = $_POST['date'];
            $venue = $_POST['venue'];
            $price = $_POST['price'];

            $eventModel = $this->model('Event');
            $eventModel->create($title, $date, $venue, $price);
            header('Location: /event/index');
            exit;
        }
        $this->view('events/create');
    }
}
