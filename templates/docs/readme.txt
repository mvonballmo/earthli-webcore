Thanks for downloading an earthli WebCore product!

------------------------------------
..:: Requirements ::..
------------------------------------

- PHP 4.x
- MySQL 3.x or MySQL 4.x
- PHPMyAdmin (or similar database administration tool)

The software is developed on Apache 1.3.x/PHP 4.3.x/MySQL 4.01 on Windows 2000
and has been successfully deployed to systems as old as Apache 1.3.x/PHP 4.1.2/MySQL 3.x 
running on Debian. That is, both Linux and Windows deployments have been tested and
relatively old versions of PHP are also supported.




------------------------------------
..:: Detailed instructions ::..
------------------------------------

We strongly recommend using the "install" and "First Run" manuals, available online at

<http://earthli.com/software/webcore/install.php>

and

<http://earthli.com/software/webcore/first_run.php>

If you've been through the drill before, use the Quick Start instructions below.




------------------------------------
..:: Quick start instructions ::..
------------------------------------

------------------------------------
Configure files

- Copy the extracted folder to your web site
- Edit the extensions/webcore/make.php configuration file to use the correct
  login information for your database and the correct paths for your web site
- Edit the extensions/<app_name>/make_app.php configuration file to customize
  the application(s) for your site

------------------------------------
Configure .htaccess

If you downloaded the WebCore Suite, the applications will share a common WebCore
library. 

- Adjust the .htaccess file to include the installation path

------------------------------------
Configure database

For a first time installation (no other earthli packages yet):

- Create a new database
- Using PHPMyAdmin or a similar tool, run sql/icons_themes.sql and sql/shared_users.sql

For each application you want to install

- Using PHPMyAdmin or a similar tool, run sql/<app_name>.sql

------------------------------------
Log in

- Log in with user 'root' and password 'password'. Change the password by going to the
  user home page and clicking the key icon.

------------------------------------
Create content

- For new applications, create a new folder by clicking the folder icon on the home page.
- Assign rights for other users by clicking on the lock icon on the home page.




------------------------------------
..:: Support ::..
------------------------------------

Please report all errors and omissions to <software@earthli.com>.
