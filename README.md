# php101
a small library I use to ease things
# php101

php101 is a web application that allows users to sign up and create accounts securely. It provides a user-friendly interface for managing user information and implements various security measures to protect user data.

## Features

- User Sign Up: Users can create new accounts by providing their username, email, phone number, and password.
- Password Strength Validation: The application enforces password strength requirements to ensure strong and secure passwords.
- Email Validation: The system validates the email address to ensure it is in the correct format.
- Phone Number Validation: The application verifies the phone number to ensure it is valid.
- User Data Encryption: User data is encrypted and stored securely using modern encryption techniques.
- JWT Authentication: The system uses JSON Web Tokens (JWT) for secure user authentication and authorization.
- Error Handling: The application implements robust error handling and logging to track and manage errors.
- Database Interaction: User data is stored in a secure database, and the application communicates with it using PDO for safe database operations.

## Installation

1. Clone the repository to your local machine.
2. Set up a web server with PHP and configure it to serve the project.
3. Create a MySQL database and import the provided SQL file to set up the necessary tables.
4. Update the `Dbh.conf.php` file with your database connection credentials.
5. Run the application on your web server.

## Usage

1. Access the web application through the provided URL.
2. Sign up for a new account by providing your username, email, phone number, and password.
3. The application will validate your data and create a new user account if all requirements are met.
4. If there are any errors during sign up, the system will provide appropriate error messages.
5. Once signed up, you can use your credentials to log in and access the user dashboard.

## Security

php101 takes security seriously and implements various measures to protect user data and ensure a secure user experience. User passwords are hashed before storage, and sensitive information is encrypted to prevent unauthorized access. JSON Web Tokens (JWT) are used for authentication to enhance security.

## Contributing

We welcome contributions to improve php101. If you find any issues or have ideas for enhancements, please feel free to submit a pull request or open an issue.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contact

For any questions or inquiries, please contact us at emuswalo7@gmail.com.

Thank you for using php101! We hope you find it useful and enjoy a secure user experience.
