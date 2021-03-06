<html><head><title>Documentation for InnReachTracker</title></head>
<style>
.doc_nav ul { list-style: none; }
.doc_nav li, .doc_nav li a { 
 display: inline; 
 padding: .25em .5em .25em .5em; 
 background-color: green;
 color: white;
 font-weight: bold;
}
.doc_nav li a:hover {
  text-decoration: underline overline;
}
</style>

<?php
$doc_nav = '<ul class="doc_nav">
<li><a href="#install">Installation</a></li>
<li><a href="#change_log">Change Log</a></li>
<li><a href="#upgrade">Upgrading from v 1.0.1</a></li>
<li><a href="#add_circ">Add circ data</a></li>
<li><a href="#add_title">Add title data</a></li>
<li><a href="#license">License</a></li>
</ul>';
?>

<a name="install"></a>
<?php echo $doc_nav;?>
<h2>Installation</h2>
<pre>
<?php include ("readme.txt"); ?>
</pre>
<hr>
<a name="change_log"></a>
<?php echo $doc_nav;?>
<h2>Change Log</h2>
<pre>
<?php include ("changelog.txt"); ?>
</pre>
<hr>
<a name="upgrade"></a>
<?php echo $doc_nav;?>
<?php include ("documentation_upgrade_notes.php"); ?>
<hr>
<a name="add_circ"></a>
<?php echo $doc_nav;?>
<?php include ("documentation_how_to_add_circ_data.php"); ?>
<hr>
<a name="add_title"></a>
<?php echo $doc_nav;?>
<?php include ("documentation_how_to_add_title_data.php"); ?>
<hr>
<a name="license"></a>
<?php echo $doc_nav;?>
<?php include ("license.php");?>
<?php include("version.php");?>
