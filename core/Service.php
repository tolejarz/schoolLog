<?php
abstract class Service {
    protected $configuration;
    
    public function __construct($configuration) {
        $this->configuration = $configuration;
    }
    
    protected function log($file, $text) {
        $url = $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != '80' ? ':' . $_SERVER['SERVER_PORT'] : '') . $_SERVER['REQUEST_URI'];
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'unknown';
        $ip = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
        $r = file_put_contents($file, sprintf("%s\nMessage: %s\nReferer: %s\nPage: %s\nIP: %s\n\n", date('Y-m-d H:i:s'), $text, $referer, $url, $ip), FILE_APPEND);
        if ($r === false) {
            error_log(sprintf('Warning: %s is not writable.', $file));
        }
    }
    
    protected function remoteFileExists($url) {
        ini_set('allow_url_fopen', '1');
        return @fclose(@fopen($url, 'r'));
    }
}
?>
