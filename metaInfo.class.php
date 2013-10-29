<?php
/**
 * @author Rohit Choudhary <rohitatmail@yahoo.com>
 * @Description Class to process the URL to get the meta and othe information required by the application flow
 * @copyright (c) Opensource
 * @version 1.0
 * 
 */



class metaInfo {

    //hold the metakeys values
    protected $_metaKeys;
    //hold the metadescription values
    protected $_metaDesc;
    //hold the metatitle values
    protected $_metaTitle;
    //hold the remote IP values
    protected $_remoteIp;
    //hold the loadtime of website values
    protected $_loadTime;
    //hold the response status values
    protected $_httpStatus;
    //hold the internal links in array form
    protected $_internalLinks;
    //hold the external links in array form
    protected $_externalLinks;
    //URL whose information is to be get
    protected $_url;
    //Curl response 
    public $curlResponse = true;
    //Curl error 
    public $curlError;

    //Default constructor
    
    public function __construct($url) {

        $this->_url = $url;
        $data = $this->file_get_contents_curl();
        if ($data['curlResponse'] === true) {
            $this->_remoteIp = $data['ip'];
            $this->_loadTime = $data['loadTime'];
            $this->_httpStatus = $data['httpStatus'];
            $this->_internalLinks = $data['links']['internal'];
            $this->_externalLinks = $data['links']['external'];

            $doc = new DOMDocument();
            $doc->loadHTML($data['html']);

            $titleNode = $doc->getElementsByTagName('title');
            $this->_metaTitle = $titleNode->item(0)->nodeValue;

            $metas = $doc->getElementsByTagName('meta');
            for ($i = 0; $i < $metas->length; $i++) {
                $meta = $metas->item($i);
                if ($meta->getAttribute('name') == 'Description')
                //$meta->getAttribute('content');
                    $this->_metaDesc = $meta->getAttribute('content');
                if ($meta->getAttribute('name') == 'Keywords')
                //echo $meta->getAttribute('content');
                    $this->_metaKeys = $meta->getAttribute('content');
            }
        }else {
            $this->curlResponse = $data['curlResponse'];
            $this->curlError = $data['curlError'];
        }
    }

    /**
     * @author Rohit Choudhary <rohitatmail@yahoo.com>
     * @Description To check whether curl in enabled or not in the running system
     * @return boolean
     */
    
    public static function iscurl() {
        if (function_exists('curl_version'))
            return true;
        else
            return false;
    }

    /**
     * @author Rohit Choudhary <rohitatmail@yahoo.com>
     * @Description To check whether passed url is valid or not
     * @return boolean
     */
    public static function validateUrl($url) {        
        if(filter_var($url, FILTER_VALIDATE_URL)){
            return true;
        }else{
            return false;
        }
    }
    

    /**
     * @author Rohit Choudhary <rohitatmail@yahoo.com>
     * @Description To get the details of the url by curl
     * @return mixed
     */
    private function file_get_contents_curl() {
        $data = array();

        if ($this->iscurl()) {
            $ch = curl_init();
            $wrapper = fopen('php://temp', 'r+');
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_STDERR, $wrapper);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_URL, $this->_url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLINFO_TOTAL_TIME);

            if (curl_exec($ch) === false) {
                $data['curlResponse'] = false;
                $data['curlError'] = curl_error($ch);
            } else {
                $data['html'] = curl_exec($ch);
                $realUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                $host = parse_url($realUrl, PHP_URL_HOST);
                $data['ip'] = gethostbyname($host);
                $data['loadTime'] = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
                $data['httpStatus'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $data['links'] = $this->sortLinks($data['html'], $host);
                $data['curlResponse'] = true;
            }
            curl_close($ch);
        } else {
            $data['curlResponse'] = false;
            $data['curlError'] = 'Curl is not enabled';
        }


        return $data;
    }
    
    
    /**
     * @author Rohit Choudhary <rohitatmail@yahoo.com>
     * @Description To sort the links  found in the website, as external and internal links
     * @return array
     */

    private function sortLinks($curlResult, $host) {

        $hrefLinks = array();
        $regex = '|<a.*?href="(.*?)"|';
        preg_match_all($regex, $curlResult, $parts);
        $links = $parts[1];
        foreach ($links as $link) {
            //echo $link."<br>";
            if (preg_match('/#/', $link)) {
                continue;
            } else {
                $parsedHost = parse_url($link, PHP_URL_HOST);
                if ($parsedHost === NULL) {
                    $hrefLinks['internal'][] = $link;
                } else if ($host === $parsedHost) {
                    $hrefLinks['internal'][] = $link;
                } else {
                    $hrefLinks['external'][] = $link;
                }
            }
        }

        return $hrefLinks;
    }

    
    /**
     * @author Rohit Choudhary <rohitatmail@yahoo.com>
     * @Description To return the meta description
     * @return string
     */
    public function getMetaDescription() {
        return $this->_metaDesc;
    }

    /**
     * @author Rohit Choudhary <rohitatmail@yahoo.com>
     * @Description To return the meta keyword
     * @return string
     */
    public function getMetaKeyWords() {
        return $this->_metaKeys;
    }

    /**
     * @author Rohit Choudhary <rohitatmail@yahoo.com>
     * @Description To return the meta Title
     * @return string
     */
    public function getMetaTitle() {
        return $this->_metaTitle;
    }

    /**
     * @author Rohit Choudhary <rohitatmail@yahoo.com>
     * @Description To return the remote IP
     * @return string
     */
    public function getRemoteIp() {
        return $this->_remoteIp;
    }

    /**
     * @author Rohit Choudhary <rohitatmail@yahoo.com>
     * @Description To return the Load time
     * @return string
     */
    public function getLoadTime() {
        return $this->_loadTime;
    }
    
    /**
     * @author Rohit Choudhary <rohitatmail@yahoo.com>
     * @Description To return the Status code
     * @return string
     */
    public function getHttpStatusCode() {
        return $this->_httpStatus;
    }

    /**
     * @author Rohit Choudhary <rohitatmail@yahoo.com>
     * @Description To return the all internal links
     * @return array
     */
    public function getInternalLinks() {
        return $this->_internalLinks;
    }

    /**
     * @author Rohit Choudhary <rohitatmail@yahoo.com>
     * @Description To return the all external links
     * @return array
     */
    public function getExternalLinks() {
        return $this->_externalLinks;
    }

}