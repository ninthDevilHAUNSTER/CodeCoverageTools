<?php

require 'utils.php';

$conn = new mysqli(
    "websec", "root", "password", "code_coverage", "22306"
);

if (mysqli_connect_errno()) {
    error_log(sprintf("Connect failed: %s", mysqli_connect_error()));
    exit();
}


function search_coverage($file_name, $test_group)
{
    global $conn;
    $stmt = $conn->prepare("SELECT line_number FROM tests
         LEFT JOIN covered_files cf on tests.id = cf.fk_test_id
         LEFT JOIN covered_lines cl on cf.id = cl.fk_file_id
WHERE file_name = ?
  and test_group = ? "
    );
    $stmt->bind_param("ss", $file_name, $test_group);
    $stmt->execute();
    $result = $stmt->get_result();
    $rt = array();
    while ($row = $result->fetch_assoc()) {
//        print_r($row);
        array_push($rt, $row['line_number']);
    }
    $stmt->free_result();
    return $rt;
}


function get_file_coverage($test_group,$file, $visited_lines, &$TOTAL_FILE_LINE_CNT, &$TOTAL_COVERED_LINE_CNT, $kind = ParserFactory::PREFER_PHP5)
{
    system("D:\wamp\bin\php\php7.4.0\php.exe -c D:\wamp\php_box\debloating\custom.ini  $file "); # change statment here TODO RECHECK HERE
    if (file_exists("TEST.json")) {
        $var = json_decode(file_get_contents("TEST.json"), true);
    } else {
        echo "TEST.JSON not found when analyzing file $file";
        exit();
    }
    $result = $var[$file];
    $software_lines = [];
    foreach ($result as $key => $value) {
        $software_lines[] = $key;
    }
    $_real_visited_lines = array_intersect($software_lines, $visited_lines);
    $result_matrix = array(
        "software_line_cnt" => count($software_lines),
        "visited_line_cnt" => count($_real_visited_lines),
        "code_coverage" => round(count($_real_visited_lines) / count($software_lines), 2),
    );
    $TOTAL_FILE_LINE_CNT += $result_matrix['software_line_cnt'];
    $TOTAL_COVERED_LINE_CNT += $result_matrix['visited_line_cnt'];
    file_put_contents("$test_group.log", "FILE :: " . $file . "  CODE COVERAGE " . $result_matrix['code_coverage'] * 100 . "%" . PHP_EOL, FILE_APPEND);
    return true;
}

function get_total_coverage($source_root, $test_group, $ignore_dir = array())
{
    @unlink("$test_group.log");
    print "Analyzing " . $test_group . " with source root" . $source_root;
    $files = [];
    getDirContents($source_root, $files, ["php"], $ignore_dir);
    $total_lines = 0;
    $covered_lines = 0;
    foreach ($files as $file) {
        $visit_lines = search_coverage($file, $test_group);
        get_file_coverage($test_group,$file, $visit_lines, $total_lines, $covered_lines);
    }
    file_put_contents("$test_group.log", "Total software cnt :: " . $total_lines . PHP_EOL
        . "Total visited cnt ::  " . $covered_lines . PHP_EOL
        . "software coverage ::  " . $covered_lines / $total_lines . PHP_EOL, FILE_APPEND);
}

# get_total_coverage('D:\wamp\php_box\piwigo\Piwigo-2.8.3', 'piwigo283_robercrwaler_login_superadmin', ["_data\\", "language\\", "install\\"]);
// get_total_coverage('D:\wamp\mantisbt', 'mantisbt1215_robercrwaler_login_superadmin', ["install\\"]);

// get_total_coverage('D:\wamp\php_box\piwigo\Piwigo-2.9.2', 'piwigo292_robercrwaler_login_superadmin', ["_data\\", "language\\", "install\\"]);

$param_arr = getopt('d:g:');
echo "GET ARG INPUT";
print_r($param_arr);
get_total_coverage($param_arr['d'],$param_arr['g']);