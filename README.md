# NewsAggregator Api
 A news aggregator laravel application that pulls articles from three news sources(NewsApiOrg, Guardian News Api, New York Times) and displays them in a clean, easy-to-read format

## Table of Contents

* [Features](#Features)
* [Configuration](#Configuration)
* [Tech Stack](#Tech%Stack)
* [Documentation](#Documentation)

## Features
<li> User Authentication(Sign up & Sign in) with laravel sanctum</li>
<li>Search for news artciles by author, category </li>



## Configuration

Project is built with Laravel version 10

<li>Ensure that php version >= 8.1 is installed</li>
<li>Install and setup composer</li>
<li>Get api key for newsapi from -[NewsApiOrg: ](https://newsapi.org/)</li>
<li>Get api key for Guardians news from -[Guardian News: ](https://open-platform.theguardian.com/documentation/)</li>
<li>Get api key for new york times from -[NewYorkTimes: ](https://developer.nytimes.com/apis/)</li>


```bash
git clone this repo
cd into the project
composer install
php artisan migrate
php artisan l5-swagger:generate
php artisan serve
```

## Tech Stack
Php Laravel

## Screenshots

- Desktop View
![](/src/assets/readmeimg/desktop.png)

- Mobile View
![](/src/assets/readmeimg/mobile.png)
