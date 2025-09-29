# ✨ printRoute ✨

Welcome to **printRoute**, a lightweight web platform designed to modernize the way users interact with local printing and Xerox shops. This project bridges the gap between customers and vendors, creating a seamless, queue-free printing experience.


---


## 🎯 Objective

The goal of printRoute is to provide a one-stop solution where users can locate nearby Xerox shops, upload their documents, customize printing and binding options, pay securely, and collect their prints without the usual hassle and waiting times. For shop owners, it serves as an efficient order management system and a tool to increase their customer reach.

---

## 🚀 Key Features

### 👤 For Users:
* **Shop Discovery:** Find nearby shops and view their live status (Idle, Busy, Closed).
* **Seamless Ordering:** Upload a PDF, and dynamically calculate the price based on a wide range of options:
    * Print Type (Color / Black & White)
    * Page Size (A4, A3, Legal)
    * Paper Thickness (GSM)
    * Binding (Staple, File, Spiral, Hardbound)
* **Wallet System:** Maintain a wallet to pay for orders, add balance, and withdraw funds.
* **Dynamic Order Placement:** The system intelligently checks your wallet balance to determine the payment flow.
* **Order Tracking:** View active and recent orders on a personal dashboard.
* **Cancel Pending Orders:** Cancel orders that have not yet been accepted by the shop and receive an instant refund to your wallet.

### 🏪 For Shop Owners:
* **Status Management:** Set your shop's status to Idle, Busy, or Closed to manage order flow.
* **Order Management Dashboard:** Receive new print orders in real-time.
* **Secure File Access:** Download customer-submitted PDFs securely.
* **Analytics (Conceptual):** View key metrics like pending orders and earnings.

### 👨‍💼 For Admin:
* **Shop Verification:** An admin role is designed to approve and manage shops before they appear on the platform.
* **System Oversight:** Monitor users, orders, and transactions.

---

## 🛠️ Technology Stack

This project is built with a classic and reliable stack, perfect for dynamic web applications.

* **Frontend:**
    * HTML5
    * CSS3 (with Bootstrap 5 for responsiveness)
    * JavaScript (for dynamic UI and client-side logic)
* **Backend:**
    * PHP 8.2
* **Database:**
    * MySQL

---

## 📁 Project Structure

printRoute/
├── backend/
│   ├── db_connect.php
│   ├── get_shops.php
│   ├── get_user_orders.php
│   ├── login.php
│   ├── logout.php
│   ├── register.php
│   ├── submit_order.php
│   └── ...
├── config/
│   └── config.php
├── database/
│   ├── schema.sql
│   └── seed.sql
├── frontend/
│   ├── assets/
│   │   ├── css/
│   │   └── images/
│   ├── add_balance.html
│   ├── login.php
│   ├── nav_user.php
│   ├── profile.php
│   ├── register.html
│   ├── select_shop.html
│   ├── shop_dashboard.php
│   ├── upload_pdf.html
│   ├── user_dashboard.php
│   ├── withdraw.html
│   └── index.html
└── uploads/
└── ... (for user documents and shop images)


---

## 💾 Database Schema

The database consists of four main tables to manage the application's data:
* **`users`**: Stores data for all roles (user, shop_owner, admin), including credentials and wallet balance.
* **`shops`**: Contains all details for each vendor, including their status and location.
* **`orders`**: Tracks every print job, linking a user to a shop and storing all order options.
* **`transactions`**: Logs all financial activities, including deposits and platform fees.

---

## ⚙️ Setup and Installation

Follow these steps to get the project running on your local machine using XAMPP on a Mac.

1.  **Start XAMPP:** Open the XAMPP application and start the **Apache** and **MySQL** services.
2.  **Place Files:** Copy the `printRoute` project folder into the XAMPP `htdocs` directory (`/Applications/XAMPP/xamppfiles/htdocs/`).
3.  **Create Database:**
    * Navigate to `http://localhost/phpmyadmin`.
    * Create a new database named `printroute_db`.
    * Select the new database and go to the **Import** tab.
    * Import the `database/schema.sql` file to create the tables.
4.  **Seed Database (Optional):**
    * To add 3 predefined shops, import the `database/seed.sql` file.
5.  **Run the Application:**
    * Open your browser and navigate to: `http://localhost/printRoute/frontend/`

---

## 🧪 How to Use

* **Register a User:** Go to the "Get Started" / "Sign Up" page to create a new user account.
* **Register a Shop:** On the registration page, select "I'm a Shop Owner" to create a shop.
    * **Note:** You must manually verify the new shop in the `shops` table in phpMyAdmin by changing `is_verified` from `0` to `1`.
* **Login as a Predefined Shop:**
    * **Email:** `sonal@printroute.com`
    * **Password:** `password123`

Enjoy exploring the printRoute application!

---

## 💡 Real-World Use Cases

printRoute is designed to solve common, everyday printing frustrations for a variety of users.

*   **For College Students:**
    *   A student has a final project report due in an hour. Instead of running to the campus library and waiting in a long queue, they can upload their PDF to printRoute, select a nearby shop that is `Idle`, and pay online. By the time they walk to the shop, their spiral-bound report is ready for collection.

*   **For Business Professionals:**
    *   An employee needs 50 copies of a training manual for a workshop. They can place the order from their office, choosing high-quality paper and file binding. The shop owner receives the order, prepares it, and the employee can pick it up on their way to the event without any downtime.

*   **For Job Seekers:**
    *   Someone urgently needs to print their resume for a walk-in interview. They can quickly find the closest print shop, send the document, and grab the printouts in minutes, ensuring they arrive at their interview on time and well-prepared.

---

## 🔮 Future Vision

printRoute is a platform with significant potential for growth. Here are some of the features and improvements we envision for the future:

*   **Integrated Delivery System:**
    *   Partner with local delivery services to allow users to have their printed documents delivered directly to their doorstep. This would add a new level of convenience, especially for large orders or users with tight schedules.

*   **Advanced Shop Analytics:**
    *   Provide shop owners with a powerful analytics dashboard to track their revenue, order history, and customer demographics. This will help them optimize their pricing, services, and business hours to maximize profitability.

*   **Mobile Applications:**
    *   Develop native iOS and Android apps to provide a more streamlined and accessible user experience. Mobile apps would enable features like push notifications for order status updates and location-based shop recommendations.

*   **Expanded Service-Offerings:**
    *   Allow shops to offer a wider range of services, such as merchandise printing (T-shirts, mugs), photo printing, and custom design services, turning printRoute into a comprehensive hub for all printing needs.
