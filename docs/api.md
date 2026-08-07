# EventPro API & Routing Documentation

EventPro implements a robust internal RESTful routing convention mapped directly to Controller actions.

## Authentication Endpoints
- `GET /auth/login` - Render login form
- `POST /auth/login` - Process user authentication
- `GET /auth/register` - Render registration form
- `POST /auth/register` - Process user creation
- `GET /auth/logout` - Destroy user session and redirect

## Event Endpoints
- `GET /events` - List all upcoming and published events
- `GET /events/create` - Render create event form (Organizer/Admin)
- `POST /events/create` - Store new event
- `GET /events/view?id={id}` - View specific event details
- `GET /events/edit?id={id}` - Render edit form
- `POST /events/edit?id={id}` - Update event details

## Ticketing & Check-in Endpoints
- `GET /tickets/book?event_id={id}` - Render ticket booking page
- `POST /tickets/book` - Process ticket reservation and generate QR
- `GET /tickets/my_tickets` - View authenticated user's tickets and downloads
- `GET /tickets/checkin` - Check-in scanner interface
- `POST /tickets/verify` - AJAX endpoint for verifying ticket QR codes
