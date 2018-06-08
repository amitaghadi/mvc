https://jelastic.com/blog/php-session-clustering-and-load-balancing-in-the-cloud/

http://localhost/mvc/table/list_table/people?show=errors

localhost/mvc/table/insert_into_table/people?show=errors
fields and value

localhost/mvc/table/update_table/people?show=errors 

fields and value

localhost/mvc/table/delete_from/people?show=errors 
id



https://www.javatpoint.com/php-star-triangle
https://www.thecodedeveloper.com/php-program-to-print-triangle-number/

https://www.javatpoint.com/php-fibonacci-series

https://www.w3resource.com/php-exercises/php-function-exercise-2.php


https://pear.php.net/manual/en/package.networking.net-mac.format.php






https://github.com/amitaghadi/mvc



https://stackoverflow.com/questions/12614541/whats-the-difference-between-myisam-and-innodb

find . -name \*.php -exec php -l "{}" \;

https://dev.to/iriskatastic/the-role-skills-and-duties-of-a-software-architect-413

We have 2 tables, Table A has dept_id,dept name. Table B has id, dept_id,name. need to print data users who are associated with number of department. if any user is not associated with any department print zero also.

SELECT emp.name,count(dpt.id) FROM emp Left join dpt on emp.did=dpt.id group by emp.name 



https://www.guru99.com/joins.html


indexes

http://www.treselle.com/blog/mysql-indexes-basicstypes-and-features/

http://www.treselle.com/blog/mysql-indexes-basicstypes-and-features/
=============================================================================================================================







Introduction

Indexes in MySQL are physical objects that are used to enforce uniqueness in a table. This will ensure there are no duplicate values in a table. Indexes help in speeding up query processing and therefore the database performance.

This blog covers the basics stuff on what an Index is, how to create an Index on single and multiple columns, and how it works in MySQL. This blog also covers different types of indexes in MySQL.
Use Case

This use case describes how to create an Index on a table and subsequently ‘Explain’ the query to check how the index works.

What we want to do:

    Provide a basic explanation about an Index.
    Different types of Indexes in MySQL.
    Create an Index on a table and Explain the Query.

Solution
MySQL Indexes: A Basic explanation

Like tables, indexes also consist of rows and columns but it stores the data in a logically sorted manner to improve search performance. It’s probably like a telephone directory where they are usually sorted last_name, first_name and potentially other criteria (e.g. zip code). This type of sorting makes it possible to find all entries for a specific last name quickly. If we just know the first name, it is possible to find the entries for the combination last name/first name very quickly.

However, if we just know the first name, the telephone book does not really help. It’s the same thing for a multi-column database indexes. So, an index can potentially improve search performance. If we have a wrong index for the question (e.g. a phonebook when searching by first name) they might be useless.

We can have many indexes on the same table but on different columns. So, an index on last_name, first_name is different from an index on first_name only (for which we need to optimize our searches by first name).

Indexes hold redundant data (ex: clustered indexes = telephone book). They have the same information as stored in the table (ex: function based indexes), but in a sorted manner. This redundancy is automatically maintained by the database for each write operation we perform (insert/update/delete). Consequently, Indexes decrease write performance.
Different types of Indexes in MySQL

    Clustered indexes

Besides finding data quickly, indexes can also be used to optimize sort operations (order by) and physically arrange related data closely together.This is process is called clustering of data.

Accessing a row through the clustered index is fast because the row data is on the same page where the index search leads. If a table is large, the clustered index architecture often saves a disk I/O operation, when compared to storage organizations that stores row data using a different page from the index record. (For example, MyISAM uses one file for data rows and another for index records.)

In InnoDB, the records in non-clustered indexes (also called secondary indexes) contain the primary key columns for the row that are not in the secondary index. InnoDB uses this primary key value to search for the row in the clustered index. If the primary key is long, the secondary indexes use more space, so it is advantageous to have a short primary key.

