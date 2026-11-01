# SecureBank – Online Banking System

SecureBank is a web-based Online Banking System developed as a college mini project. It provides a user-friendly interface for accessing basic banking services such as account management, money transfers, transaction history, money requests, and loan management.

The project demonstrates frontend development, PHP backend programming, and MySQL database integration.

## Features

- User Registration and Login
- Banking Dashboard
- Account Balance Management
- Money Transfer
- Transaction History
- Money Requests
- Loan Management

## Technologies Used

| Technology | Purpose |
|---|---|
| HTML5 | Webpage structure |
| CSS3 | Styling and layout |
| JavaScript | Frontend functionality |
| PHP | Backend development |
| MySQL | Database management |
| Apache | Web server |
| XAMPP | Apache and PHP local environment |
| MySQL Workbench | Database management |

## Project Structure

```text
SecureBank2/
│
├── api/
│   └── PHP backend files
│
├── auth.html
├── dashboard.html
│
├── database/
│   └── online_banking.sql
│
└── README.md
```

## Requirements

- XAMPP (Apache and PHP)
- MySQL Server 8.0 or compatible version
- MySQL Workbench
- Modern web browser

**Note:** This project uses XAMPP for Apache and PHP. MySQL Server runs separately and is managed using MySQL Workbench. You do not need to start MySQL from the XAMPP Control Panel.

## Installation and Setup

### 1. Clone the Repository

```bash
git clone YOUR_GITHUB_REPOSITORY_URL
```

Alternatively, download the repository as a ZIP file.

### 2. Move the Project Folder

Place the `SecureBank2` folder inside:

```text
C:\xampp\htdocs\SecureBank2
```

### 3. Start Apache

1. Open XAMPP Control Panel.
2. Start Apache.
3. Make sure Apache is running successfully.

You do not need to start MySQL from XAMPP if you are using a separate MySQL Server installation.

### 4. Start MySQL Server

Make sure your separately installed MySQL Server service is running.

Open MySQL Workbench and connect to your MySQL Server.

### 5. Create the Database

In MySQL Workbench, create a schema named:

```sql
CREATE DATABASE online_banking;
```

### 6. Import the Database

1. Open `database/online_banking.sql` in MySQL Workbench.
2. Select `online_banking` as the default schema.
3. Execute the SQL script.
4. Verify that these four tables are created:

   - `users`
   - `transactions`
   - `money_requests`
   - `loans`

**Note:** The included SQL file contains the database structure only. It does not include existing user accounts or transaction records. Register a new account to create a user record.

### 7. Configure Database Connection

Open the PHP database connection file inside the `api` folder.

Configure the connection using your MySQL Server details:

- Host: `localhost`
- Port: `3306` (or your configured MySQL Server port)
- Database: `online_banking`
- Username: Your MySQL username
- Password: Your MySQL password

Make sure PHP connects to the same MySQL Server instance that you use in MySQL Workbench.

Do not upload private database credentials to GitHub.

### 8. Run the Project

Open your browser and visit:

```text
http://localhost/SecureBank2/auth.html
```

Register a new account or log in to access the application.

## Database Structure

The project uses a MySQL database named `online_banking`.

| Table | Description |
|---|---|
| users | Stores user account information |
| transactions | Stores credit and debit transactions |
| money_requests | Stores money request information |
| loans | Stores loan application information |

## Project Objective

The objective of SecureBank2 is to demonstrate the development of a basic online banking application using HTML, CSS, JavaScript, PHP, and MySQL.

It provides practical experience in frontend development, backend programming, database connectivity, and web application development.

## Future Enhancements

- Email or OTP-based verification
- Improved transaction security
- Better responsive design
- Secure online deployment

## Disclaimer

This project is developed for educational purposes only. It is not intended for real-world banking transactions or production use.

## Author

**Aryan More**

College Mini Project – Online Banking System