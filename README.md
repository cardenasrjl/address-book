Address Book 
======================

The following demo is an address-book made with Symfony 3.4 and php 7 and Sqlite3

Installation
---------------------

First install all vendors with composer

```
$ composer install 
```

Create the database

```
$ php bin/console doctrine:database:create
```

Force to update the schema.

```
php bin/console doctrine:schema:update --force
```

Make sure that following parameters are well set in parameteres.yml
```
database_path: '%kernel.project_dir%/var/data/data.sqlite'
pictures_path: '%kernel.project_dir%/web/uploads/'
pictures_web_path: '/uploads/'
```

Thats it !!
