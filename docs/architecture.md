# EventPro Architectural Overview

EventPro is a modern, lightweight, object-oriented PHP event management and ticketing platform built using a custom MVC (Model-View-Controller) architecture.

## 1. Architectural Pattern
- **MVC Architecture**: Strict separation of concerns across Models (`app/models/`), Views (`app/views/`), and Controllers (`app/controllers/`).
- **Front Controller**: All requests are routed through `public/index.php`, which boots the application core, initializes the router, and dispatches requests.
- **Custom Autoloader**: Implements PSR-4 compatible autoloading standards via `app/core/Autoloader.php`.

## 2. Core Components
- **Router (`app/core/Router.php`)**: Maps HTTP methods and request URIs to specific Controller actions with support for dynamic parameter matching.
- **Database Layer (`app/core/Model.php` & `config/database.php`)**: Utilizes PDO (PHP Data Objects) for secure, prepared-statement database interactions.
- **Authentication & Authorization**: Session-based auth ensuring role-based access control (Admin, Organizer, Attendee).

## 3. Key Features & Modules
- **Event Management**: Create, update, view, and manage events with capacity limits and scheduling.
- **Ticketing & Booking**: Reserve tickets, generate cryptographically-secure unique ticket IDs.
- **QR Code & PDF Integration**: Built-in PHPQRCode generation and TCPDF integration for printable tickets and QR check-ins.
- **Check-in System**: Real-time ticket verification and attendee check-in scanner interface.
