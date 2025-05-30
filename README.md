# EventPro - Advanced Online Event Ticketing & Management System

![EventPro Banner](public/event-pro.png)

## Overview

EventPro is a **full-stack web application** designed to streamline event ticket sales, attendee management, and check-in processes. It enables event organizers to **create, manage, and sell tickets** while providing customers with a seamless booking experience.

## Key Features

✔ **User Roles & Authentication**  
- Admin (Event creation, analytics)  
- Customer (Ticket booking)  
- Staff (QR validation)  

✔ **Event Management**  
- Create/edit events (title, date, venue, tickets)  
- Dynamic pricing (Early Bird/VIP tickets)  

✔ **Ticket Booking & Payments**  
- Stripe/PayPal integration  
- Email tickets (PDF + QR code)  

✔ **QR Check-In System**  
- Staff dashboard for real-time validation  

✔ **Reporting & Analytics**  
- Sales tracking, attendee reports  

## Technology Stack

| Category         | Technology                     |
|------------------|--------------------------------|
| Frontend         | HTML5, CSS3, JavaScript (AJAX) |
| Backend          | PHP (Custom MVC)               |
| Database         | MySQL                          |
| Payment Gateway  | Stripe API                     |
| QR Generation    | phpqrcode Library              |
| PDF Tickets      | TCPDF                          |
| Security         | CSRF Protection, Prepared Statements |

## Target Audience

🎯 **Event Organizers** (Conferences, Concerts, Workshops)  
🎯 **Customers** (Ticket Buyers)  
🎯 **Venue Staff** (Check-In Personnel)  

## Contributors

👨‍💻 **Backend Developer**: [ Azhar Uddin](https://github.com/4xrhd)  
🎨 **UI/UX Designer**: [Sabkun Nahar Alina](https://github.com/sabikun-nahar-alina) 
🎨 **UI/UX Designer**: [Alin](https://github.com/username)  
<br>
<a href="https://github.com/4xrhd"><img src="https://avatars.githubusercontent.com/u/65583096?s=96&v=4"> </a>
<a href="https://github.com/sabikun-nahar-alina"><img src="https://avatars.githubusercontent.com/u/188478942"></a>
<a href="https://github.com/Alin472"><img src="https://avatars.githubusercontent.com/u/185522545?v=4"> </a>

## Getting Started

1. Clone the repository
2. Set up the database (MySQL)
3. Configure the environment variables
4. Run the application on a PHP server
`$php -S 127.0.0.1:8080 -t public`
---

**EventPro** - Simplifying event management one ticket at a time!
