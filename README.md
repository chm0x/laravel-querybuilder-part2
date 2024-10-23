# QUERY BUILDER PART 2

## TRANSACTION

Database transaction are a way of ensuring data, consistency and integrity in the app.

Basically allows you to ensure that the series of database operations are performed as a single unit of work. This can be useful in situations where you need to perform multiple database operations together and want to ensure that they are all succeded or failed as a single unit. 

Example: The user has to create a new account on a website, and the account creation process involves creating a user records, creating a billing record and creating a shipping address record. 

This example below, indicates that the user with ID 1 send money to the user with ID 2.

```
DB::transaction(function(){
    DB::table('users')
        ->where('id', 1)
        ->decrement('balance', 20);
    
    DB::table('users')
        ->where('id', 2)
        ->increment('balance', 20);
}); 
```