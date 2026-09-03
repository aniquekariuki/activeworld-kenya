# Active World Kenya - Event Management System

**Live Website:** https://activeworld.freedev.app/

---

## Table of Contents

1. [Project Overview](#project-overview)
2. [Purpose of the System](#purpose-of-the-system)
3. [Key Features](#key-features)
4. [Technologies Used](#technologies-used)
5. [Project Structure](#project-structure)
6. [Database Structure](#database-structure)
7. [Installation Guide](#installation-guide)
8. [How to Use the System](#how-to-use-the-system)
9. [Admin Dashboard Guide](#admin-dashboard-guide)
10. [M-Pesa Payment Integration](#mpesa-payment-integration)
11. [Email Notifications](#email-notifications)
12. [Deployment Information](#deployment-information)
13. [Troubleshooting Common Issues](#troubleshooting-common-issues)
14. [Live Demo Credentials](#live-demo-credentials)
15. [Contact Information](#contact-information)
16. [License](#license)
17. [About the Developer](#about-the-developer)

---

## Project Overview

Active World Kenya is a complete event management system built for an event planning company based in Nairobi, Kenya. The system allows customers to request event quotes, make secure M-Pesa payments, leave reviews, and view the event gallery. It also includes a powerful admin dashboard for managing all event requests, tracking payments, and handling customer communications.

This project was built to solve a real-world problem: the difficulty customers face in finding reliable event planners, getting clear quotes, making secure payments, and tracking their event status. The system centralizes all these processes into one easy-to-use platform.

---

## Purpose of the System

The main purpose of this system is to streamline the event planning process for both customers and the business. Here is what the system achieves:

**For Customers:**

- Provides a simple way to request event quotes online
- Allows secure M-Pesa payments without visiting the office
- Enables customers to leave reviews and share their experiences
- Gives customers a professional platform to browse services and gallery

**For the Business (Admin):**

- Centralizes all event requests in one dashboard
- Tracks payment status for each event (No Payment, Deposit Paid, Partial Paid, Fully Paid)
- Allows quick status updates for events (Pending, Approved, Completed)
- Enables searching and filtering of requests
- Provides export functionality for data analysis
- Sends automated email notifications to customers on approval

---

## Key Features

### Customer Features

**Event Request Form**
Customers can submit detailed event requests including their full name, phone number, email address, event type, event date, expected number of guests, venue location, and a message describing their event vision. All fields are validated to ensure data quality.

**M-Pesa Payment Integration**
Customers can securely pay event deposits using M-Pesa STK Push. The system sends a payment request to the customer's phone, and they confirm using their M-Pesa PIN. Payments are logged in the database for tracking.

**Customer Reviews**
Customers can leave reviews about their experience with the company. Reviews are submitted and await admin approval before being published on the website. This builds trust and social proof for the business.

**Event Gallery**
The website includes a gallery section where the business can showcase photos from past events. This helps customers visualize what the company can deliver.

**Mobile Responsive Design**
The entire website is fully responsive and works seamlessly on all devices including desktop computers, tablets, and mobile phones.

### Admin Features

**Secure Admin Login**
The admin section is protected by a secure login system. Only authorized users can access the dashboard.

**Dashboard with Statistics**
The admin dashboard displays key statistics including total requests, pending requests, approved requests, and completed requests. This gives the admin a quick overview of business activity.

**Payment Tracking**
Admins can track payment status for each event. The system allows four payment statuses: No Payment, Deposit Paid, Partial Paid, and Fully Paid. Admins can also record the amount paid, total amount, payment method, and transaction ID.

**Search and Filter**
Admins can search for specific requests by name or email. They can also filter requests by status and payment status.

**Export to Excel**
Admins can export all event requests to a CSV file for analysis in Excel or other spreadsheet software.

**Edit and Delete Requests**
Admins have full control to edit customer details or delete requests if needed.

**Review Management**
Admins can approve or delete customer reviews before they appear on the website.

---

## Technologies Used

| Technology | Version | Purpose |
|------------|---------|---------|
| HTML5 | - | Website structure and content |
| CSS3 | - | Styling and responsive design |
| JavaScript | - | Interactive elements and API calls |
| PHP | 8.2.12 | Backend logic and API endpoints |
| MySQL | 8.0 | Database storage |
| M-Pesa Daraja API | - | Payment processing |
| InfinityFree | - | Free hosting provider |
| Apache | 2.4.58 | Web server |
| XAMPP | - | Local development environment |

---

## Project Structure

activeworld-kenya/
│
├── admin/
│ ├── login.php # Admin login page
│ ├── dashboard.php # Main admin dashboard
│ ├── logout.php # Logout handler
│ ├── manage-reviews.php # Review management page
│ └── email-log.php # Email log viewer
│
├── api/
│ ├── config-sample.php # Database configuration template
│ ├── submit-event.php # Event request submission handler
│ ├── mpesa-config-sample.php # M-Pesa configuration template
│ ├── mpesa-stk.php # M-Pesa STK Push request handler
│ ├── mpesa-callback.php # M-Pesa callback handler
│ ├── update-payment.php # Payment status update handler
│ ├── get-events-api.php # API endpoint for retrieving events
│ └── send-email.php # Email sending function
│
├── images/ # Static images for the website
├── customer/ # Customer-facing pages (if applicable)
├── email_backups/ # Saved email backups
├── email_logs/ # Email transaction logs
│
├── index.html # Homepage
├── about.html # About Us page
├── services.html # Services page
├── contact.html # Contact form with M-Pesa
├── reviews.php # Customer reviews page
├── gallery.php # Event gallery page
├── style.css # Main stylesheet
└── script.js # Main JavaScript


---

## Database Structure

The system uses a MySQL database with the following main tables:

**admin_users**
Stores admin login credentials including username, hashed password, and email.

**event_requests**
The main table storing all customer event requests. Includes fields for customer details, event information, status, payment tracking, and timestamps.

**mpesa_transactions**
Stores all M-Pesa payment transactions including transaction ID, phone number, amount, receipt number, and status.

**reviews**
Stores customer reviews including name, email, rating, review text, and approval status.

**event_images**
Stores event gallery images with titles, categories, and image paths.

**notifications**
Logs all notifications sent to customers (email, SMS, push).

**subscribers**
Stores newsletter subscribers (optional feature).

---

## How to Use the System

### As a Customer

**Request a Quote:**
1. Navigate to the "Contact Us" page
2. Fill in all required fields:
   - Full Name (first and last)
   - Phone Number (12 digits starting with 254)
   - Email Address
   - Event Type
   - Event Date (future date)
   - Expected Guests (optional)
   - Venue (optional)
   - Message (optional)
3. Click "Send Request"
4. You will receive a confirmation message with a reference number

**Make an M-Pesa Payment:**
1. Navigate to the "Contact Us" page
2. Scroll to the "Pay with M-Pesa" section
3. Enter your M-Pesa number (12 digits starting with 254)
4. Enter the amount you wish to pay
5. Click "Pay Now"
6. Check your phone for the M-Pesa prompt
7. Enter your PIN to confirm the payment

**Leave a Review:**
1. Navigate to the "Reviews" page
2. Fill in your name, email, and event type
3. Select a rating (1-5 stars)
4. Write your review
5. Click "Submit Review"
6. Your review will appear after admin approval

### As an Admin

**Log In:**
1. Go to: https://activeworld.freedev.app/admin/login.php
2. Enter username and password
3. Click "Login"

**View Event Requests:**
- All requests are displayed in the dashboard table
- You can see important details at a glance

**Update Event Status:**
1. Find the request in the dashboard
2. Use the dropdown in the "Status" column
3. Select: Pending, Approved, or Completed
4. The status updates automatically

**Track Payments:**
1. Click the "Pay" button on any request
2. Select payment status: No Payment, Deposit Paid, Partial Paid, or Fully Paid
3. Enter the amount paid and total amount
4. Select payment method
5. Enter transaction ID (optional)
6. Click "Update Payment"

**Search and Filter:**
- Use the search box to find requests by name or email
- Use the status filter to view specific statuses
- Use the payment filter to view specific payment statuses

**Export Data:**
- Click the "Export to Excel" button
- A CSV file will download
- Open in Excel for analysis

**Manage Reviews:**
1. Click "Manage Reviews" in the dashboard
2. Pending reviews will appear
3. Click "Approve" to publish
4. Click "Delete" to remove

---

## Admin Dashboard Guide

The admin dashboard is the central control panel for managing the entire system. Here is a detailed guide to each section:

### Statistics Cards
The top of the dashboard displays four statistic cards:
- **Total Requests:** Total number of event requests
- **Pending:** Requests awaiting action
- **Approved:** Approved requests
- **Completed:** Completed events

### Payment Totals
Three cards display payment summaries:
- **Total Deposits:** Sum of all deposit payments
- **Total Partial Payments:** Sum of all partial payments
- **Total Fully Paid:** Sum of all fully paid events

### Filter Section
The filter bar allows you to:
- Search by name or email
- Filter by event status
- Filter by payment status

### Table Columns
The main table shows:
- **ID:** Unique request identifier
- **Date:** Submission date
- **Name:** Customer name
- **Email:** Customer email
- **Phone:** Customer phone number
- **Event Type:** Type of event requested
- **Event Date:** Proposed event date
- **Venue:** Proposed venue
- **Status:** Current event status (dropdown)
- **Payment Status:** Payment status with amount details
- **Actions:** Edit, Pay, and Delete buttons

### Actions
- **Edit:** Edit customer details
- **Pay:** Update payment information
- **Delete:** Remove the request

---

## M-Pesa Payment Integration

### How It Works

The M-Pesa integration uses Safaricom's Daraja API to send STK Push payment requests to customers' phones. The process works as follows:

1. Customer enters their M-Pesa number and amount on the contact page
2. The system sends a request to Safaricom's API
3. Safaricom sends a pop-up notification to the customer's phone
4. Customer enters their PIN to authorize the payment
5. Safaricom confirms the payment and sends a callback to the system
6. The system updates the transaction status in the database

### Sandbox Mode

The system is currently configured for Sandbox mode, which means:
- No real money is transferred
- Payments are simulated for testing
- Use test phone numbers provided by Safaricom

### Going Live

To process real payments, you need to:
1. Register for a Paybill or Till number with Safaricom
2. Apply for production access in the Daraja portal
3. Replace sandbox credentials with live credentials
4. Update the callback URL to your live domain

---

## Email Notifications

The system automatically sends email notifications when an admin approves an event request. The email includes:
- Event approval confirmation
- Event details summary
- Company contact information

### Email Backup

All emails sent through the system are saved as backups in the `email_backups/` folder. This ensures that no communication is lost.

### Email Configuration

To enable real email sending:
1. Configure SMTP settings in your PHP configuration
2. Or use PHPMailer library with Gmail SMTP
3. Or use your hosting provider's mail function

---

## Deployment Information

### Current Deployment

The system is currently deployed on InfinityFree at:
- Live URL: https://activeworld.freedev.app/
- Admin Login: https://activeworld.freedev.app/admin/login.php

### Deployment Checklist

When deploying to a new server, ensure you:
1. Upload all files to the web server
2. Create a MySQL database
3. Import the database structure
4. Update database credentials in config.php
5. Update M-Pesa callback URL in mpesa-config.php
6. Change the default admin password
7. Test all functionality
8. Enable SSL for security

### Hosting Recommendations

| Host | Cost | Features |
|------|------|----------|
| Hostinger | ~KES 500/mo | PHP/MySQL, Email, SSL, 24/7 Support |
| InfinityFree | Free | PHP/MySQL, SSL (current) |
| 000webhost | Free | PHP/MySQL, Ads displayed |

---

## Troubleshooting Common Issues

### Issue: "Could not connect to server"

**Cause:** The API path in contact.html is incorrect.

**Solution:**
1. Open `contact.html`
2. Find the `apiBase` variable (around line 350)
3. Ensure it points to the correct URL:
```javascript
const apiBase = 'https://yourdomain.com/api/';
