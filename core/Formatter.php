<?php
class Formatter {
    protected $query;
    protected $content_type = null;
    
    public function __construct($query) {
        $this->query = $query;
    }
    
    protected function getHeader() {
        return '';
    }
    
    public function execute($result) {
        HeaderUtils::addHeader('Content-type', $this->query->getContentType(), true);
        if (is_array($result) && array_key_exists('http_code', $result)) {
            HeaderUtils::httpResponseCode($result['http_code']);
            unset($result['http_code']);
        } else {
            HeaderUtils::httpResponseCode(HeaderUtils::HTTP_OK);
        }
        $content_type = HeaderUtils::getHeader('Content-type');
        if ($content_type == 'application/json') {
            echo json_encode($result);
        } else {
            echo $result;
        }
    }
}
?>
