<?php

// default PHP server can't properly manage URL with dot sign in its name
// the solution is http://stackoverflow.com/a/32098723/3167855

$_SERVER['SCRIPT_NAME'] = 'index.php';
include __DIR__ . '/index.php';