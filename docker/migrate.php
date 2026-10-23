<?php
define('YOURLS_ADMIN', true);
define('YOURLS_INSTALLING', true);

require_once(__DIR__ . '/includes/load-yourls.php');
require_once(YOURLS_INC . '/functions-install.php');

// Only install into an empty database. yourls_create_sql_tables() also resets version, db_version, next_id and
// active_plugins, which on an existing install would hide pending upgrades and deactivate plugins. Upgrades are
// left to YOURLS's own admin upgrade prompt.
if (!yourls_is_installed()) {
    yourls_create_sql_tables();
}
