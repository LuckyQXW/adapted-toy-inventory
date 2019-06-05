<?php
  /*
    Name: Wen Qiu
    Date: May 30, 2019
    Section: CSE 154 AJ
    This file provides back-end support for the Adapted Toy Inventory web services.
    Contains get_PDO function which establishes the connection to the Adapted Toy
    Inventory database. If the connection fails, output 503 error as plain text.
    Also contains other error handling functions.
  */

  /**
   * Returns a PDO object connected to the database. If a PDOException is thrown when
   * attempting to connect to the database, responds with a 503 Service Unavailable
   * error.
   * @return {PDO} connected to the database upon a succesful connection.
   */
  function get_PDO() {
    # Variables for connections to the database.
    $host = "localhost";
    $port = "8889";
    $user = "root";
    $password = "root";
    $dbname = "inventorydb";

    # Make a data source string that will be used in creating the PDO object
    $ds = "mysql:host={$host}:{$port};dbname={$dbname};charset=utf8";

    try {
      $db = new PDO($ds, $user, $password);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $db;
    } catch (PDOException $ex) {
      handle_db_error("Cannot connect to the database. Please try again later.");
    }
  }

/**
 * Copied from wpl-queue lecture example
 * Prints out a plain text 503 error message given $msg. If given a second (optional) argument as
 * an PDOException, prints details about the cause of the exception.
 * @param $msg {string} - Plain text 503 message to output
 * @param $ex {PDOException} - (optional) Exception object with additional exception details
 */
 function handle_db_error($msg, $ex=NULL) {
   process_error("HTTP/1.1 503 Service Unavailable", $msg, $ex);
 }

 /**
  * Copied from wpl-queue lecture example
  * Prints out a plain text error message given $msg after sending the given header.
  * If given a second (optional) argument as an Exception, prints details about the cause
  * of the exception.
  * @param $type {string} - The HTTP error header string.
  * @param $msg {string} - Plain text message to output.
  * @param $ex {Excpetoin} - (optional) Exception object with additional exception details.
  */
  function process_error($type, $msg, $ex=NULL) {
    header($type);
    header("Content-type: text/plain");
    if ($ex) {
      echo ("Error details: $ex \n");
    }
    die("{$msg}\n");
  }

  /**
   * Responds 400 error and returns a message in plain text
   * @param  [string] $msg - the message to be displayed as error
   */
  function print_errors($msg) {
    header("HTTP/1.1 400 Invalid Request");
    header("Content-type: text/plain");
    echo $msg;
  }
?>
