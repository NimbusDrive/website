## Contributing
> [!IMPORTANT]
> Whenever you pull down commits, make sure to check for dependency changes and setup instruction changes.
>
> If there is a dependency change, re-run `composer install`.

### Setting Things Up
1. Clone the repository (duh)
2. Open the repository in a command prompt window and run `composer install`
3. Create a symlink from your XAMPP PHP folder to the repository's source folder (See the tip below)
4. Create a `nimbus_drive` database in MySQL
5. Open the repository **source folder** (`/src/`) in a command prompt window and run `php index.php /ilgar/migrate`
6. Run XAMPP Apache and MySQL servers
7. You can now access the webpage at http://localhost/php/nimbus_drive/

> [!TIP]
> In order to create a symlink, you must be using a command prompt running as **Administrator**.
>
> Assuming XAMPP is installed in `C:\XAMPP`, your `(Destination)` will be \
> `C:\xampp\htdocs\php\nimbus_drive`.
>
> Your `(Source)` will be the path of the repository source folder on your machine, such as \
> `C:\Users\(User)\Documents\GitHub\website\src`.
>
> Full command example:
> ```
> mklink /D "C:\xampp\htdocs\php\nimbus_drive" "C:\Users\(User)\Documents\GitHub\website\src"
> ```
> After the symlink is created, you can continue with the other instructions.

> [!TIP]
> If you are on MacOS or Linux, the command is similar but using \
> `ln -s (Source) (Destination)`.
>
> Full command example:
> ```
> ln -s "~/Documents/GitHub/website/src" "/Applications/XAMPP/htdocs/php/nimbus_drive"
> ```

> [!CAUTION]
> If you drop the database or adjust migrations, you will have to edit the `migration.json` file in order for it to properly update.
>
> This file is located at `vendor/chez14/f3-ilgar/data/migration.json`.
>
> The best way to fix everything would be to drop every table in the database and set the json file's `"version"` to `-1`, then remigrate.
