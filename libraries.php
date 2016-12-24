<?php

# hash manipulation

  function print_hash($hash){
    return "<input type=\"text\" value=\"" . $hash . "\"/>";
  }

# hash update

  function need_update($hash) {
    $info = user_query($hash);
    return $info["first_time"] == 1;
  }

  function update_hash($hash) {
    $id = $hash;
    do {
      $id = md5($id . rand());
    } while (query_user_existance($id));
    include "setdbconn.php";
    $sql = "UPDATE players SET id = '" . $id . "', first_time = 0 WHERE id = '" . $hash . "'";
    $conn -> query($sql);
    $sql = "UPDATE matches SET player1h = '" . $id . "' WHERE player1h = '" . $hash . "'";
    $conn -> query($sql);
    $sql = "UPDATE matches SET player2h = '" . $id . "' WHERE player2h = '" . $hash . "'";
    $conn -> query($sql);
    $conn -> close();
    return $id;
  }

# sql sanity

  function sql_sanity($string) {
    $result = $string;
    $result = str_replace("'", "", $result);
    $result = str_replace("\"", "", $result);
    $result = str_replace(";", "", $result);
    $result = str_replace("`", "", $result);
    $result = str_replace("\\", "", $result);
    $result = str_replace("--", "", $result);
    $result = str_replace("({", "", $result);
    $result = str_replace("/*", "", $result);
    return $result;
  }

# database queries

  function query_user_existance($hash) {
    include "setdbconn.php";
    $sql = "SELECT id FROM players WHERE id = '" . $hash . "'";
    $result = $conn -> query($sql);
    $conn -> close();
    return $result -> num_rows > 0;
  }

  function match_query($id) {
    include "setdbconn.php";
    $sql = "SELECT * FROM matches WHERE player1h = '" . $id . "' OR player2h = '" . $id . "'";
    $result = $conn -> query($sql);
    $conn -> close();
    return $result -> fetch_assoc();
  }

  function user_query($id) {
    include "setdbconn.php";
    $sql = "SELECT * FROM players WHERE id = '" . $id . "'";
    $result = $conn -> query($sql);
    $conn -> close();
    return $result -> fetch_assoc();
  }

# database creations

  function new_user($name) {
    $id = $name;
    do {
      $id = md5($id . rand());
    } while (query_user_existance($id));
    include "setdbconn.php";
    $sql = "INSERT INTO players(id, name, first_time) VALUES ('" . $id . "', '" . $name . "', 1)";
    $conn -> query($sql);
    $conn -> close();
    return $id;
  }

  function new_match($id1, $id2) {
    include "setdbconn.php";
    $sql = "INSERT INTO matches(player1h, player2h) VALUES ('" . $id1 . "', '" . $id2 . "')";
    $conn -> query($sql);
    $conn -> close();
  }

# database updates

  function update_map($id, $map) {
    include "setdbconn.php";
    $sql = "UPDATE matches SET mapdetails = '" . $map . "' WHERE id = " . $id;
    $conn -> query($sql);
    $conn -> close();
  }

  function update_nation($id, $nation) {
    include "setdbconn.php";
    $sql = "UPDATE players SET nation = '" . $nation . "' WHERE id = '" . $id . "'";
    $conn -> query($sql);
    $conn -> close();
  }
?>
