<?php
class ResourceController extends Controller {
    public function doGet($args) {
        $filename = $args['resource_path'];
        
        $ext = explode('.', $filename);
        $ext = $ext[count($ext) - 1];
        if ($ext == 'css') {
            if (strpos($filename, '.custom')) {
                header('Content-type: text/css');
                include 'lib/_js/theme_jquery_ui/' . $filename;
                die();
            }
            header('Content-type: text/css');
            include 'templates/style.css';
            die();
        } elseif ($ext == 'png') {
            header('Content-type: image/png');
            $c = file_get_contents('templates/img/' . $filename);
            echo $c;
            die();
        } elseif ($ext == 'jpg') {
            header('Content-type: image/jpeg');
            $c = file_get_contents('templates/img/' . $filename);
            echo $c;
            die();
        } elseif ($ext == 'js') {
            header('Content-type: application/javascript');
            $c = file_get_contents('lib/_js/' . $filename);
            echo $c;
            die();
        }
    }
}
?>
