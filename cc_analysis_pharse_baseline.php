<?php

require 'admin/vendor/autoload.php';
require 'utils.php';
require 'CommonLineVisitor.php';

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter;

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


function get_file_coverage($file, $visited_lines, &$TOTAL_FILE_LINE_CNT, &$TOTAL_COVERED_LINE_CNT, $kind = 2)
{
    $code = file_get_contents($file);
    $traverser = new NodeTraverser();
    $visitor = new CommonLineVisitor($file, $visited_lines);
    $traverser->addVisitor($visitor);
    $parser = (new ParserFactory())->create($kind);
    try {
        $ast = $parser->parse($code);
    } catch (\Error $error) {
        echo "$error" . PHP_EOL;
        return false;
    }
    $traverser->traverse($ast);
    $result_matrix = $visitor->getCodeCoverage();
    $TOTAL_FILE_LINE_CNT += $result_matrix['software_line_cnt'];
    $TOTAL_COVERED_LINE_CNT += $result_matrix['visited_line_cnt'];
    file_put_contents("result.log", "FILE :: " . $file . "CODE COVERAGE " . $result_matrix['code_coverage'] * 100 . "%" . PHP_EOL, FILE_APPEND);
    return true;
}

function get_total_coverage($source_root, $test_group, $ignore_dir = array())
{
    unlink("result.log");
    print "Analyzing " . $test_group . " with source root" . $source_root;
    $files = [];
    getDirContents($source_root, $files, ["php"], $ignore_dir);
    $total_lines = 0;
    $covered_lines = 0;
    foreach ($files as $file) {
        $visit_lines = search_coverage($file, $test_group);
        get_file_coverage($file, $visit_lines, $total_lines, $covered_lines);
    }
    file_put_contents("result.log", "Total software cnt :: " . $total_lines . PHP_EOL
        . "Total visited cnt ::  " . $covered_lines . PHP_EOL
        . "software coverage ::  " . $covered_lines / $total_lines . PHP_EOL, FILE_APPEND);
}

get_total_coverage('D:\wamp\php_box\piwigo\Piwigo-2.8.3', 'piwigo283_robercrwaler_login_superadmin', ["_data\\", "language\\", "install\\"]);