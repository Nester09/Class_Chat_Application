# Class Chat Application

A PHP and MySQL web-based chat application designed for students to communicate through class groups and private conversations. The application, named **Nexy Messenger**, supports group messaging, private messaging, user profiles, file sharing, message reactions, and account management.

## Features

* User registration and login
* User profile management
* Class/group creation and management
* Group messaging
* Private messaging between users
* File and image sharing
* Message reactions
* Message search
* Password reset functionality
* Account deletion
* Group member management
* Online/local chat interface
* Responsive interface for different screen sizes
* Emoji support in messages

## Technology Stack

### Frontend

* HTML5
* CSS3
* JavaScript
* Bootstrap 5.3.0
* jQuery
* Font Awesome
* EmojiOneArea

### Backend

* PHP
* MySQL/MariaDB

### Development Environment

* XAMPP
* Apache
* MySQL/MariaDB

## Project Structure

Class_Chat_Application/
│
├── images/
│   └── Application preview and interface images
│
├── plugins/
│   ├── bootstrap-4.5.2/
│   ├── bootstrap-5.3.0/
│   ├── emojionearea-3.4.1/
│   ├── Font-Awesome-6.4.0/
│   ├── jquery-3.6.0/
│   └── popperjs/
│
├── profile/
│   └── User profile-related files
│
├── styles/
│   └── Application CSS files
│
├── uploads/
│   └── User-uploaded files
│
├── chat.php
├── private_chat.php
├── chat-backend.php
├── group_management.php
├── register.php
├── login.php
├── logout.php
├── edit_profile.php
├── forgot_password.php
├── reset_password.php
├── delete_account.php
├── search_messages.php
├── check_new_messages.php
├── store_group_id.php
├── db.php
├── index.php
├── logged-out.php
├── chat_app_db.sql
├── .gitignore
├── PROJECT_STRUCTURE.md
└── README.md


## Requirements

Before running the application, make sure the following are installed:

* XAMPP
* Apache
* MySQL or MariaDB
* A modern web browser
* PHP (provided through XAMPP)

No internet connection is required for the application's main local functionality when the bundled frontend libraries are used.

## Installation

1. Install XAMPP on your computer.

2. Start **Apache** and **MySQL** from the XAMPP Control Panel.

3. Copy the project folder into the XAMPP `htdocs` directory.

   Example:

   `C:\xampp\htdocs\Class_Chat_Application`

4. Open phpMyAdmin in your browser.

5. Create a database for the application.

6. Import the provided:

   `chat_app_db.sql`

7. Open `db.php` and make sure the database connection details match your local MySQL configuration.

8. Open the application in your browser:

   `http://localhost/Class_Chat_Application/`

## Database Setup

The project includes a database export named:

`chat_app_db.sql`

The database contains tables used for:

* Users
* Groups
* Group membership
* Group messages
* Private messages
* Message reactions
* Password resets
* Cleared messages

To set up the database:

1. Open phpMyAdmin.
2. Create the application database.
3. Select the newly created database.
4. Choose **Import**.
5. Select `chat_app_db.sql`.
6. Execute the import.
7. Verify that the required tables have been created.
8. Confirm that the credentials in `db.php` correspond to the database configuration.

## Running the Application

After Apache and MySQL have been started:

1. Place the project inside the XAMPP `htdocs` directory.
2. Make sure the database has been imported.
3. Confirm the database configuration in `db.php`.
4. Open a browser.
5. Navigate to:

`http://localhost/Class_Chat_Application/`

6. Register a user account or use an existing test account.
7. Log in and access the available chat functionality.

## Default/Test Accounts

The database dump contains sample accounts for testing.

| Username       | Email                                   | Password         |
| -------------- | --------------------------------------- |------------------|
| MegaTron       | [megatron@gmail.com]                    | megatron         |
| ADMIN          | [admin@admin.com]                       | admin            |
| Vintage        | [vintage@gmail.com]                     | vintage          |
| BambleBee      | [bamblebee@gmail.com]                   | bamblebee        |
| Matrix         | [matrix@gmail.com]                      | matrix           |
| CyberTron      | [cybertron@gmail.com]                   | cybertron        |
| Ultron~Ultron  | [ultron@gmail.com]                      | ultronultron     |
| Meta           | [meta@gmail.com]                        | meta             |

> **Note:** You can create a new account through the registration page and test.

For a public repository, avoid publishing real passwords or other sensitive credentials.

## Known Limitations

* The application is primarily designed to run in a local XAMPP environment.
* It does not currently provide a production hosting configuration.
* User-uploaded files are excluded from the Git repository through `.gitignore`.
* The application does not currently include a dedicated mobile application.
* Some functionality may require additional configuration when deploying outside a local XAMPP environment.
* Email-based features such as password recovery may require a properly configured mail service when deployed on a different server.
* The application currently uses PHP/MySQL architecture rather than a separate REST API and frontend framework.

## License

This project is distributed under the license included in the repository.