# Setting Things Up
> [!IMPORTANT]
> 1. Clone the repository (duh)
> 2. Open the repository in a command prompt window and run `composer install`
> 3. Create a symlink from your XAMPP PHP folder to the repository's source folder

> [!TIP] Setting up the symlink
> 1. Open a command prompt window as **Administrator**
> 2. Enter `mklink /D "(Destination)" "(Source)"`

> [!NOTE] Example symlink
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
