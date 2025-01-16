# Technologies Used
- [Fat-Free Framework](https://fatfreeframework.com/3.9/home)
- [PHP dotenv](https://github.com/vlucas/phpdotenv)
- [PHP CS Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)
- [Ilgar](https://github.com/chez14/f3-ilgar)

# Setting Things Up
> [!IMPORTANT]
> 1. Clone the repository (duh)
> 2. Open the repository in a command prompt window and run `composer install`
> 3. Create a symlink from your XAMPP PHP folder to the repository's source folder (See the tip below)
> 4. Create a `nimbus_drive` database in MySQL
> 5. Open the repository **source folder** (`/src/`) in a command prompt window and run `php index.php /ilgar/migrate`
> 6. Run XAMPP Apache and MySQL servers
> 7. You can now access the webpage at http://localhost/php/capstone_project/

> [!TIP]
> In order to create a symlink, you must be using a command prompt running as **Administrator**.
>
> Assuming XAMPP is installed in `C:\XAMPP`, your `(Destination)` will be \
> `C:\xampp\htdocs\php\capstone_project`.
>
> Your `(Source)` will be the path of the repository source folder on your machine, such as \
> `C:\Users\(User)\Documents\GitHub\capstone_project\src`.
>
> Full command example:
> ```
> mklink /D "C:\xampp\htdocs\php\capstone_project" "C:\Users\(User)\Documents\GitHub\capstone_project\src"
> ```
> After the symlink is created, you can continue with the other instructions.

> [!CAUTION]
> If you drop the database or adjust migrations, you will have to edit the `migration.json` file in order for it to properly update.
>
> This file is located at `vendor/chez14/f3-ilgar/data/migration.json`.
>
> The best way to fix everything would be to drop every table in the database and set the json file's `"version"` to `-1`, then remigrate.

# Contributing
> [!IMPORTANT]
> Whenever you pull down commits, make sure to check for dependency changes and setup instruction changes. \
> If there is a dependency change, re-run `composer install`.
