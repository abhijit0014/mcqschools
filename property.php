<?php

// Database Dev
$G_DB_HOST = "localhost:3306";
$G_DB_NAME = "projectdb";
$G_DB_USER = "root";
$G_DB_PASS = "master";

// Database Prod
/*
$G_DB_HOST = "localhost";
$G_DB_NAME = "mcqschoolsdb";
$G_DB_USER = "mcqschoolsdbuser";
$G_DB_PASS = "kmp#2323@DB";
*/


// Host
//$G_HOST = "mcqschools.com";
$G_HOST = "localhost";

// Email
$G_EMAIL_NOREPLY = "noreply@mcqschools.com";
$G_EMAIL_CONTACT = "info@mcqschools.com";


// maximum exam user can add per day
$EXAM_ADITION_LIMIT_PER_DAY = 100;

// maximum question user can add per day
$QUESTION_ADITION_LIMIT_PER_DAY = 2000;

// maximum unsolved report limit
$UNSOLVED_REPORT_LIMIT = 5;

// number of result in category autocomplete
$CATEGORY_AUTOCOMPLETE_RESULT_LIMIT = 8;

// blocked categories
$BLOCKED_CATEGORIES = array("Others", "Online Mock Test");

?>