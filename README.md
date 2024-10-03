## Work Records

#### Application for recording employee working time. It allows you to count the number of hours worked on a given date and value hours worked with due consideration rates and division into standard hours and overtime.

To test the application locally, you need to install:

1. Composer
2. Docker

You should configure the database in Your ```.env.local```.
For example ```DATABASE_URL="mysql://root:root@db:3306/symfony?serverVersion=8.0&charset=utf8mb4"```

Run:
1. ```composer install```
2. ```docker-compose up```

...and go to https://127.0.0.1:8000, enjoy! :)

The configuration parameters of the working time system are located in the ```paramaters.yaml``` file.
