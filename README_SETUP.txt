LUXEVISTA RESORT BOOKING SYSTEM SETUP INSTRUCTIONS
=================================================

Thank you for using the LuxeVista Resort template with booking system integration.

SETUP INSTRUCTIONS:
-------------------

1. DATABASE CONFIGURATION:
   - Open 'includes/db_connect.php'
   - Update the database credentials:
     * DB_HOST: Your database host (usually 'localhost')
     * DB_USER: Your MySQL username
     * DB_PASS: Your MySQL password
     * DB_NAME: Database name (default is 'luxevista_resort')

2. DATABASE SETUP:
   - Method 1 (Automatic):
     * Visit 'install.php' in your browser
     * The system will automatically create the database and tables
   - Method 2 (Manual):
     * Create a MySQL database named 'luxevista_resort'
     * Execute the SQL statements in 'db_schema.sql'

3. VERIFICATION:
   - Visit 'diagnostics.php' to check if everything is configured correctly
   - Test the booking system by visiting 'book.php' or using the form on 'index.html'

TROUBLESHOOTING:
----------------

If the booking system is not working:

1. Check that PHP and MySQL are running on your server
2. Verify database credentials in 'includes/db_connect.php'
3. Ensure the 'luxevista_resort' database exists with all required tables
4. Check file permissions for PHP files (they should be readable)
5. Review error messages in 'diagnostics.php'

REQUIRED PHP EXTENSIONS:
------------------------

- PDO
- PDO MySQL

If these extensions are not available, enable them in your php.ini file.

SUPPORT:
--------

For additional help, please visit the diagnostics page or contact the template provider.

Note: This is a template system. For production use, you should:
- Implement proper security measures
- Add input validation and sanitization
- Implement proper error handling
- Add user authentication for admin functions