By default, with InnoDB, the primary index is a clustered index.

    Comparison to Non-clustered indexes

All indexes are physically stored in order (a btree actually), so if we are returning just the column that is stored in the index, we’re still getting the same benefit. That is because the indexed column’s actual value is stored in the index, therefore MySQL will use the index value instead of reading the record. However, if we start retrieving columns that aren’t part of the index, this is where we want the actual records stored in order, such as they are with a clustered index.

    Primary Key

A PRIMARY KEY is a unique index where all key columns must be defined as NOT NULL. If the primary key is not declared as NOT NULL, then MySQL declares them implicitly (and silently). A table can have only one PRIMARY KEY. The name of a PRIMARY KEY is always PRIMARY, which thus cannot be used as the name for any other kind of index.

    Unique Key

A UNIQUE index creates a constraint such that all values in the index must be distinct. An error occurs if we try to add a new row with a key value that matches an existing row. For all engines, a UNIQUE index permits multiple NULL values for columns that contain NULL.

    Normal Index

If it’s not primary or unique, it doesn’t constrain values inserted into the table, but it does allow them to be looked up more efficiently.

    Full Text Index

It is a more specialized form of indexing that allows full text search. Think of it as (essentially) creating an “index” for each “word” in the specified column. Up to 5.5 versions, this index is supported for MyISAM engine only but from 5.6 it supports both MyISAM and InnoDB engines.
Index Creation Steps on a table

    Create table:

We have first created the ‘zipcodes_tab’ table and inserted records into it using the query below. The Primary Key is defined on the ‘id’ column and number of records inserted into the table is 11097.
Query
		CREATE TABLE `zipcodes_tab` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `county` varchar(3) DEFAULT NULL,
		  `zipcode` varchar(5) DEFAULT NULL,
		  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB;
