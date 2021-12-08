# Company Assets Management System

> Simple Company Assets Management System project using Laravel

This project runs with Laravel version 8.65

# Getting started

Assuming you've already installed on your machine: PHP (>= 7.3.0), [Composer](https://getcomposer.org) & [Git](https://git-scm.com/).

## Installation

Clone the repository

    git clone https://github.com/Raju875/assets-management-system.git
    
Switch to the repo folder

    cd assets-management-system
  
Install all the dependencies using composer

    composer install
    
make the required configuration changes in the .env file  
    
Run the database migrations with seeder (**Set the database connection in .env before migrating**)

    php artisan migrate:fresh --seed

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000
