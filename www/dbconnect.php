<?php

if(!mysql_connect("mysql","root","root"))
{
     die('oops connection problem ! --> '.mysql_error());
} 
if(!mysql_select_db("lcsportident_events"))
{
     die('oops database selection problem ! --> '.mysql_error());
}
?>
