# Technologies Used
- [Fat-Free Framework](https://fatfreeframework.com/3.9/home)
- [PHP dotenv](https://github.com/vlucas/phpdotenv)
- [PHP CS Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)

# Setting Things Up
> [!IMPORTANT]
> 1. Clone the repository (duh)
> 2. Open the repository in a command prompt window and run `composer install`
> 3. Create a symlink from your XAMPP PHP folder to the repository's source folder
> 4. Create a `nimbus_drive` database in MySQL
> 5. Run XAMPP

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
> After the symlink is created, you can start up XAMPP and you will be able to access the page by navigating to http://localhost/php/capstone_project/
