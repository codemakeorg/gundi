# gundi
simple php framework

Server Requirements
The Gundi framework has a few system requirements.
You will need to make sure your server meets the following requirements:

PHP >= 5.5.9
* composer
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension

Install
 - Copy all directories and files to root path of your application.

Configuration

All of the configuration files for the Gundi framework are stored in the "app/Setting" directory.
Open Env.php file, check your host path, directory path and enter database connection parameters.

After configuring, run your terminal cd to root path of application and run few commands:
 - composer update
 - php bin/gundi db:migrate - for migrate tables
 - php bin/gundi seed:run - for inserts data

Run Tests in terminal
TDD tests:
- php vendor/bin/codecept run unit

BDD tests:
- php vendor/bin/codecept run acceptance
For running bdd tests you need selenium server and firefox version 17.

Framework Directories structure:

* /app - framework application
* /app/Core - libraries
* /app/Module - modules
* /app/Setting - constants and other configuration
* /app/Template - theme of application
* /bin - commands(migrate, seed etc.)
* /static - javascript and css libraries
* /tests - BDD and TDD tests
* /var - logging and other
* /vendor - framework dependencies

Module files and directories structure:
* /Component/Controller - Controller of module
* /Component/Block - Blocks of module
* /Database - Migrate and Seed
* /Model - Models
* /Static - css, html, js files
* /View - Views of Controllers and Blocks
* Bootstrap.php - Routes of module and other.
* info.json - Info of module
