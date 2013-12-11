This is an examination of problem solving, logic development and how PHP is utilized.

Test Requirements:
  1) Create a single PHP file (demo.php)
  2) generate a valid xHTML 1.1 code based upon sample (sample.html)
  3) use the given JSON data file (sample.json)
  4) follow app requirements as defined below
  5) do not use external/third party libraries


Application Requirements:

The application must accept a single GET parameter, "cdate", with a format of "MM-DD-YYYY". If a date is not given, default to current date.

This inbound date will be used to determine the default display of the data.

The data is received via a JSON object (sample.json). Using this data, a collection of archived links are to be generated and displayed.

Only a single year at a time is to be seen by the user.

A SELECT Element, containing the YEAR in descending order, is to be used to allow the user to select which YEAR of archives they wish to view.

When a user selects a new YEAR from the SELECT Element, the display automatically changes to show that year’s list of archives without reloading the page.

Please re-create the link structure as defined in the sample.html file.
