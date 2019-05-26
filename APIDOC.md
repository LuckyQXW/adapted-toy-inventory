# Adapted Toy Inventory API Documentation
The Adapted Toy Inventory API provides an interface for accessing the Adapted Toy
Inventory database and allows users to get a list of toys that matches different
filtering criteria.

## Get the full list of toys from the inventory
**Request Format:** `inventory-select.php?mode=toys`

**Request Type:** GET

**Returned Data Format**: JSON

**Description:** Returns all the toys from the Adapted Toy Inventory database, including:
+ item: full name of the toy
+ function: features of the toy, can be a combination of Lights, Sound, Motion, and Bump N' Go
+ available: number of the given toy in stock that is available for request
+ image: the full URL that links to an image of the toy


**Example Request:** `inventory-select.php?mode=toys`

**Example Response:**
```json
[
    {
        "item": "Fire Engine",
        "function": "Lights , Sound , Motion , Bump N' Go",
        "available": "6",
        "image": ""
    },
    {
        "item": "Flapping Yellow Duck",
        "function": "Lights , Sound , Motion",
        "available": "0",
        "image": ""
    },
    ...
]
```

**Error Handling:**
If missing the `toys`, it will respond a 400 error with a message `Please put in a valid mode parameter.`

## Get a filtered list of toys from the inventory
### Filter based on the function and availability of the toy
**Request Format:** `inventory-select.php?mode=toys&type={comma-separated list of filters}`

**Request Type:** GET

**Returned Data Format**: JSON

**Description:** Returns a list of toys from the Adapted Toy Inventory database that match all the given filters.
If passed in any type that is not included in the list below, the API will return an empty list.
A list of valid types:
+ Lights
+ Motion
+ Sound
+ Bump N' Go
+ Available (will only return toys with more than one available in stock)

**Example Request:** `inventory-select.php?mode=toys&type=Lights,Sound,Available`

**Example Response:**
```json
[
    {
        "item": "Fire Engine",
        "function": "Lights , Sound , Motion , Bump N' Go",
        "available": "6",
        "image": ""
    },
    {
        "item": "Laugh & Learn My Pretty Learning Lamp",
        "function": "Lights , Sound , Motion",
        "available": "1",
        "image": "https://keep.google.com/u/1/media/v2/1olcQkaFmnprAvdnJsjeBi0HMHXNv3Dvu1GRNWxFr0UcSfDXX9DpWaBt7RxHOxjw/1N0kezevpDoPN690EaP7LElo2XTTuswW4vRo3sxgQW0WA_Def7O-j7wvRF6-MhA?accept=image/gif,image/jpeg,image/jpg,image/png,image/webp,audio/aac&sz=1600"
    },
    ...
    (rest of the toys that includes both Lights and Sound function and are available in stock)
]
```
### Search for a toy by name

**Request Format:** `inventory-select.php?mode=toys&search={search term}`

**Request Type:** GET

**Returned Data Format**: JSON

**Description:** Returns all the toys from the Adapted Toy Inventory database that have a case-insensitive match with the search term in the name of the toy.
If none of the toys matches the search term, returns an empty list.

**Example Request:** `inventory-select.php?mode=toys&search=frozen`

**Example Response:**
```json
[
    {
        "item": "Frozen Olaf Plush",
        "function": "Sound , Motion",
        "available": "1",
        "image": "http://depts.washington.edu/adaptuw/wordpress/wp-content/uploads/2019/01/DSC_0199.jpg"
    }
]
```
### Get a sorted list of toys

**Request Format:** `inventory-select.php?mode=toys&sort={integer}`

**Request Type:** GET

**Returned Data Format**: JSON

**Description:** Returns a sorted list of toys from the Adapted Toy Inventory database in the specified order by name.
Two possible values for sort:
+ 0: alphebetical order
+ 1: reverse alphebetical order
**Example Request:** `inventory-select.php?mode=toys&sort=0`

**Example Response:**
```json
[
    {
        "item": "2435 Prisma Light Kaleidoscope Light Show Projector",
        "function": "Lights",
        "available": "4",
        "image": ""
    },
    {
        "item": "Animated Happy Birthday Teddy Bear",
        "function": "Sound , Motion",
        "available": "1",
        "image": ""
    },
    {
        "item": "Automatic Bubble Machine",
        "function": "Motion",
        "available": "1",
        "image": ""
    },
    {
        "item": "Baby Beats Monkey Drum",
        "function": "Lights , Sound",
        "available": "0",
        "image": ""
    },
    ...
]
```

### Combining all filter options

**Request Format:** `inventory-select.php?mode=toys&type={comma-separated list of filters}&search={search term}&sort={integer}`

**Request Type:** GET

**Returned Data Format**: JSON

**Description:** Returns all the toys from the Adapted Toy Inventory database that matches all the filter options. The `type`, `search`, `sort` parameters can be passed in any order.
If none of the toys matches all the given filters, returns an empty list.

**Example Request:** `inventory-select.php?mode=toys&type=Lights,Sound&search=monkey&sort=0`

**Example Response:**
```json
[
    {
        "item": "Baby Beats Monkey Drum",
        "function": "Lights , Sound",
        "available": "0",
        "image": ""
    },
    {
        "item": "Light N Go Movin' Lights Monkey",
        "function": "Lights , Sound , Motion , Bump N' Go",
        "available": "0",
        "image": "https://keep.google.com/u/1/media/v2/1GiRgws-QC1e_B31Q0TvQLcMC2c97Yrl4a_bHRE77UL1hzhHDn8qCKyi1os_Ymg/1mNLRpS_JZZukJJG7dMHAcbFiYIx0HeDJDAkXFIr-8b3_xClcMyy8RuMLAoCIKzA?accept=image/gif,image/jpeg,image/jpg,image/png,image/webp,audio/aac&sz=1600"
    },
    {
        "item": "Movin Lights Monkey",
        "function": "Lights , Sound , Motion",
        "available": "3",
        "image": ""
    }
]
```
