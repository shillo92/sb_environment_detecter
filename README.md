Environment Detecter for Concrete5
=======================

Detects which environment is currently being used (i.e development or production).

###Explanation please
This Concrete5 package eases developers manage the site settings for **production & development** environments.
It creates an **environment.php** file (in the config folder) which provides the package with description of the available environments (production/development).

###Installation
The package can be easily installed by cloning this repo into your **packages** folder and install it via the Concrete5 CMS installer.

####Using Git
```shell
$ git clone https://github.com/shillo92/sb_environment_detecter.git myconcrete5site/packages
```

####Using Volo
```shell
$ cd myconcrete5site/packages
$ volo install shillo92/sb_environment_detecter
```

Where **myconcrete5site** is the path to your, well, Concrete5 site.

After you cloned the package, you'll need to install it through the dashboard using the Concrete5 Installer.
Since the package messes with the config/site.php file and since the configurations are loaded before any package, you'll have to call a function to detect & load the appropriate environment. It can be done using CLI or by adding the following code at the start of your config/site.php file:
```php
require DIR_BASE.'/packages/sb_environment_detecter/envsetup.php';
if (SbEnvironmentDetecter_setupEnvironment()->getContext() instanceof EnvironmentDetecter_EnvironmentContext_Production) {
    return;
}
```

####Using the envdet CLI
1. Open you'r shell application (e.g Terminal).
2. **cd** into the package's folder.
3. Run **php envdet setupSiteConfig**.

###How it works
If the package is installed correctly, at first session of the website the package will automatically setup the **config/environment.php** (if not already exists), detect if the website runs on development or production. If the package detected that the running environment is **production**, a **config/production** folder with a file called **site.php** is created.

###File: config/environment.php
This file needs to return associative array with the available environment names (currently only 'development' can be defined) associated with the host name at which this specific environment will to run on.
Default contents of the file:
```php
return array(
    'development' => 'localhost'
);
```

###File: config/production/site.php
This file needs to define the same settings defined in the original **config/site.php** (which is used for developoment environment). Default contents of the file:
```php
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
define('DB_DATABASE', '');
define('PASSWORD_SALT', '6PqBEvnqtBUARnXhIGVUCsfXhlhazelKCQj1zGRMHKsQN35RZ6ufUUztzI7zgjkc');
```
**Notice!** By default the file created by the package (either using CLI or automatically at first session), it copies the PASSWORD_SALT used by the original config/site.php file, therefore you should be aware what password salt your'e using for development database & for production database.

##How to use
###CLI
The package comes with a shell script to help you setup the files needed for the package to work properly. Here are simple steps to follow in order to setup everything correctly using the CLI:

1. Open shell application (e.g Terminal).
2. **cd** into the package's folder.
3. Run **php envdet setupEnv** to setup the **environments.php** file.
4. Run **php envdet setupProduction** to setup the **config/productions** folder.
5. You may edit the environments.php file to suit you're needs, but by default it uses **localhost** as development environment.
6. Edit file **config/production/site.php** to suit you're production database settings.
7. Fire you're app!

###Browser
When connected as **admin**, the package adds a toolbar button that enables you to switch between the environments you're working on (either development or production).

##Feedback
Please provide feedbacks so i'll know what issues I have and what can be further improved.

##License
This package is licensed under the GPLv2 License


