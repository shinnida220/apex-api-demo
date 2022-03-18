## Description

This is a repository of the task with the following requirements:

Using any stack of your choice, develop an API for intra-bank transaction processing with the following requirements-

1. An endpoint for transaction validation with the following checks:
   i. Check that the account number is NUBAN and exists on your database
   ii. Return the corresponding account name stored against the nuban account number

2. An endpoint for completing the transaction with the following features:
   i. It should accept the same reference used for validation
   ii. Duplicate transactions should not be processed
   iii. Available balance should be enough to accommodate the transaction amount
   iv. Return the right response where balance is not enough
   v. Return a generic failed response for other non-successful conditions
   vi. Return successful response when transaction meets successful requirements

3. An endpoint for status check or requery

## Seting Up

1. Create a database
2. Update the database name in your _.env_ file
3. Run the migration:
   php artisan migrate

## Running the application

1. Run
   _php artisan route:list --path=api/_
   to view the list of available routes.
2. Run
   _php artisan serve_
   to start the application, it should be available at http://127.0.0.1:8000

You can test the available endpoints using Postman or any tool of your choice.
