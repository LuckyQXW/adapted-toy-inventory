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
    - sort
    Output formats:
    - JSON
    Output Details:
    - If the mode parameter is passed and set to "toys", the API with return all
      toys from the inventory database as JSON.
    - If the mode parameter is passed and set to "toys", and the type parameter
      is passed in a comma-separated list of filter criterias, the API with return
      toys from the inventory database that matches all filter criteria as JSON.
    - If the mode parameter is passed and set to "toys", and the search parameter
      is passed, the API will return toys from the inventory database that are
      case-insensitive matches of the search term in the item name as JSON.
    - If the mode parameter is passed and set to "toys", and the sort parameter
      is passed as 0, the API will return toys from the inventory database ordering
      names in ascending order as JSON; if the sort parameter is passed as 1, the
      API will return toys from the inventory database ordering names in descending
      order as JSON.
    - If the query is invalid or the database connection fails, outputs a 503
      error as plain text.
    - Else outputs 400 error message as plain text.
  */

  include 'common.php';

  if (isset($_GET["mode"]) && $_GET["mode"] === "toys") {
    $output = get_toys();
    if (isset($output)) {
      header("Content-Type: application/json");
      echo json_encode($output);
    }
  } else {
    print_errors("Please put in a valid mode parameter.");
  }

  /**
   * Gets a list of toys that matches the filter criteria determined by either
   * the type parameter or search parameter
   * @param  [PDO] $db - the toy inventory database
   * @return [array] - toys in the inventory that matches the filter criteria,
   *                   includes the item, function, availability, and the image
   *                   link of the toy
   */
  function get_toys() {
    $db = get_PDO();
    $output = null;
    try {
      $query = get_query();
      $rows = $db->query($query);
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
    } catch (PDOException $ex) {
      handle_db_error("Can not query the database. Please check your parameters.");
    }
  }

  /**
   * Constructs a query based on the parameters
   * @return [string] - the query based on the parameters passed in
   */
  function get_query() {
    $query = "SELECT * FROM inventory";
    if (isset($_GET["type"]) || isset($_GET["search"])) {
      $query .= " WHERE ";
      $query .= get_filter_query();
      $query .= get_search_query();
      // Trim the last added AND
      $query = substr($query, 0, strlen($query) - 5);
    }
    $query .= get_sort_query();
    $query .= ";";
    return $query;
  }

  /**
   * Constructs a fragment of the query based on type parameter
   * @return [string] - a fragment of the query based on type parameter with a
   *                    trailing AND
   */
  function get_filter_query() {
    $query = "";
    if (isset($_GET["type"])) {
      $types = explode(",", $_GET["type"]);
      foreach ($types as $type) {
        if ($type !== "Available") {
          $query .= "function LIKE '%{$type}%' AND ";
        } else {
          $query .= "available > 0 AND ";
        }
      }
    }
    return $query;
  }

  /**
   * Constructs a fragment of the query based on search parameter
   * @return [string] - a fragment of the query based on search parameter with a
   *                    trailing AND
   */
  function get_search_query() {
    $query = "";
    if (isset($_GET["search"])) {
      $search = strtoupper($_GET["search"]);
      $query .= "upper(item) LIKE '%{$search}%' AND ";
    }
    return $query;
  }

  /**
   * Constructs a sort query fragment based on the sort parameter
   * @return [string] - the sort query fragment based on the sort parameter
   */
  function get_sort_query() {
    if (isset($_GET["sort"])) {
      if ($_GET["sort"] == 0) {
        return " ORDER BY item";
      } else if ($_GET["sort"] == 1) {
        return " ORDER BY item DESC";
      } else {
        print_errors("Invalid sort value");
      }
    }
    return "";
  }
?>
