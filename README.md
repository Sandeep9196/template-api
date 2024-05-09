### ENVIRONMENT SETTINGS
Add this to environment variables `SANCTUM_STATEFUL_DOMAINS="localhost:8080"`

### RUN THE FOLLLOWING ARTISAN COMMAND
`composer install` 
### Run this command every time new API route with name is added
`php artisan update:permissions`

### RUN THE COMMAND
`php artisan migrate:refresh --seed`


