# ICS-Timetabler

### Working on:

- add settings page to let user customise settings (such as images linked to module code, light/dark mode, etc)

- Re-implement processICSFile for each time user logs in, try adding all lectures that are the same day as current day, leave the comparing of time to the removeOldLectures function

### Todo:

- scraping algorithm to find the correct webpage for the specific module then direct user there

### Done:

- ~~make it generate a random name for the cal_data for each user~~

- ~~Update ics_processor to write all processed data to database, need to provide user data to find correct location to upload data ~~

- ~~fix processICSFiles, weirdly doesn't save lectures same day as current day~~

- ~~move all php to do with accessing the server into functions in functions.php, more secure~~ - I don't thing there's anything else that needs to be moved

- ~~allow a way for user to update multiple/all lectures~~ - probably not needed, will retain file but remove connections to it

- ~~Automatic download of new timetables for all users every set period of time (can use cron jobs, look into it) (need to check to see if timetable data has changed from last check such as new lectures, if so update, otherwise don't)~~

- ~~adding onto previous point, when doing this auto-update, check to see all new lectures that got added, and remove all lectures before the current day as not needed anymore.~~

last two points addressed by short term solution of downloading ics file each time user logs in, and checks if new lectures added in ics file then updating to database

- ~~limiting max lectures shown at once to 20, then allowing user to do 20 more etc (retrieve all and then javascript to only show 20, etc)~~

- ~~every time user refreshes page (so at top of view_lectures page, call the removeOldLectures function from the ics_processor.php so the user doesn't see old lectures)~~

- ~~weird deletion of lectures by the removeOldLectures function if the time is getting close to the next lecture, or if within lecture time (maybe fixed, check when near lectures/2 hour lectures)~~

### How to test ical functionality:
- remove all x.ics files
- login with a created account
- (check if 2 hour lectures are working correctly)

