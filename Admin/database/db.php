<?php

$conn = new mysqli('localhost', 'root', '', 'pharmacy');

if ($conn->connect_error) {
    die ("Connection Failed: " . $conn->connect_error);
}

?>