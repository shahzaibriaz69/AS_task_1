<?php
$host = "localhost";
$dbname = "anomozco_dev_shahzaib_riaz-JAR";
$username = "anomozco_dev_shahzaib_riaz-JAR";
$password = "passdb_shahzaib_riaz-JAR_8B6EJL45";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>