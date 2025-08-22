# Jendra Tours

Jendra Tours is a web-based application designed for managing tour and travel bookings, likely for a shuttle or bus service. It provides a comprehensive platform for administrators to manage operations, owners to monitor business performance, and customers to book tickets seamlessly.

## Features

The application is divided into three main roles, each with its own set of features:

### Admin
- **Dashboard:** An overview of the application's activity.
- **Customer Management:** Full CRUD (Create, Read, Update, Delete) functionality for customer data.
- **Vehicle Management:** Manage the fleet of vehicles (`mobil`).
- **Schedule Management:** Create and manage travel schedules (`jadwal`).
- **Booking Management:** View and manage all customer bookings (`pemesanan`).
- **Passenger Management:** Manage passenger information for each booking.
- **Printables:** Generate and print receipts (`nota`) and travel permits (`surat jalan`).

### Pemilik (Owner)
- **Dashboard:** A dedicated dashboard to view key business metrics.
- **Transaction Reports:** Access and view detailed reports of all transactions (`laporan transaksi`).

### Pelanggan (Customer)
- **Authentication:** Secure registration and login functionality.
- **Profile Management:** View and update personal profile information.
- **Search & Book:** Search for available schedules and book tickets.
- **Booking History:** View a history of all past and upcoming trips (`riwayat`).
- **Payment:** A seamless payment process integrated with Midtrans.
- **E-Ticket:** View and manage booked tickets.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2
- **Frontend:** Blade, Vite.js
- **Database:** MySQL (or other Laravel-supported database)
- **Payment Gateway:** [Midtrans](https://midtrans.com/)

## Installation

To get the project up and running on your local machine, follow these steps:

1.  **Clone the repository:**
    ```bash
    git clone <your-repository-url>
    cd <project-directory>
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Install JavaScript dependencies:**
    ```bash
    npm install
    ```

4.  **Create your environment file:**
    ```bash
    cp .env.example .env
    ```

5.  **Generate an application key:**
    ```bash
    php artisan key:generate
    ```

6.  **Configure your `.env` file:**
    Open the `.env` file and set up your database credentials (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`). You will also need to add your Midtrans API keys:
    ```
    MIDTRANS_MERCHANT_ID=your_merchant_id
    MIDTRANS_CLIENT_KEY=your_client_key
    MIDTRANS_SERVER_KEY=your_server_key
    ```

7.  **Run the database migrations:**
    ```bash
    php artisan migrate
    ```

8.  **Seed the database with initial users:**
    This will create the default admin and owner accounts.
    ```bash
    php artisan db:seed --class=UsersTableSeeder
    ```

9.  **Start the development servers:**
    Open two terminal windows and run the following commands:
    ```bash
    # Terminal 1: Start the Laravel server
    php artisan serve

    # Terminal 2: Start the Vite development server
    npm run dev
    ```

## Usage

Once the application is running, you can log in with the default credentials created by the seeder.

-   **Admin Login:**
    -   **Login Page:** `/login`
    -   **Username (No. HP):** `081234567890`
    -   **Password:** `admin123`

-   **Owner Login:**
    -   **Login Page:** `/login`
    -   **Username (No. HP):** `081234567891`
    -   **Password:** `pemilik123`

-   **Customer Access:**
    -   Customers can register for a new account at `/register`.
    -   The main landing page is at the root URL (`/`).

## About the Developer

This project was developed by a passionate developer. You can view their profile [here](/developer).
