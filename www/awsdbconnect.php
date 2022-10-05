<?php


if(!mysql_connect("ces.cyjhywszjezw.us-east-1.rds.amazonaws.com","cesuser","wvG-Tkd-huo-72S"))
{
     die('oops connection problem ! --> '.mysql_error());
}
if(!mysql_select_db("ces"))
{
     die('oops database selection problem ! --> '.mysql_error());
}
?>