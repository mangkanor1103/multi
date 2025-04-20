<?php
$conn = new mysqli("localhost", "root", "", "mangyan");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}?>