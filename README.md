# EventPro - Advanced Online Event Ticketing & Management System

![EventPro Banner](public/event-pro.png)

## Overview

EventPro is a **full-stack web application** designed to streamline event ticket sales, attendee management, and check-in processes. It enables event organizers to **create, manage, and sell tickets** while providing customers with a seamless booking experience, built using a robust custom PHP MVC architecture.

## Documentation

Comprehensive project documentation is available in the [`docs/`](docs/) directory:
- [Architectural Overview](docs/architecture.md)
- [API & Routing Documentation](docs/api.md)
- [Deployment Guide](docs/deployment.md)
- [Developer Guide](docs/development.md)

## Key Features

✔ **User Roles & Authentication**  
- Admin / Organizer (Event creation, analytics)  
- Customer (Ticket booking & management)  
- Staff (QR code validation & check-in)  

✔ **Event Management**  
- Create and edit events with dates, venues, and capacity limits  
- Dynamic ticketing options  

✔ **Ticket Booking & QR Code Generation**  
- Instant reservation and secure ticket generation  
- PDF ticket generation and QR code integration via TCPDF and phpqrcode  

✔ **QR Check-In System**  
- Real-time ticket verification and attendee check-in scanner interface  

## Technology Stack

| Category         | Technology                     |
|------------------|--------------------------------|
| Frontend         | HTML5, CSS3, JavaScript (AJAX) |
| Backend          | PHP 8.1+ (Custom MVC)          |
| Database         | MySQL / MariaDB (via PDO)      |
| QR Generation    | phpqrcode Library              |
| PDF Generation   | TCPDF                          |
| Security         | Prepared Statements, Session-based Auth |

## Contributors

👨‍💻 **Backend Developer**: [Azhar Uddin](https://github.com/4xrhd)  
🎨 **UI/UX Designer**: [Sabikun Nahar Alina](https://github.com/sabikun-nahar-alina)  
🎨 **UI/UX Designer**: [Alin](https://github.com/Alin472)  

<br/>

<a href="https://github.com/4xrhd"><img src="https://avatars.githubusercontent.com/u/65583096?s=96&v=4" width="100"></a>
<a href="https://github.com/sabikun-nahar-alina"><img src="https://avatars.githubusercontent.com/u/188478942?s=96&v=4" width="100"></a>
<a href="https://github.com/Alin472"><img src="https://avatars.githubusercontent.com/u/185522545?s=96&v=4" width="100"></a>

## Getting Started

1. **Clone the repository**
   ```bash
   git clone https://github.com/4xrhd/event-pro.git
   cd event-pro
   ```

2. **Install Composer dependencies**
   ```bash
   composer install
   ```

3. **Set up the database**
   Import `database/schema.sql` into your MySQL/MariaDB database and configure connection settings in `config/database.php`.

4. **Run the local development server**
   ```bash
   php -S 127.0.0.1:8080 -t public
   ```

---

**EventPro** - Simplifying event management one ticket at a time!
