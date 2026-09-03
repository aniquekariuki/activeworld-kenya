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

