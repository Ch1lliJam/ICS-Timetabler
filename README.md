# ICS-Timetabler

Todo:
- make it generate a random name for the cal_data for each user
- upload all data from the processed ical data
- allow a way for user to update multiple/all lectures

- Automatic download of new timetables for all users every set period of time
- (can use cron jobs, look into it)
- (need to check to see if timetable data has changed from last check such as new lectures, if so update, otherwise don't)

- Update ics_processor to write all processed data to database, need to provide
- user data to find correct location to upload data

- add anything else that might need work on

Howto test:
- login with an account
- then direct browser to test_ical_func.php