# projectTracker
Allows users to add projects, and update things such as status, various dates, notes, etc.

Description:
This was built from the ground up, by myself, running within a LAMP stack.
Project Tracker contains many cool features, a few of them are:
1) When a new project is added, the user can choose a complexity. tthis value determines how much time they are alloted for the project.    It will automatically calculate project dates base off of complexity.
2) When a project is 'on hold', i use a cron job to add 1 day to the project release date, for every day that the status is on hold.
3) Ive implemented a reprot that shows projects released within different time frames, and other data.
4) I created a report that shows how many days a project has been rejected, in testing, and on hold.
5) I connected to our bugilla database to show the total amount of bugs a project has. This functionality can be used for many other things in the future, such as, list a projet's bugs that are in 'need info' status.
6) The login page has a fun feature that shows reels, like a slot machine, it will show 'Winner' if you get 3 of a kind.
