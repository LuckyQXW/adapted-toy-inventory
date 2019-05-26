/**
 * Name: Wen Qiu
 * Date: May 30, 2019
 * Section: CSE 154 AJ
 * This is the JavaScript for the Adapted Toy Inventory website handling user
 * interactions.
 */
(function() {
  "use strict";
  const SELECT_API = "inventory-select.php?mode=toys";
  let sortQuery = "";
  let typeQuery = "";
  let searchQuery = "";
  window.addEventListener("load", init);

  /**
   * Loads all books in the beginning and attaches actions to buttons
   */
  function init() {
    loadAllToys();
    let typeBoxes = qsa(".toy-type");
    for (let i = 0; i < typeBoxes.length; i++) {
      typeBoxes[i].addEventListener("change", updateType);
    }
    let sortOptions = qsa(".sort");
    for (let i = 0; i < sortOptions.length; i++) {
      sortOptions[i].addEventListener("change", updateSort);
    }
    id("search-btn").addEventListener("click", search);
    id("home").addEventListener("click", loadAllToys);
  }

  /**
   * Finds and lists toys that match the given search term
   * @param  {Event} e - the button click event
   */
  function search(e) {
    id("loading").classList.remove("hidden");
    e.preventDefault();
    let searchTerm = id("search-term").value.trim();
    id("search-term").value = searchTerm;
    if (searchTerm !== "") {
      searchQuery = "&search=" + searchTerm;
    } else {
      searchQuery = "";
    }
    getToyList();
  }

  /**
   * Updates the toy list based on the filter options
   */
  function updateType() {
    id("loading").classList.remove("hidden");
    let typeBoxes = qsa(".toy-type");
    let typeString = "";
    for (let i = 0; i < typeBoxes.length; i++) {
      if (typeBoxes[i].checked === true) {
        typeString += typeBoxes[i].value + ",";
      }
    }
    if (typeString) {
      typeQuery = "&type=" + typeString.substring(0, typeString.length - 1);
    } else {
      typeQuery = "";
    }
    getToyList();
  }

  /**
   * Sends a toy list request with all the query fragments
   */
  function getToyList() {
    fetch(SELECT_API + typeQuery + sortQuery + searchQuery)
      .then(checkStatus)
      .then(JSON.parse)
      .then(generateToyList)
      .catch(handleError);
  }

  /**
   * Sorts the toy list based on the chosen option
   */
  function updateSort() {
    sortQuery = "&sort=" + qs("input[type=radio]:checked").value;
    getToyList();
  }


  /**
   * Loads all the toys in the toy list
   */
  function loadAllToys() {
    resetFilters();
    id("search-term").value = "";
    id("loading").classList.remove("hidden");
    fetch(SELECT_API)
      .then(checkStatus)
      .then(JSON.parse)
      .then(generateToyList)
      .catch(handleError);
  }

  /**
   * Resets all the filters and query fragments
   */
  function resetFilters() {
    let typeBoxes = qsa(".toy-type");
    for (let i = 0; i < typeBoxes.length; i++) {
      typeBoxes[i].checked = false;
    }
    let sortOptions = qsa(".sort");
    for (let i = 0; i < sortOptions.length; i++) {
      sortOptions[i].checked = false;
    }
    sortQuery = "";
    typeQuery = "";
    searchQuery = "";
  }

  /**
   * Generates the toy list
   * @param  {Object} json - the json containing a list of toys
   */
  function generateToyList(json) {
    id("error-text").classList.add("hidden");
    if (json.length) {
      id("toy-list").innerHTML = "";
      for (let i = 0; i < json.length; i++) {
        id("toy-list").appendChild(createToy(json[i].item,
          json[i].function, json[i].available, json[i].image));
      }
      id("loading").classList.add("hidden");
      id("toy-list").classList.remove("hidden");
    } else {
      handleSearchNotFound();
    }
  }

  /**
   * Creates a toy card
   * @param  {String} name - name of the toy
   * @param  {String} func - function/type of the toy
   * @param  {String} num - number of available toys in stock in a string
   * @param  {String} image - the url of the toy image
   * @return {Object} - the toy card displaying the given name, function, and number
   *                    available in stock
   */
  function createToy(name, func, num, image) {
    num = parseInt(num);
    let toyCard = document.createElement("div");
    let pic = document.createElement("img");
    let nameText = document.createElement("h2");
    let functionText = document.createElement("h3");
    let availableText = document.createElement("p");
    if(!image) {
      pic.src = "https://lakeshoreinlove.com/wp-content/uploads/2018/07/"
      + "thumbnail-default-nob.png";
      pic.alt = "placeholder image";
    } else {
      pic.src = image;
      pic.alt = name;
    }
    nameText.textContent = name;
    functionText.textContent = func;
    availableText.textContent = "Available: " + num;
    toyCard.appendChild(pic);
    toyCard.appendChild(nameText);
    toyCard.appendChild(functionText);
    toyCard.appendChild(availableText);
    toyCard.id = name;
    return toyCard;
  }

  /**
   * Shows that no toys matches the search term
   */
  function handleSearchNotFound() {
    displayErrorView("No toys found that match the filters and the search string '"
      + id("search-term").value.trim() + "', please try again.");
    id("loading").classList.add("hidden");
  }

  /**
   * Shows that there is an error when making a request to the web service
   */
  function handleError() {
    displayErrorView("Something went wrong with the request. Please try again later.");
    id("loading").classList.add("hidden");
  }

  /**
   * Displays the error view with the given error message
   * @param  {String} message - the error message to be displayed
   */
  function displayErrorView(message) {
    id("toy-list").classList.add("hidden");
    id("error-text").classList.remove("hidden");
    id("error-text").textContent = message;
  }

  /**
   * Helper method for getting element by id
   * @param {String} elementID - the id with which the target objects are attached to
   * @return {Object} the DOM element object with the specified id
   */
  function id(elementID) {
    return document.getElementById(elementID);
  }

  /**
   * Helper method for getting an element by selector
   * @param {String} selector - the selector used to select the target elements
   * @return {Object} A element in the DOM with the given selector
   */
  function qs(selector) {
    return document.querySelector(selector);
  }

  /**
   * Helper method for getting an element by selector
   * @param {String} selector - the selector used to select the target elements
   * @return {Object[]} A list of elements in the DOM selected with the given selector
   */
  function qsa(selector) {
    return document.querySelectorAll(selector);
  }

  /**
    * Helper function to return the response's result text if successful, otherwise
    * returns the rejected Promise result with an error status and corresponding text
    * Used the template from spec
    * @param {object} response - response to check for success/error
    * @returns {object} - valid result text if response was successful, otherwise rejected
    *                     Promise result
    */
   function checkStatus(response) {
     let responseText = response.text();
     if (response.status >= 200 && response.status < 300 || response.status === 0) {
       return responseText;
     } else {
       return responseText.then(Promise.reject.bind(Promise));
     }
   }
})();
