# File Browser
A PHP online file browser with an easy JSON options file and streamlined features all wrapped up in a nice modern design.

![example of the interface](https://github.com/owoalex/fileBrowser/blob/master/example.png)
## Features
* Multiple Users
* Permission Groups
  * Each group has certain permission for different directories with an inherited structure
* Session Logging
  * IP logging
  * User logging
  * Activity logging
* All settings in human-readable JSON

## How to set up the file browser
Simply add the folder to your web server of choice, making sure to prevent users from accessing the users.json file, the groups.json file and the logs folder.
You will want to edit the groups.json file to configure file access for different groups. By default, all new accounts will be in the "USERS" group. As such this group should have minimal/no permissions. The default configuration is for testing purposes only.

## Note
The test "GUEST" account is in group "USER" by default. It has the default password of "1234"