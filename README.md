# cosmomonger

You'll need to download the Yii 1.1.17 framework and place it in the folder above this checkout.
You can download from the Yii Github project here: https://github.com/yiisoft/yii/releases/download/1.1.17/yii-1.1.17.467ff50.tar.gz

The extracted folder should have a name of __yii-1.1.17.467ff50__

Then create the following folders and give the Apache process write access to them

    mkdir protected/assets protected/runtime
    chown www-data protected/assets protected/runtime
    # or less secure, give all users write access to the folders.
    chmod a+rwx protected/assets protected/runtime


You'll also need to setup the MySQL database. Liquibase (http://www.liquibase.org/)
is used to manage database changes, this is due to the project orignally being 
an ASP.NET MVC + SQL Server based web application.

Edit the db/liquibase.properties file with the location of your MySQL database
and then run the liquibase-updateDB.bat or liquibase-updateDB.sh file depending
on your platform. The output will be redirected to liquibase.log for review.

Requires PHP 5.2+