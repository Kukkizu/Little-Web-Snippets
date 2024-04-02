<?php

// Set headers to allow cross-origin requests from a website
header('Access-Control-Allow-Origin: website.com');
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// Start a PHP session
session_start();

// Check if the form has been submitted
if(isset($_POST['submit'])){

    // Database connection parameters
    $hostname = 'website.com';
    $username = ''; // Database username
    $dbpassword = ''; // Database password
    $dbname = ''; // Database name

    // Establish a connection to MySQL database or terminate with an error message
    $conn = mysqli_connect($hostname,$username,$dbpassword,$dbname) or die("Couldn't connect to MySQL:" .mysqli_connect_error());

    // Check if email and password fields are not empty
    if(!isset($_POST['email']) && !isset($_POST['password'])){
        echo('xEmpty'); // Output error message
    }

    // Check if email and password fields are set
    if(isset($_POST['email']) && isset($_POST['password'])){
        // Sanitize user input to prevent SQL injection
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        // Hash the password using the default PHP hashing algorithm
        $hashbrown = password_hash($password,PASSWORD_DEFAULT);

        // Prepare SQL query to retrieve user information based on email
        $sql="SELECT * FROM users WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'";

        // Execute the SQL query
        $result = mysqli_query($conn,$sql);

        // Check if query was successful
        if($result){
            //echo 'Compare Success!!  ';
        }else{
            echo("xNF"); // Output error message if query fails
        }

        // Check if there is exactly one matching user
        if (mysqli_num_rows($result) == 1) {
            // Get the password hash from the database row
            $row = mysqli_fetch_assoc($result);
            $dbPassword = $row['passwordhash'];
            // Verify the password hash against the user input
            if(password_verify($password, $dbPassword)) {
                $displayName = $row['displayname'];
                $userId = $row['id'];

                // Concatenate user data
                $data = $displayName."|".$userId;
                $key = "MYKEY";

                // Encrypt user data using AES-256-CBC encryption
                //$iv = openssl_random_pseudo_bytes(16); // Generate a random initialization vector
                $encryptedData = openssl_encrypt($data, 'AES-256-CBC', $key, 0);
                $encryptedData = base64_encode($encryptedData);

                // Output encrypted data
                echo $encryptedData;

                
            }else{
                echo("xNF"); // Output error message if password verification fails
            }
        } else {
            echo("xNF"); // Output error message if there is no matching user
        }

    }

} else{
    echo('SERVER FAILED!'); // Output error message if server fails
}
?>
