Readme.txt

NDB WEBLOG Version 1.1.27 - Released 21 Dec 2013

CONTENTS
There are two versions of this installation file:

  FULL INSTALLATION
  1) countries.js  Internal code for defining country codes (see http://www.beaconworld.org.uk/files/countrylist.pdf)
  2) functions.js  Internal code for the log system - don't try to change this either
  3) help.html     The 'Help' page - don't try to change this
  4) index.html    The 'Start' page - don't try to change this
  5) ndbweblog.css Styleshgeet which controls the way the page is formatted
  6) readme.txt    This text file

  The last three files can be obtained in two ways:
  - Use the ones supplied in this installation and adapt them to your needs
  - if you have loggings already uploaded at RNA (http://www.classaxe.com/dx/ndb/rna) or REU
    (http://www.classaxe.com/dx/ndb/reu) you can have these files generated for you by clicking on your name in the
    Listener list, selecting 'Export' and then choosing 'NDB WebLog files', and downloading replacements for these
    three files to your NDB WebLog directory.  For this version you can use the 1.1.25 and later file type.
  7) config.js     Modify for your own location - or download one if you have loggings at RNA or REU
  8) log.js        Contains loggings of reception of stations
  9) stations.js   Contains technical data on about 800 stations receivable in Ontario - add you own to these.

  UPGRADE
  As above but less the last three files - you already have these!


REQUIREMENTS
As of release 1.0.5, NDB logger files no longer need to be placed on a web server in order for the system to work -
you can run it straight from your hard drive (or even floppy disk) without any other software.
To publish your log, simply place the files in this package (once you have configured them and added your own data)
in the same directory on your web server. You don't need PHP, Perl or anything else, since all processing of data
is performed by the visitor's web browser.

As of version 1.1.0, all times and dates are to be entered as UTC.

As of version 1.1.3, countries.js is also required.

As of version 1.1.7, ndbweblog.css is also required.



CONFIGURING FOR YOUR OWN USE
The last three files in the full installation package contain information specific to your own log book.
You will need to edit them for your own use.
  * Change the details in config.js to reflect your own location, name and email address
  * Add technical details for stations you have received and are not already listed in stations.js
    (Please use NDB List approved standard country codes - see  for a list)
  * Record your own loggings in the file log.js.


WWSU 6.2
Alex Wiecek's excellent WWSU program integrates beautifully with NDB WebLog and contains a database of around 10,000
NDBs, DGPS and Navtex stations. I highly recomend using his program to record all of your loggings both at home and on the road.
The full version costs just $10 Canadian and even supports grouping of loggings from up to 20 different locations.



When you are finished, all the files need to be placed together on a web server in order to use
the system.   Then all you need to do is type in the address of the web site and path to the directory in order
to use it.

As you log new stations day by day, add to the contents of stations.js and log.js and then upload these two
files to the server to keep your data up to date.



NEW VERSIONS OF THE SYSTEM
Check back with the ndb log site at http://www.classaxe.com/dx for new versions of this system.

Please contact me at martin@classaxe.com if you use this system as I need to ensure compatibility for
existing users when I produce new versions of the system - otherwise don't blame me if your log data
fails to be compatible with newer releases of the software!

Also, if I know about your site, I can publish your site on the official NDB WebLog Links page -
see http://www.classaxe.com/dx/nbd/log/links


(Martin Francis)
