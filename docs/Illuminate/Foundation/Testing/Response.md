# Illuminate\Foundation\Testing\Response

### assertJsonStructure

#### overview

```php
<?php
public function assertJsonStructure(array $structure = null, $responseData = null);
```

Asserts that the response body is JSON and has the fields listed in `$structure`

#### $structure

```php
<?php

$structure = [
    /*
     +---------------------------------------
     | These fields are required
     +---------------------------------------
     */
    'code', 'message',

    /*
     +---------------------------------------
     | `data` is required
     | and with these fields required inside
     +---------------------------------------
     */
    'data' => ['id', 'name', 'e.t.c', 'fields in data'],

    /*
     +---------------------------------------
     | Contains a list data
     +---------------------------------------
     */
    'list' => [
        // Every item should contain the following keys
        '*' => ['id', 'pictures', 'fields in per item of the list']
    ]
];
```