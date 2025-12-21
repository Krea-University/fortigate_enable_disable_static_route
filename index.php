<?php
/**
 * Index - Redirect to dashboard or login
 */
require_once 'session.php';

if (Session::isAuthenticated()) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
