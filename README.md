# EnterPriseSoftDMS
My senior project for enterprise software engineering

In my final semester at UTSA I took Enterprise Software with Professor Valadez, this is the culmination of near 120-140 hours of work spread out over about a month. 

We began with creating a php file to query his API endpoint and get dummy data, and before going live with that we created a database schema to match that. 

Afterwards we moved towards having some logging functionality, transitioning from being able to create and delete sessions to then automating the query with CRON jobs.

From there we had to plan how to deal with trash data, server disconnects, server timeouts, connection timeouts, unresponsive APIs, essentially the gauntlet of DevOps problems things that our professor had to deal with in his professional career.

The way I did that was having the hourly request cronjob that was mandatory (properly formatted to log the time of request, amount of files retrieved, and any errors that occured) then also having another cronjob that would reboot the aws instance every three hours, 30 minutes before the next file request was set to go off. This was my workaround to not having more robust logging and systems put into place to deal with disconnects.

If I had to go back and do it again I would set up the system in a way that any time the system took more than 30 seconds to complete a query I would get notified via email or text thus being able to respond more dynamically. 

After our month long data collection period we had to go into our database and create our report of the total files we had, the total and average size of all files, the number of disconnects/timeouts, the type of file that we retrieved, sort all files by the type, and average how many of each of those files we had.

I did all of that through PHP and SQL calls weaved into the PHP. 

Then because I thought it was ugly and wanted a better UI and UX I used bootstrap css/js to make it both easier to read and easier to navigate. So instead of needing to have 8 different tabs open to see all of the reports you only needed one and you could click forwards and backwards through it.

This was done with no previous experience in PHP. Introductory SQL knowledge with a deeper understanding of HTML, CSS, and JS.
