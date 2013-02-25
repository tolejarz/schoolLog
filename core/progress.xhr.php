<?php
header('Content-type: text/plain; charset=UTF-8');

if (isset($_GET['progress_key'])) {
    $rep=apc_fetch('upload_'.$_GET['progress_key']);
    echo json_encode($rep);
    exit;
}
?>