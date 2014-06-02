<? include("config.php"); ?>
<head><title>How to add title information to circ data</title></head>
<link href="pcirc_style.css" rel="stylesheet" type="text/css">
<h1>How to add title information to circ data</h1>

<p>The data we export from III's catalog includes both title and call number information, but does not correlate the two. InnReachTracker uses the call number as the main identifier for each item. There are several ways we can associate title information with the call numbers to make this data more meaningful.</p>

<h2>Item Views</h2>
<p>The main InnReachTracker display has two tabs: "Show Individual Titles" and "Bulk Title Add".</p>
<ol><li><b>Show Individual Titles</b> shows all items that match the current call number class and minimum number of circulations; it shows titles for known items, and will allow the user to add title information for one item at a time. This view includes three tools for looking up call numbers; there are advantages and disadvantages to each.</li>
<li><b>Bulk Title Add</b> shows only items that have no title information associated with them. This form allows the user to add titles for multiple titles at once. Known titles are <em>not</em> displayed in this view. The same title lookup tools available in "Show Individual Titles" are available in this view as well.</li>
</ol>

<h2>Title Lookup Tools</h2>

<img src="images/indiv_title_display_labels.png" title="Labelled components of the Individual Title display">

<p>Here are some tools you can use to look up title information. I suggest using the title + statement of responsibility as the information you enter into the database for each title, e.g. <cite>Existentialism : basic writings / edited, with introductions, by Charles Guignon and Derk Pereboom</cite>.</p>
<ul>

    <li><b>The Innreach Catalog link</b> <?=$check_innreach_icon;?>: This icon is a link to a call number search for the item in the consortial catalog defined in <code>config.php</code>. The linked search will open in a new tab or window -- note: after your first link to the catalog, your browser may not change focus to the updated catalog window. (By default, this is the OhioLINK icon; if your library belongs to another consortium, you may change the icon in the config file.) This is often the simplest and most accurate way to lookup information, but it does not always works, especially if the call number is not defined in the MARC 050 field for the record of the item in question.</li>
     <li><b>The Google search link</b> <?=$google_icon?>: A sloppy but often effective tool, the Google link searches for the call number as a phrase in Google; this often turns up a list of books on a library website including the book in question. When using this method, it is advisable to confirm the book title in your own consortial catalog to be sure of the correct title/call# match.</li>
     <li><b>The Call Number link / Title History lookup</b>: Clicking on the call number (title history lookup) will open an inline pane in the current window. That pane (see below) will suggest possible titles based on matching dates of circulation for various titles with the dates of circulation for the selected call number. Sometimes this works very well, especially when there are at least 3 circulation instances to match on. This tool is generally not suitable for matching items with only one or two circulations.<br><img src="images/inline_lookup_pane.png" title="Inline Lookup Pane"></li>
</ul>
</li>

<h2>Local Holdings Information</h2>

<p>You can add local holdings information for items using <a href="pcirc_holdings.php">pcirc_holdings.php</a>. This script takes known titles and links to a title search in your local catalog to check holdings. For each title, you may add any of several entries:</p>
<table>
<tr><th>Y</th><td>Yes, for titles held locally. These titles will be marked wit the "have" icon defined in <code>config.php</code><br><?=$have_icon;?></td></tr>
<tr><th>N</th><td>No, for titles not held in any form. These titles will be marked with the "no have" icon defined in <code>config.php</code><br><?=$nohave_icon;?></td></tr>
<tr><th>Any other notes, e.g.<br>ebook<br>1992 ed.</th><td>All notes other than Y/N will be treated as a modified form of "Yes" and will render like this:<br><img src="images/holdings_sortof.png" title="Approximate Holdings"></td></tr>
</table>

<p>Known titles for which holdings are unknown will be marked with a question-mark icon (also defined in <code>config.php</code>: <?=$question;?> This icon is a link to the <code>pcirc_holdings.php</code> page.</p>