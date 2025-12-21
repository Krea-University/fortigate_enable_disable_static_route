<?php
/**
 * Logout
 */
require_once 'session.php';
Session::logout();
header('Location: login.php');
exit;
