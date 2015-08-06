<?
ShowDataLoadInstructions(); 

function ShowDataLoadInstructions () {
include ("config.php");
?>
<h1>How to get data from you catalog into InnreachTracker</h1>
<ol>
 <li>Go to your catalog's <a href="<?=$III_manage;?>">Web Management Reports</a>.</li>
 <li>Select: Collection >> Collection Development.</li>
 <li>Log in with your initials and password.</li>
 <li>Select: 
<ul>
<li>Report: INN-Reach Circulation</li>
<li>Inn-Reach Circ: Trans by TITLE</li>
<li>Report: Title</li>
<li>Type: Ptype</li>
<li>Date Range: Last Month</li>
<li>Limit by: System</li>
<li>Click: Submit</li>
</ul>

 </li>

 <li>Select all results in main frame; copy and paste them into an Excel spreadsheet (or similar).</li>
 <li>In Excel, start an additional column on the right side of the spreadsheet and enter into it the last day of the previous month, e.g. 9/30/2008.</li>
 <li>Change the date to yyyy-mm-dd format (e.g. 2008-09-30). To do this in Excel, highlight the cell and right-click => Format Cells
<ul>
<li>Category: Date</li>
<li>Locale (location) pulldown: English (U.K.)</li>
<li>Type: 2001-03-14 (or other yyyy-mm-dd formatted date)
</ul>
 </li>
 <li>Having properly formatted the date in the first cell of the column, copy and fill down so that same value is applied to all rows in that column:
<ul>
<li>Highlight the first cell.</li>
<li>Ctrl-C to copy the cell.</li>
<li>Highlight down from the first cell to the last row of the column with data in it.</li>
<li>Ctrl-D to fill-down.</li>
        </ul>
 </li>
 <li>Now delete the non-tabular information at the top of the spreadsheet, such as table headers and dates.</li>
 <li>Also delete the summary information at the bottom of the table.</li>
<li>Save the file as Tab-delimited Text. Save it someplace where you'll find it again. I often save the file with a name like "ibt_sept08.txt" for "innreach by title".</li>
 <li>Go back to step 4 and repeat the process for "Report: Call #" instead of "Report: Title". You might save the resulting file as "ibc_sept08.txt".</li>
 <li>Having saved the monthly data exported from III, you now need to load it into the MySQL tables <code>innreach_by_call</code> and <code>innreach_by_title</code>. There are a few ways to do this.
    <ol>
<li>The simplest way is to use the <strong><a href="upload_data.php">Upload Data</a></strong> function. To use this function, you will have to make sure that the <code>$allow_uploads</code> variable is set to <code>true</code> and that you have an <code>$Upload_Folder</code> defined in the <strong>config.php</strong> file AND your web server will have to have write permissions for the $Upload_Folder directory. You may need to ask your system administrator for help in granting those permissions.</li>

 <li>If you have access to a web interface to MySQL (like phpMyAdmin), you might upload these files in a fashion something like this:
<ul>
 <li>Select <strong>my_database</strong> from the list of databases.</li>
  <li>Select the <strong>innreach_by_call</strong> database from the left-hand pane.</li>
 <li>At the bottom of the "Structure" tab (or in some versions of phpMyAdmin, at the bottom of the "Insert" tab) click on the link that says "Insert data from a textfile into table".</li>
 <li>Using the form displayed:
 <ul>
 <li>locate the file to upload</li>
 <li>Make sure that the "replace table data" checkbox is UNCHECKED</li>
 <li>Fields terminated by: \t</li>
 <li>Fields enclosed by: (none - make sure this is empty)</li>
 <li>Fields escaped by: \</li>
 <li>Lines terminated by: \n<br>(if that doesn't work, try \r instead)</li>
<li>Click Submit</li>
<li>Repeat these steps to upload data to the innreach_by_title table as well.
</ul>
 </li>
        </ul>
    </li>
    <li>If you have command-line access to MySQL through a login-shell on your server, you may use a series of commands something like this once you've logged in to mysql:
				 <ul>
				 <li><code>use my_database</code></li>
				 <li><code>load data infile '/path/to/file/ibt_sept08.txt'<br>
				 into table innreach_by_title<br>
				 fields terminated by '\t' lines terminated by '\n';</code></li>
				 <li><code>load data infile '/path/to/file/ibc_sept08.txt'<br>
				 into table innreach_by_call<br>
				 fields terminated by '\t' lines terminated by '\n';</code></li>
				 </ul>
    </li>
    </ol>
				 </li>
</ol>
<?

				 } //end function ShowDataLoadInstructions
?>
