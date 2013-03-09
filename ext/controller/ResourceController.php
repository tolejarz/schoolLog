<?php
class ResourceController extends Controller {
    public function doGet($args) {
        $filename = $args['resource_path'];
        
        $ext = explode('.', $filename);
        $ext = $ext[count($ext) - 1];
        if ($ext == 'css') {
            header('Content-type: text/css');
        } elseif ($ext == 'png') {
            header('Content-type: image/png');
        } elseif ($ext == 'jpg') {
            header('Content-type: image/jpeg');
        } elseif ($ext == 'js') {
            header('Content-type: application/javascript');
        }
        $c = file_get_contents('public/' . $filename);
        echo $c;
    }
}
?>
