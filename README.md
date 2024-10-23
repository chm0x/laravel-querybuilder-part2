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

## PESSIMISTIC LOCKING

The pessimistic locking is a technique that is used to prevent conflicts between multiple users when accessing the same resource.

Example scenario: Two users want to update the balance of a user in the users table.
Without passimistic locking, both user could update the balance at the same time and potentially overwrite each other's changes. This can lead incosistencies in the data, it can also cause issues with the app's functionalities. 

With pessimistic locking, which will ensure that only one user can update the balance at a time. 

It can be added inside a database transaction. 


This lockForUpdate() method *locks* the selected row until the transaction is completed.  
```
DB::transaction(function(){
    DB::table('users')
        ->where('id', 1)
        ->lockForUpdate()
        ->decrement('balance', 20);
    
    DB::table('users')
        ->where('id', 2)
        ->increment('balance', 20);
});
```

sharedLock() method can be used for locking rows in a table.
Similar to above, but it *locks the selected row for shared reading instead of exclusive writing*. This means that the other users can still read the locked rows, but they cannot modify them until the lock is released. (recommended)
```
DB::transaction(function(){
    DB::table('users')
        ->where('id', 1)
        ->sharedLock()
        ->decrement('balance', 20);
    
    DB::table('users')
        ->where('id', 2)
        ->increment('balance', 20);
});
```