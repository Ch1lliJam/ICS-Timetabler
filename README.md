# ICS-Timetabler

Todo:

- ~~make it generate a random name for the cal_data for each user~~

- ~~Update ics_processor to write all processed data to database, need to provide user data to find correct location to upload data ~~

- ~~fix processICSFiles, weirdly doesn't save lectures same day as current day~~

- move all php to do with accessing the server into functions in functions.php, more secure

- ~~allow a way for user to update multiple/all lectures~~ - probably not needed, will retain file but remove connections to it

- ~~Automatic download of new timetables for all users every set period of time (can use cron jobs, look into it) (need to check to see if timetable data has changed from last check such as new lectures, if so update, otherwise don't)~~

- ~~adding onto previous point, when doing this auto-update, check to see all new lectures that got added, and remove all lectures before the current day as not needed anymore.~~

- last two points addressed by short term solution of downloading ics file each time user logs in, and checks if new lectures added in ics file then updating to database

- scraping algorithm to find the correct webpage for the specific module then direct user there




How to test ical functionality:
- login with a created account

