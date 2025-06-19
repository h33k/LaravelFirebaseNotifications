# Welcome to the Laravel Firebase Notification App!

To use this app, please follow these steps:

1. Run migrations using `php artisan migrate`  
2. Create a new admin user with `php artisan make:filament-user`  
3. Paste your public Firebase app credentials into the `public/firebaseConfig.js` file  
4. Place your Firebase private key JSON file into the `config` folder and rename it to `privatekeys.json`  
5. Create a new user and register device via API  
6. Create your first notification job  

**Note:** To use this app you need to set up cron in your system or you can trigger events with `php artisan app:process-scheduled-notifications` command
