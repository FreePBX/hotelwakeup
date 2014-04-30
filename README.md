<h2>FreepBX Module - Wakeup Calls</h2>

This is a beta version (2.11.4 betaX) of Wake Up Calls module for FreePBX. This version has a number of improvements over 2.11.3 and is ready for testing by following the instructions below.

<h3>Install Instructions:</h3>

* From the Linux CLI, first remove any old existing version:
```
amportal a ma uninstall hotelwakeup
amportal a ma delete hotelwakeup
```

* Clone the most most recent beta from this repo:
```
cd /var/www/html/admin/modules
git clone https://github.com/lgaetz/Hotel-Style-Wakeup-Calls.git hotelwakeup
chown -R asterisk:asterisk hotelwakeup
```
In FreePBX, go to Module Admin, locate Wakeup Calls in the list and install like normal. Report bugs here or on the PIAF forum. Ignore any automatic notices from FreePBX to upgrade, that will replace your beta version with the older version.


* Any time you want to incorporate the most recent changes on github, update your beta install to the most recent version:
```
cd /var/www/html/admin/modules/hotelwakeup
git pull
chown -R asterisk:asterisk .
```

* If you need to abandon the beta version and revert to the supported version:
```
amportal a ma uninstall hotelwakeup
amportal a ma delete hotelwakeup
amportal a ma download hotelwakeup
amportal a ma install hotelwakeup
```

<h3>Introduction</h3>
Version 2.11.4 of the Wake Up Calls module has been significantly changed to cater for current enhancements and future ones. The main internal change has been that call schedules created via the GUI are now stored in a database table rather than as saved call files. The main enhancement has been to allow for repeating alarms - initially implemented as repeating Daily or Weekly.  Previously it was only possible to set up one-off alarms.

Frequency can now be selected as either 'Once' (default), 'Daily' or 'Weekly'. Daily will result in the alarm being repeated every day at the same time till it is deleted.  The Weekly one will similarly repeat on the same day of the week. Calls set up by using the phone (feature code *68) continue to be saved as call files and do not appear in the database.

Using the phone, if call files already exist for the extension, then they are now listed individually with an option to keep or delete. The GUI screen structure basically remains unchanged with the top section allowing for the creation of a new schedule, the middle showing existing schedules and the bottom allowing the system-wide configuration to be changed.

The middle section now shows existing schedules in two separate lists. The first table lists all the existing call files.  The second lists all existing schedules saved in the database.  If there are no schedules in the database then the second table is not shown.

<h3>Method of Operation:</h3>

There is a cron job set up that runs every hour at one minute before the hour to initiate a scan process.

This scan process extracts from the database all alarms that are due within the next 24-25 hours, writes a call file for it, and then updates the database record to move to the next scheduled time.

So the GUI will thus show scheduled alarms split between the two tables.

The top one will show all phone generated alarms, whether due shortly or any time in the future.  It will also show scheduled alarms originating from the database, once they become due within the next 24-25 hours.  So to see alarms due within the next 24 hours, or due at any time if set up via the phone, always look at the top table.

The 2nd table shows any future schedules stored in the database.

Example of an alarm scheduled via the GUI:

Today is Monday week 1.  Schedule an alarm for 6 am next Wednesday, repeating Weekly.  It will appear in the 2nd table as due 6 am Wed week 1.

At 5:59 am on Tuesday the alarm will appear in table one and the entry in table 2 will now show the next schedule at 6 am Wednesday week 2.

At 6 am Wednesday the alarm call will be made and the entry in table one will disappear (subject to delays to due to snoozing)

Changing an alarm:

It is not possible to change an existing alarm, whether in table one or table two.  If an alarm is wrong it must be deleted using the Delete button on the appropriate line, and a new one set up as necessary.

Skipping the next scheduled alarm:

If you do not want the next alarm to go off for a particular future schedule (today is Monday and you do not want the next Wednesday schedule) but you do want schedules after that to continue, then do the following:

Click the Generate button against the relevant schedule.  This will generate in advance the call file so it appears in table one and it will move the schedule on to the next one.  You can then delete the generated call file from table one and leave the schedule in table two now showing the period after the deleted one.

If you want to ship more than one week then just repeat the above as often as necessary.

Example of wake up calls for a week:

To get up at a fixed time on all seven days of the week, just create a single 'Daily' alarm.

But most people do not get up at the same time on all seven days.  Typically, Monday to Friday will be the same while Saturday and Sunday will be different.

The solution is to set up for each day of the week a different 'Weekly' entry, each set to the relevant time.  Once set up it will continue for ever.  To allow for days off and the like, just use the 'skipping' instructions above.

Further Enhancements:

We have some ideas for further improvements but would rather hear from users what they would like.

<h3>License</h3>
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
