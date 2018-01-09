# Initialize database command

## Register

In file `App\Console\Kernel`

```php
<?php

namespace App\Console;

class Kernel
{
    protected $commands = [
        \fk\utility\Console\InitDatabaseCommand::class,
    ];
}
```

## Usage

```bash
php artisan init:db
```