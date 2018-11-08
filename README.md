# catpress
Flat file "cms"  

A very short PHP file and a tree of some plain text files is all it takes now to make a webpage.


- No Javascript.  (I suppose you could, if you want, add it.)
- CSS applied to all pages.
- Consistent style, header, footer applied to all pages.
- No database
- Automatic linking of multi-page articles.
- No admin interface

This is meant for small, personal webpages.  Maybe even a blog that's contributed to by a handful of people.  You have to
know where to put files and how to format them in HTML.  This provides _NO_ administration interface.  The idea is the host 
filesystem enforces permissions.  You can even have multiple personal spaces within catpress, and multiple users with 
different unix file permssions able to log in and edit their own sub-space.  Authentication, encryption, passwords, and
permissions are all solved elsewhere by standard operating system components and utilities.  It is not worth the time 
and trouble of implementing any of that myself.  Here, I don't have to worry about the Wordpress admin interface having
a bug, or worry about running and securing a mysql instance.  

http://mwsherman.com is my personal page, and an example of a catpress site.  I don't know if its a _good_ example, its just
the only one that exists so far.  

There is a minimal example hosted at http://mwsherman.com/catpress-example/ .  The source code to that example is actually what
is in the repo here.  To make it work on another website, there are two variables in index.php that need to change: $pageroot
and $pagedomain.

Please use this!  Please tell me what's wrong with it!  Tell me its great!  Tell me its trash!  Fix it and send a pull request!

