# SimCompBot Documentation

## Overview
This project was developed to automate actions for the online game **SimCompanies**. It serves as a demonstration of programming skills, including working with databases, HTTP requests, and logic. **Note:** The use of this script violates the terms of service of SimCompanies and may result in account bans. It is intended solely for educational purposes.

---

## Key Features
1. **Company Management**:
   - Automated creation and management of companies.
   - Storage of company data (e.g., email, password, status) in a database.

2. **Game Interactions**:
   - **Building and Upgrading**: Automated creation and upgrading of buildings.
   - **Market Interactions**: Buying and selling resources.
   - **Sending Contracts**: Automated sending of contracts.
   - **Completing Achievements**: Automatically completing and claiming achievements.

3. **Proxy Support**:
   - Use of proxies for HTTP requests to obscure the origin of the requests.

4. **Optimization**:
   - Optimized to quickly generate resources like construction units and send them to a main account.

---

## Project Structure
### Main Files and Classes
- **`BotFather.php`**:
  - Main class that controls the workflow.
  - Manages companies and executes actions.
- **`Methods/makeCompany.php`**:
  - `makeCompany` class for creating new companies.
  - Generates random email addresses and passwords.
- **`Methods/Storage.php`**:
  - `Storage` class for database management.
  - Methods: `addCompany`, `deleteCompany`, `getCompanies`, `editCompany`.
- **`Methods/Company.php`**:
  - `Company` class for managing individual companies.
  - Executes actions like clearing warehouses, building structures, and buying resources.
- **`Communicators/TelegramBot.php`**:
  - `TelegramBot` class for communication via Telegram.
  - Methods: `sendMessage`, `quietMessage`.
- **`Communicators/Curler.php`**:
  - Base class for HTTP requests (GET, POST, DELETE).
- **`DataSets/allbuildings.json`**:
  - Contains data about buildings, including costs, production rates, and categories.

---

## Key Classes and Methods
### BotFather
- **`main($db_table_name)`**:
  - Main loop that manages companies and executes actions.
- **`checkCompanies($storage)`**:
  - Checks the status of companies and deletes completed ones.

### makeCompany
- **`make()`**:
  - Creates a new company and returns CSRF token, session ID, email, and password.

### Storage
- **`addCompany($csrf, $sessionid, $email, $password)`**:
  - Adds a new company to the database.
- **`deleteCompany($email)`**:
  - Deletes a company from the database.
- **`getCompanies()`**:
  - Retrieves all companies from the database.

### Company
- **`run($company)`**:
  - Executes actions for a company (e.g., clearing warehouses, building structures).
- **`clearwarehouse()`**:
  - Clears the warehouse of a company.
- **`contracts()`**:
  - Sends contracts.

### TelegramBot
- **`sendMessage($text)`**:
  - Sends a message via Telegram.
- **`quietMessage($text)`**:
  - Sends a silent message via Telegram.

---

## Database Structure
The database stores information about companies in a table. Example fields:
- `csrf`: CSRF token of the company.
- `sessionid`: Session ID of the company.
- `email`: Email address of the company.
- `password`: Password of the company.
- `status`: Current status of the company.
- `done`: Indicates whether the company is completed.

---

## Security and Legal Notices
- **Risk of Misuse**: The use of this script violates the terms of service of SimCompanies.
- **Proxy Usage**: Proxies are used to obscure HTTP requests.
- **Disclaimer**: The developer is not responsible for the misuse of this script.

---

## Requirements
- **PHP**: The project is written in PHP and requires a PHP runtime environment.
- **Database**: A MySQL database is used to store company data.
- **cURL**: The cURL library is used for HTTP requests.

---

## Installation and Usage
1. **Set up the Database**:
   - Create a MySQL database and table according to the requirements in `Storage.php`.
2. **Configuration**:
   - Ensure the database connection details in `Storage.php` are correct.
3. **Run**:
   - Execute `BotFather.php` to start the bot.

---

## Disclaimer
This project is for educational purposes only. The use of this script may result in account bans. The developer is not responsible for any damages caused by the use of this script.
