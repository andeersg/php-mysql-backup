# Automatic backup of MySql-database to Dropbox

To use this script run setup.php from the terminal with ```php setup.php``` and follow the instructions:

* Go to the url.
* Log in and allow this app.
* Copy the authorization code and paste it in your terminal window.
* If you want notifications via [Pushover](http://pushover.net) you can input your tokens.
* If the authorization to dropbox was successful you should see your accountinformation.

## Add databases and mysql-information

Next, open config.json and fill in the username and password of a mysql user with the permission to access the databases you want to backup.

The databases object consist of the database name with two keys, "filename" and "timestamp". If you want to give the backup a custom name you can specify it in "filename". "timestamp" is a boolean, if TRUE your backups will be placed in a folder named with timestamps. If false your backup will be written to a single file and overwritten each time.

Because of Dropbox revisions you will still have access to older versions even if you backup to the same file.

## Automatic backup

To start the backup you must setup a cronjob that executes backup.php at your desired interval.