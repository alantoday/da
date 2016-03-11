<?php
define(PATH,"/home/digital/public_html/da/");
define(MEMBERS_PATH, PATH."my/");
define(ADMIN_PATH, PATH."admin/");

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

define("FROM_NOREPLY", SYSTEM_NAME." <noreply@".DOMAIN_NAME.">");  // Split Second <splitsecondsystem.com>

define("COPYRIGHT", "&copy; Copyright ". SYSTEM_NAME." ".date("Y").". All rights reserved.");  // 
define("INFUSIONSOFT_FORMID_THESFM", "e2115bafc80e942016198045c489f94d");
