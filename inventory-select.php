<?php
  /*
    Name: Wen Qiu
    Date: May 30, 2019
    Section: CSE 154 AJ
    This file provides back-end support for the Inventory Filter API.
    Based on the input parameters supplied using GET/POST requests,
    the API outputs information on a list of toys from the inventory database.

    Web Service details:
    =====================================================================
    Required GET parameter:
    - mode
    Optional GET parameters:
    - type
    - search
    Output formats:
    - JSON
    Output Details:
    - If the mode parameter is passed and set to "toys", the API with return all
      toys from the inventory database as JSON.
    - If the mode parameter is passed and set to "toys", and the type parameter
      is passed in a comma-separated list of filter criterias, the API with return
      toys from the inventory database that matches all filter criteria as JSON.
    - If the code parameter is passed and set to "toys", and the search parameter
      is passed, the API will return toys from the inventory database that are
      case-insensitive matches of the search term in the item name as JSON.
    - If the query is invalid or the database connection fails, outputs a 503
      error as plain text.
    - If the mode parameter is not passed or not set to "toys", outputs 400 error
      message as plain text.
  */

  include 'common.php';

  $db = get_PDO();
  if (isset($db)) {
    if (isset($_GET["mode"]) && $_GET["mode"] === "toys") {
      $output = get_toys($db);
      if (isset($output)) {
        header("Content-Type: application/json");
        echo json_encode($output);
      }
    } else {
      print_errors("Please put in a valid mode parameter.");
    }
  }

  /**
   * Gets a list of toys that matches the filter criteria determined by either
   * the type parameter or search parameter
   * @param  [PDO] $db - the toy inventory database
   * @return [array] - toys in the inventory that matches the filter criteria,
   *                   includes the item, function, availability, and the image
   *                   link of the toy
   */
  function get_toys($db) {
    $output = null;
    try {
      if (isset($_GET["search"])) {
        $query = get_search_query();
      } else {
        $query = get_filter_query();
      }
      $rows = $db->query($query);
    } catch (PDOException $ex) {
      handle_db_error("Can not query the database. Please check your parameters.");
    }

    $output = array();
    foreach($rows as $row) {
      $result = array();
      $result["item"] = $row["item"];
      $result["function"] = $row["function"];
      $result["available"] = $row["available"];
      $result["image"] = $row["image"];
      array_push($output, $result);
    }
    return $output;
  }

  /**
   * Constructs a filter query based on the type parameter
   * @return [string] - the filter query based on the type parameter
   */
  function get_filter_query() {
    $query = "SELECT * FROM inventory";
    if (isset($_GET["type"])) {
      $query .= " WHERE ";
      $types = explode(",", $_GET["type"]);
      foreach ($types as $type) {
        if ($type !== "Available") {
          $query .= "function LIKE '%{$type}%' AND ";
        } else {
          $query .= "available > 0 AND ";
        }
      }
      $query = substr($query, 0, strlen($query) - 5);
    }
    $query .= ";";
    return $query;
  }

  /**
   * Constructs a filter query based on the search parameter
   * @return [string] - the filter query based on the search parameter
   */
  function get_search_query() {
    $search = strtoupper($_GET["search"]);
    $query = "SELECT * FROM inventory";
    $query .= " WHERE upper(item) LIKE '%{$search}%';";
    return $query;
  }
?>
