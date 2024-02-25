# Subscription Management System

A subscription management system that allows users to subscribe to a service and manage their subscription status. Built using PHP 8.3 and Symfony 7.

## Installation

After cloning the repository you have to run 
```
$ composer install
```
After that you have to configure the .env file or use .env.local file. The first thing you have to do is connect the api with database by changing the DATABASE_URL in .env file.
Once you setup the database connection we have to create the tables by running
```
$ php bin/console doctrine:migrations:migrate
```
For test purposes we have to insert some dump data to tables. So run the command below.
```
$ php bin/console doctrine:fixtures:load
```
Now that our database is fully setup, let's run one more command to generate secret keys for our authentication system (JWT)
```
php bin/console lexik:jwt:generate-keypair
```
## API Documentation
I lack the time to add a proper API Documentation for .e.x Swagger documentation, but for the sake of testing the API I added a POSTMAN collection to the repository (I know that we are not suppose to add to GIT that kind of file).
[Postman collection file](Subscription%20Management%20System.postman_collection.json)

## TODO
- Exception handling
- Swagger documentation
- PHP Unit test
- Code style checkers
