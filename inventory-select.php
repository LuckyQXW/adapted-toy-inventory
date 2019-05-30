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
      $rows->setFetchMode(PDO::FETCH_ASSOC);
      foreach($rows as $row) {
        array_push($output, $row);
      }
      return $output;
    } catch (PDOException $ex) {
      handle_db_error("Can not query the database. Please check your parameters.");
    }
  }

  /**
   * Constructs a query based on the parameters
   * @return [string] - the query based on the GET parameters passed in
   */
  function get_query() {
    $query = "SELECT item, function, available, image FROM inventory";
    if (isset($_GET["type"]) || isset($_GET["search"])) {
      $filter_query = " WHERE ";
      $filters = array();
      get_filter_query($filters);
      get_search_query($filters);
      $filter_query .= implode(" AND ", $filters);
      $query .= $filter_query;
    }
    $query .= get_sort_query();
    $query .= ";";
    return $query;
  }

  /**
   * Constructs a fragment of the query based on type parameter
   * @param  [array] $filters - the filters array containing a list of filter queries
   */
  function get_filter_query(&$filters) {
    if (isset($_GET["type"])) {
      $types = explode(",", $_GET["type"]);
      foreach ($types as $type) {
        if ($type !== "Available") {
          array_push($filters, "function LIKE '%{$type}%'");
        } else {
          array_push($filters, "available > 0");
        }
      }
    }
  }

  /**
   * Constructs a fragment of the query based on search parameter
   * @param [array] $filters - the filters array containing a list of filter queries
   */
  function get_search_query(&$filters) {
    if (isset($_GET["search"])) {
      $search = strtoupper($_GET["search"]);
      array_push($filters, "upper(item) LIKE '%{$search}%'");
    }
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
