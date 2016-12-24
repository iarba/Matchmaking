<?php
  session_start();
  $GLOBALS["errmsg"] = "";
  include "libraries.php";
  $title = "";
  $content = "";
  if (isset($_POST["button"])) {
    if ($_POST["button"] == "Log in") {
      $data = filter_var($_POST["code"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
      $_SESSION["matchUserHash"] = sql_sanity($data);
    }
    if ($_POST["button"] == "Log out") {
      $_SESSION["matchUserHash"] = "";
    }
    if ($_POST["button"] == "Request match") {
      $data = filter_var($_POST["user1"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
      $n1 = sql_sanity($data);
      $data = filter_var($_POST["user2"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
      $n2 = sql_sanity($data);
      $u1 = new_user($n1);
      $u2 = new_user($n2);
      new_match($u1, $u2);
      $content = $content . "<h4>" ;
      $content = $content . "New match between " ;
      $content = $content . $n1;
      $content = $content . " and ";
      $content = $content . $n2;
      $content = $content . " created!";
      $content = $content . "</h4>";
      $content = $content . "<h4>" ;
      $content = $content . "Give the code " ;
      $content = $content . print_hash($u1);
      $content = $content . " to ";
      $content = $content . $n1;
      $content = $content . " and the code ";
      $content = $content . print_hash($u2);
      $content = $content . " to ";
      $content = $content . $n2;
      $content = $content . ". The codes will change once that user logs in. ";
      $content = $content . "Therefore, the other person will know that you accessed his account, ";
      $content = $content . "And will know to not proceed.";
      $content = $content . "</h4>";
      $content = $content . "<form action=\"match.php\" method=\"post\">";
      $content = $content . "<input type=\"submit\" name=\"button\" value=\"Got it!\">";
      $content = $content . "</form>";
    }
  }
  $user_hash = "";
  if (isset($_SESSION["matchUserHash"])) {
    $user_hash = $_SESSION["matchUserHash"];
  }
  if (($content == "") && (query_user_existance($user_hash))) {
    #setting up match
    $content = $content . "<br/><span>";
    if (need_update($user_hash)) {
      $user_hash = update_hash($user_hash);
      $_SESSION["matchUserHash"] = $user_hash;
      $content = $content . "<h4 style=\"color:red;\">";
      $content = $content . "You have logged in for the first time. ";
      $content = $content . "For security reason, your code has been changed. ";
      $content = $content . "This way, only you know it. The new code is ";
      $content = $content . print_hash($user_hash);
      $content = $content . "</h4>";
    } else {
      $content = $content . "Not the first login. ";
      $content = $content . "If you have had your code changed after logging in once, disregard this message. ";
      $content = $content . "Otherwise, ask the person that gave you the code if they accessed your account. ";
      $content = $content . "They might still be able to access it. <br/>";
    }
    $content = $content . "</span>";
    $db_match_content = match_query($user_hash);
    $db_user1_content = user_query($db_match_content["player1h"]);
    $db_user2_content = user_query($db_match_content["player2h"]);
    $title = "Match between " . $db_user1_content["name"] . " and " . $db_user2_content["name"];
    if ($user_hash == $db_user2_content["id"]) {
      $aux = $db_user2_content;
      $db_user2_content = $db_user1_content;
      $db_user1_content = $aux;
    }
    # map details
    $map_details = $db_match_content["mapdetails"];
    $map_decided = !empty($map_details);
    $can_choose_map = (!$map_decided) && ($db_user1_content["id"] == $db_match_content["player1h"]);
    if (isset($_POST["button"])) {
      if (($_POST["button"] == "Submit map") && ($can_choose_map)) {
        $data = filter_var($_POST["map_details"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        $map = sql_sanity($data);
        $map_details = $map;
        update_map($db_match_content["id"], $map);
        $map_decided = true;
      }
    }
    $content = $content . "<br/><span>";
    if ($map_decided) {
      $content = $content . "Details of the map: ";
      $content = $content . $map_details;
    } else {
      if ($can_choose_map) {
        $content = $content . "<form action=\"match.php\" method=\"post\">";
        $content = $content . "You must pick the map details: <input type=\"text\" name=\"map_details\">";
        $content = $content . "<br/>";
        $content = $content . "<input type=\"submit\" name=\"button\" value=\"Submit map\">";
      } else {
        $content = $content . "Map undecided.</span>";
      }
    }
    $content = $content . "</span>";
    # client's nation
    $client_details = $db_user1_content["nation"];
    $client_decided = !empty($client_details);
    $content = $content . "<br/><span>";
    if (isset($_POST["button"])) {
      if (($_POST["button"] == "Select nation") && (!$client_decided)) {
        $data = filter_var($_POST["nation"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        $nation = sql_sanity($data);
        $client_details = $nation;
        update_nation($db_user1_content["id"], $nation);
        $client_decided = true;
      }
    }
    if ($client_decided) {
      $content = $content . "Your pick: ";
      $content = $content . $client_details;
    } else {
      $content = $content . "<form action=\"match.php\" method=\"post\">";
      $content = $content . "Your nation: <input type=\"text\" name=\"nation\">";
      $content = $content . "<br/>";
      $content = $content . "<input type=\"submit\" name=\"button\" value=\"Select nation\">";
    }
    $content = $content . "</span>";
    # opponent's nation
    $opponent_details = $db_user2_content["nation"];
    $opponent_decided = !empty($opponent_details);
    $content = $content . "<br/><span>";
    if ($opponent_decided && $client_decided) {
      $content = $content . "Enemy pick: ";
      $content = $content . $opponent_details;
    } else {
      $content = $content . "Opponent pick is still secret.";
    }
    $content = $content . "</span>";
    # log out and refresh
    $content = $content . "<br/><form action=\"match.php\" method=\"post\">";
    $content = $content . "<input type=\"submit\" name=\"button\" value=\"Log out\">";
    $content = $content . "<input type=\"submit\" name=\"button\" value=\"Refresh\">";
    $content = $content . "</form>";
  } else {
    $GLOBALS["errmsg"] = "Code not found";
    if (!isset($_SESSION["matchUserHash"]) || $_SESSION["matchUserHash"] == "") {
      $GLOBALS["errmsg"] = "";
    }
    $_SESSION["matchUserHash"] = "";
    #setting up query page
    $_SESSION["matchUserHash"] = "";
    $title = "Match configuration Log in";
    $content = $content . "<h2>Log in</h2>";
    $content = $content . "<form action=\"match.php\" method=\"post\">";
    $content = $content . "Code: <input type=\"char\" name=\"code\">";
    $content = $content . "<br/>";
    $content = $content . "<input type=\"submit\" name=\"button\" value=\"Log in\">";
    $content = $content . "</form>";
    $content = $content . "<br/>";
    $content = $content . "<br/>";
    $content = $content . "<br/>";
    $content = $content . "<h2>create</h2>";
    $content = $content . "<form action=\"match.php\" method=\"post\">";
    $content = $content . "Player1: <input type=\"string\" name=\"user1\"> - chooses map";
    $content = $content . "<br/>";
    $content = $content . "Player2: <input type=\"string\" name=\"user2\">";
    $content = $content . "<br/>";
    $content = $content . "<input type=\"submit\" name=\"button\" value=\"Request match\">";
    $content = $content . "</form>";
  }
  
?>

<!DOCTYPE html>
<html>

  <head>
    <title><?php echo $title;?></title>
  </head>

  <body>
    <?php
      echo "<span style=\"color:red;\">" . $GLOBALS["errmsg"] . "</span>";
      echo $content;
    ?>
  </body>

</html>

