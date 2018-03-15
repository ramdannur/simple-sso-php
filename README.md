# Single Sign On (SSO) 

Single sign-on (SSO) is a property of access control of multiple related, yet independent, software systems. With this property, a user logs in with a single ID and password to gain access to a connected system or systems without using different usernames or passwords, or in some configurations seamlessly sign on at each system. 
See more about SSO https://en.wikipedia.org/wiki/Single_sign-on.

This is simple simulation Single Sign On (SSO) with PHP Native, PDO Ekstension, and MySQL for database.

Execute this script to run the server

## APP Provider
```
php -S 127.0.0.1:80 -t provider\
```

## Client 1
```
php -S 127.0.0.2:80 -t provider\
```

## Client 2
```
php -S 127.0.0.3:80 -t provider\
```
