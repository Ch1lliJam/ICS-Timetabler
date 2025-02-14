<?php
//this is where the magic, the connection to database happpens
//it is such a pain to connect to a database
//REMEMBER THAT IF CONNECTING TO MULTIPLE TABLES IN DATABASE, AND PASSING DATA THROUGH MULTIPLE, THAT THEY 
//NEED TO BE SAME LENGTH OR LONGER
//I DO NOT WANT TO SPEND ANOTHER 4 HOURS TROUBLESHOOTING MY CODE BECAUSE DATA OVERFLOW
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "login_sample_db";

$con = mysqli_connect("localhost", "root", "", "login_sample_db");
if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect");
}

?>