<?php

// Centralized Database connection parameters for deployment.
// Values are read from environment variables when available (set these in your hosting control panel,
// e.g. InfinityFree does not expose env vars, so edit the fallback defaults below to match your database).
// Variable names follow the conventions already used across the project ($host/$username/$password/$database
// and $servername/$dbname) so existing connection code continues to work unchanged.

$host = getenv('DB_HOST') ?: 'localhost';      // Database server (e.g. sqlXXX.main-hosting.eu)
$username = getenv('DB_USER') ?: 'root';       // Database username
$password = getenv('DB_PASS') ?: 'admin';      // Database password
$database = getenv('DB_NAME') ?: 'bte';        // Database name

// Aliases kept for files that use the other naming style
$servername = $host;
$dbname = $database;
$db_user = $username;
$db_pass = $password;
$db_name = $database;

?>
