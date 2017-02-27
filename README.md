Download the app and install dependencies:  
```
$ git clone git@path
$ cd path/to/the/application
$ composer install
```

To run the app execute:
```
$ php -S localhost:8000 -t public public/router.php
```

The app routes:  
`GET /files` - gets file names  
`GET /files/<file_name>` - gets the file metadata  
`GET /files/<file_name>/content` - gets the file content  

`PUT /files/<file_name>` - uploads a file and returns its metadata  
PARAMS

* rewrite = `true(default)|false` - determines whether an existing file will be overwritten by this upload

To run tests execute:  
```
$ vendor/bin/phpunit
```
