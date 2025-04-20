<?php
// get_translation.php

// Database connection
$servername = "localhost";  // Change to your database server
$username = "root";         // Change to your database username
$password = "";             // Change to your database password
$dbname = "mangyan";     // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the word from the POST request
if (isset($_POST['word'])) {
    $word = $_POST['word'];

    // Function to get translation from the database
    function getTranslation($word, $conn) {
        $word = $conn->real_escape_string($word);
        $sql = "SELECT english, tagalog FROM translation WHERE word = '$word' LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Fetch the translation data
            $row = $result->fetch_assoc();
            return [
                'english' => $row['english'],
                'tagalog' => $row['tagalog']
            ];
        } else {
            return null; // No translation found
        }
    }

    // Fetch the translation for the given word
    $translation = getTranslation($word, $conn);

    // Return the translation as a JSON response
    if ($translation) {
        echo json_encode($translation);
    } else {
        echo json_encode(null);
    }
}

$conn->close();
?>
