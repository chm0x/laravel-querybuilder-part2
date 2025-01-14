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
## paginate()

It used for display data in a paginated format on a web page, but it retrieves all the data at once and then it breaks the data into pages. ***Its inefficient when working with very large data sets.***

**Its recommendable using chunk() method.**

## Chunking Data - chunk() method

It allows you to efficiently handle large data sets. 

The chunk() method retrieves data in smaller more manageable "chunks" rather than getting all data and chunking if afterwards. This make a big difference in terms of performance and resource usage. 

parameter1: how many data we retrieve to you
parameter2: callback function
```
$posts = DB::table('posts')
        ->orderBy('id')
        
        ->chunk(150, function($posts){
            foreach($posts as $post){
                dump($post->title);
        }
});
# the callback function receives each chunk of data
# as it argument, so every 150 post chunks will
# be set equal to a variable named posts.
# Using the callback function allows you to work with each
# chunk of data separately, it can be useful for things like
# performing calculations, filtering data, or transferring the
# data into a different format. 

# returns true
dd($posts);
```

## lazy load

Next to chunking data, you are also able to lazy load data when working with large data sets.

One common issue that dev face is memory exhaustion caused by loading too much data into memory at once. 
Laravel provides **two methods** that can help you with this issue.

One is the **lazy()** method and the **lazyById()** method

### lazy() 

This is used to retrieve a large number of records without overwhelming the server's memory. 

It returns an instance of the lazy collection which fetches results in small chunks as they are iterated over. 

```
$posts = DB::table('posts')
        ->orderBy('id')
        ->lazy();

# You can iterate.
foreach($posts as $post){
    dump($post);
}

# or withou foreach, use each()
$posts = DB::table('posts')
        ->orderBy('id')
        ->lazy()
        ->each(function($post){
            dump($post);
            dump($post->title);
        });

# returns an instance
dd($posts);
```

### lazyById()

This is quite similar to the lazy() method, but it's used to retrieve a single record by its ID.

It's mainly used to retrieve a single record by its ID. It can be useful when you want to retrieve a specific record from the database without loading all the records into memory at once. 
```
$posts = DB::table('posts')
        ->where('id', 1)
        ->lazyById();

$posts = DB::table('posts')
        ->where('id', 1)
        ->lazyById()
        ->first();
            

$posts = DB::table('posts')
        ->where('id', 1)
        ->lazyById()
        ->each(function($post){
            dump($post);
        });

dd($posts);
```

## raw methods

Write your own custom queries.

```
# SELECT RAW
$posts = DB::table('posts')
    ->selectRaw('count(*) as post_count')
    ->first();

# WHERE RAW
$posts = DB::table('posts')
    ->whereRaw('created_at > now() - INTERVAL 1 DAY')
    ->get();
    # ->first();

# GROUPBY RAW
$posts = DB::table('posts')
    ->select('user_id', DB::raw('AVG(min_to_read) AS avg_mintoread'))
    ->groupByRaw('user_id')
    ->get();

# HAVING RAW
$posts = DB::table('posts')
    ->select('user_id', DB::raw( 'SUM(min_to_read) AS total_time' ) )
    ->groupBy('user_id')
    ->havingRaw('total_time > 5 ')
    ->get();
```

## orderBy()

The default is ASC
```
$posts = DB::table('posts')
    ->orderBy('title')
    ->get();

$posts = DB::table('posts')
    ->orderBy('title', 'desc')
    ->get();

$posts = DB::table('posts')
    ->orderBy('title', 'desc')
    ->orderBy('min_to_read')
    ->get();
```

### latest() & oldest()

Default column is 'created_at' on both methods

```
# latest
$posts = DB::table('posts')
    ->latest()
    ->get();

$posts = DB::table('posts')
    ->latest('title')
    ->get();

$posts = DB::table('posts')
    ->oldest()
    ->get();

$posts = DB::table('posts')
    ->oldest('title')
    ->get();
```

## full text indexes

These are useful when you need to efficiently search for words or phrases in a text. This is helpful in application that involves searching through large amounts of text, such as blog or a search engine.

They are mainly designed for searching through large amounts of datasets. 

There are 7 full text indexes:
* $table->primary('id');
* $table->primary(['id', 'parent_id']);
* $table->unique('email');
* $table->index('state');
* $table->fulltext('body');
* $table->spatialindex('location');

```
> php artisan make:migration set_description_to_text_on_posts_table --table=posts
```

**whereFullText() && orWhereFullText()**



```
#Has two arguments, the first is the column, the second is the text that you want to search.
# No case sensitive.
$posts = DB::table('posts')
    ->whereFullText('description', 'asperiores')
    ->get();

# orWhereFullText
$posts = DB::table('posts')
    ->whereFullText('description', 'asperiores')
    ->orWhereFullText('description', 'Recusandae')
    ->get();
```

## limit & offset 

The limit() method is used to limit the number of records that are returned from a query.

The offset() method is used to skip a specified number of records from the beginning of a query. 

Those methods *can be inefficient for large data sets*. 

```
$posts = DB::table('posts')
    ->limit(10)
    ->get();

$posts = DB::table('posts')
    ->offset(2)
    ->limit(10)
    ->get();
```

## conditional clauses when()

advantage: simplicity, flexibility, readability

disadvantage: perfomance, debugging

```
$posts = DB::table('posts')
    ->when(function($query){
        return $query->where('is_published', false);
    })
    ->get();
```

## remove existing ordering

```
$posts = DB::table('posts')
    ->orderBy('is_published')
    ->get();

$posts = DB::table('posts')
    ->orderBy('is_published');

$unorderedPost = $posts->reorder()->get();
$unorderedPost = $posts->reorder('title', 'desc')->get();
```

## paginate()

pagination is a technique that allows us to divide a large set of data into smaller chunks of pages. 

```
$posts = DB::table('posts')
    # 1 parameters: specific the number of records you want.
    # default: 15
    ->paginate(2);
    # dont use the get() method.

$posts = DB::table('posts')
    # Change the name to URL for pagination
    ->paginate(2, pageName: 'test');
```

## simplePaginate()

This is a helper function on the Laravel QueryBuilder that enables pagination of records retrieved from the database. Its the same as paginate() method, but there are some impactful changes. 

In the frontend, all are the same as paginate() method. **But the view is better than paginate()**. 

***simplePaginate() should be used when working with a large data sets, its more efficient than the paginate methods.*** The reason is simply uses less memory to paginate records compared to the paginate() method.

```
$posts = DB::table('posts')
    ->simplePaginate(2);
```

## cursorPaginate()

This is a method of pagination that uses a cursor or a pointer to navigate through a set of data with cursor pagination. **Its useful for large datasets**.

Data is retrieved in smaller chunk rather than all at once. paginate() and simplePaginate() dont retrieve a small chunk of data. 

advantage: faster data retrieval, more accurate sorting of data.

disadvantage: more complex to implement, less intuitive for users, more resource intensive

**It can only be used with ordered data**. Challenging when dealing with unstructured or unordered data.

```
$posts = DB::table('posts')
    # you must specified a ORDERBY clause when usin cursorPaginate()
    ->orderBy('id')
    ->cursorPaginate(2)    
    ;
```