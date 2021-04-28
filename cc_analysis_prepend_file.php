<?php

// script

xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);

function shutdown_ashd9va()
{
    // Registering shutdown function inside shutdown function
    // is a trick to make this function be called last!
    register_shutdown_function('shutdown_kdnw92j');
}

function shutdown_kdnw92j()
{
    end_coverage_cav39s8hca(True);
}

function end_coverage_cav39s8hca($caller_shutdown_func = False)
{
    $current_dir = __DIR__;
    $test_name = (isset($_COOKIE['test_name']) && !empty($_COOKIE['test_name'])) ? htmlspecialchars($_COOKIE['test_name'], ENT_QUOTES, 'UTF-8') : 'unknown_test_' . time();
    $fk_software_id = (isset($_COOKIE['software_id']) && !empty($_COOKIE['software_id'])) ? intval($_COOKIE['software_id']) : -1;
    $fk_software_version_id = (isset($_COOKIE['software_version_id']) && !empty($_COOKIE['software_version_id'])) ? intval($_COOKIE['software_version_id']) : -1;
    $test_group = (isset($_COOKIE['test_group']) && !empty($_COOKIE['test_group'])) ? htmlspecialchars($_COOKIE['test_group'], ENT_QUOTES, 'UTF-8') : 'default';

    if ($test_group == 'default') {
        // Try to read values from .htaccess
        $cfg_test_group = getenv('lim_test_group');
        $cfg_test_name = getenv('lim_test_name');
        $cfg_fk_software_id = getenv('lim_software_id');
        $cfg_fk_software_version_id = getenv('lim_software_version_id');
        if (isset($cfg_test_group)) {
            $test_group = $cfg_test_group;
        }
        if (isset($cfg_test_name)) {
            $test_name = $cfg_test_name;
        }
        if (isset($cfg_fk_software_id)) {
            $fk_software_id = $cfg_fk_software_id;
        }
        if (isset($cfg_fk_software_version_id)) {
            $fk_software_version_id = $cfg_fk_software_version_id;
        }
    }
    $dt = new DateTime("now", new DateTimeZone("Asia/Shanghai"));
    $coverageName = "TEST";
    try {
        $codecoverageData = json_encode(xdebug_get_code_coverage());
        if ($caller_shutdown_func) {
            xdebug_stop_code_coverage(); // true to destroy in memory information, not resuming later
        }
        file_put_contents($coverageName . '.json', $codecoverageData);
    } catch (Exception $ex) {
        error_log($ex);
    }
}

register_shutdown_function('shutdown_ashd9va');
error_log('registered shutdown_ashd9va as shutdown function');
