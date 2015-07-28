# laravel-generator
generate migration,seeder,model,controller,repository,validator and resource routes from json model description.

<VirtualHost crud.lgalaz.dev:80>
    DocumentRoot "C:/Users/luis.galaz/Documents/Projects/laravel-generator\src\public"
    ServerName crud.lgalaz.dev
    ErrorLog "logs/acrud.lgalaz.dev-error.log"
    CustomLog "logs/crud.lgalaz.dev-access.log" combined
    <Directory C:/Users/luis.galaz/Documents/Projects/laravel-generator\src\public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

Run migrate

Create seeders with factory models.