1
2
3
4
5
6
7
	
        CREATE TABLE `zipcodes_tab` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `county` varchar(3) DEFAULT NULL,
          `zipcode` varchar(5) DEFAULT NULL,
          `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB;

     Record select without Index :
    Query
    SELECT COUNT(*) FROM zipcodes_tab WHERE county='071';
    1
    	
    SELECT COUNT(*) FROM zipcodes_tab WHERE county='071';
    Output:

+ —————- +
| COUNT(*)   |
+ —————- +
| 192            |
+ —————- +

The number of rows returned with county=’071’ is 192.

    Explain the query to check the count of records with / without an index:

Now we will use the Explain clause in the same query. We are doing a non-indexed search here.Using the Explain Clause in front of the SELECT query, will display which indexes (if any) MySQL would use.
Query
EXPLAIN SELECT * FROM zipcodes_tab WHERE county='071';
1
	
EXPLAIN SELECT * FROM zipcodes_tab WHERE county='071';

Output:

********************** 1. row **********************
id: 1
select_type: SIMPLE
table: zipcodes_tab
type: ALL
possible_keys: NULL
key: NULL
key_len: NULL
ref: NULL
rows: 10658
Extra: Using where

From above result, we can see that MySQL has searched through 10658 rows to select the 192 rows. This shows that the MySQL optimizer didn’t find any keys to use. All the key values here are ‘NULL’.

    Create Simple Index and perform select: 

We will create an index on the ‘county’ column and run the same EXPLAIN-statement again, we will get a different and better result:
Query
ALTER TABLE zipcodes_tab ADD INDEX ind_county(`county`);

EXPLAIN SELECT * FROM zipcodes_tab WHERE county='071';
1
2
3
	
ALTER TABLE zipcodes_tab ADD INDEX ind_county(`county`);
 
EXPLAIN SELECT * FROM zipcodes_tab WHERE county='071';

 Output:

********************** 1. row **********************
id: 1
select_type: SIMPLE
table: zipcodes_tab
type: ref
possible_keys: ind_county
key: ind_county
key_len: 6
ref: const
rows: 192
Extra: Using index condition

After creating index, it searches through only 192 rows.

    Create Composite Index (Index on multiple columns) and perform select:

First let us run an Explain query with two columns in the Where clause, with the existing index ind_county.
There is only one row in the table that satisfies both the conditions, county = ‘071’ and zip code =’06125’ (as given below):
Query
SELECT COUNT(*) FROM zipcodes_tab WHERE county='071' and zipcode='06125' ;
1
	
SELECT COUNT(*) FROM zipcodes_tab WHERE county='071' and zipcode='06125' ;

 Output:

+ —————- +
| COUNT(*)    |
+ —————- +
| 1                  |
+ —————- +

Now we use the Explain clause with the Select statement above, to find out the Keys used:
Query
EXPLAIN SELECT * FROM zipcodes_tab WHERE county='071' and zipcode='06125';
1
	
EXPLAIN SELECT * FROM zipcodes_tab WHERE county='071' and zipcode='06125';

 Output:

********************** 1. row **********************
id: 1
select_type: SIMPLE
table: zipcodes_tab
type: ref
possible_keys: ind_county
key: ind_county
key_len: 6
ref: const
rows: 192
Extra: Using index condition;Using where

From above result, we can make out that, it searches 192 rows to filter out 1 row.
Now we will create a Composite Index on ‘county’ and ‘zipcode’ columns and execute Explain on the same query.
Query
ALTER TABLE zipcodes_tab ADD INDEX ind_county_zipcode(`county`,`zipcode`);

EXPLAIN SELECT * FROM zipcodes_tab WHERE county='071' and zipcode='06125';
1
2
3
	
ALTER TABLE zipcodes_tab ADD INDEX ind_county_zipcode(`county`,`zipcode`);
 
EXPLAIN SELECT * FROM zipcodes_tab WHERE county='071' and zipcode='06125';

Output:

********************** 1. row **********************
id: 1
select_type: SIMPLE
table: zipcodes_tab
type: ref
possible_keys: ind_county,ind_county_zipcode
key: ind_county_zipcode
key_len: 14
ref: const,const
rows: 1
Extra: Using index condition

From the above result, we find after creating the new composite index only 1 row has been read or hit.

    Drop Index

Now let us remove the ‘ind_county’ index on the county column and re-run the Explain query:
Query
DROP INDEX ind_county ON zipcodes_tab;

EXPLAIN SELECT * FROM zipcodes_tab WHERE county='071';
1
2
3
	
DROP INDEX ind_county ON zipcodes_tab;
 
EXPLAIN SELECT * FROM zipcodes_tab WHERE county='071';

Output:

********************** 1. row **********************
id: 1
select_type: SIMPLE
table: zipcodes_tab
type: ref
possible_keys: ind_county_zipcode
key: ind_county_zipcode
key_len: 6
ref: const
rows: 192
Extra: Using index condition

From the above result we get to know that even if we drop ‘ind_county’ index,  it uses the ‘ind_county_zipcode’ index because the ‘county’ column starts from the leftmost side.
Conclusion

Indexing is one of the essential features of MySQL which helps optimizing query performance. Before creating Index on a column, we have to check if any other index has been defined on the same column in that table. Indexes, like primary key and unique index help avoid duplicate row data, but at the same time there are few disadvantages with indexes.

    When an index is created on the column(s), MySQL also creates a separate file that is sorted, and contains only the field(s) we’re interested to sort.
    Firstly, indexes consume adequate amount of  disk space. Usually the space usage isn’t significant, but because of creating index on every column in every possible combination, the index file grows at a rather significant rate than a data file. In the case of large table size, the index file could reach the operating system’s maximum file size.
    Secondly, the indexes slow down the speed of writing queries, such as INSERT, UPDATE and DELETE. Because MySQL has to internally maintain the “pointers” to the inserted rows in the actual data file, there is a high performance price to pay.

References

    http://use-the-index-luke.com/sql/table-of-contents
    http://dev.mysql.com/doc/refman/5.0/en/innodb-index-types.html








