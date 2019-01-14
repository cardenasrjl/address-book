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

Make sure that the following parameters are well set in parameteres.yml
```
database_path: '%kernel.project_dir%/var/data/data.sqlite'
pictures_path: '%kernel.project_dir%/web/uploads/'
pictures_web_path: '/uploads/'
```

Database structure
---------------------

3 tables have been created: contacts, phones and emails. 

Structure is described in:

```
/src/AppBundle/Resources/config/doctrine/Contact.orm.yml
/src/AppBundle/Resources/config/doctrine/Phone.orm.yml
/src/AppBundle/Resources/config/doctrine/Email.orm.yml
```

Thats it !!
