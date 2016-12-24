<?php
  $servername = "localhost";
  $username = "matches_query";
  $password = "queryer";
  $dbname = "matches";
  $conn = new mysqli($servername, $username, $password, $dbname);
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
?>
