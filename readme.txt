InnreachTracker 1.1.0
by Ken Irwin, Wittenberg University
Release date: 3 June, 2014

This suite of scripts takes data exported from Innovative Interfaces (III)
catalog systems to show what books have been requested from other libraries
using the INNReach request system. This software is released under the 
Creative Commons Attribution-Share Alike 3.0 United States license. For more 
details, see the license.php file.

The software was designed in a LAMP (Linux/Apache/MySQL/PHP) environment
and has also been tested on Sun OS 5.8. Version 1.1.x requires PHP version 4.1.0 or later.

A few things are required before using the software.

1. Unzip and untar the files into their own directory, probably like this: 
   > gunzip InnReachTracker-1.0.2c.tar.gz
   > tar -xvf InnReachTracker-1.0.2c.tar

From this point on in the installation process, you may point your web browser 
at the index page for this folder and it will guide you through steps 2-5.

2. If you're upgrading from an earlier version of InnReachTracker, read the
   instructions in: documentation_upgrade_notes.php

3. Create a new MySQL database (perhaps with a name like "innreach" or 
"mydatabases_innreach";

4. Edit the config.php file to add values to each of the first 11 variables.
   (up through the $ptypes array).
   These variables define the names and locations of the local and INNReach
   catalogs, and give the names and passwords to connect to MySQL

5.   From the browser, run create_mysql_tables.php
   * creates the right size tables
   * loads some useful title data into innreach_titles_by_call

6. load up most recent month's data into the tables -- the script should 
   prompt the user if there is no data yet. There are more extensive 
   instructions for this in the file: documentation_how_to_add_circ_data.php. 

7. Once you've added circulation data, you'll be ready to start viewing your 
   circulation statistics and adding title information. These steps are 
   described in documentation_how_to_add_title_data.php. 
