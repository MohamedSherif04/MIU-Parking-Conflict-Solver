<?php

// App Root
define('APPROOT', dirname(dirname(__FILE__)));

// URL Root (Dynamic detection for XAMPP subfolders)
// This attempts to detect the path to the public folder.
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script = $_SERVER['SCRIPT_NAME'];
$path = dirname($script);

// Ensure no trailing slash
$path = rtrim($path, '/\\');

define('URLROOT', $protocol . '://' . $host . $path);

// Log Root
define('LOGROOT', APPROOT . '/../logs');

// Site Name
define('SITENAME', 'MIU Parking Conflict Solver');
