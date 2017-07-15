<?php

namespace Core\API;
/**
 * APIRequest Class File definition
 *
 * @package Core
 * @subpackage API
 */

abstract class AbstractRequest {

    /**
     * The HTTP method this request was made in
     *
     * @example GET, POST, PUT or DELETE
     * @var string
     */
    protected $http_method = '';

    /**
     * Endpoint
     * The Model requested in the URI.
     *
     * @example  /files
     * @var string
     */
    protected $endpoint = '';

    /**
     * Property : initRequest
     *
     * Request initially submited.
     *
     * @var type
     */
    protected $initRequest = '';

    /**
     * Property: args
     *
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     */
    protected $args = [];

    /**
     * Property: file
     * Stores the input of the PUT request
     *
     * @var string
     */
    protected $file = null;

    /**
     * Property: fileContent
     * Stores the input of the PUT request
     *
     * @var string
     */
    protected $fileContent = null;

    /**
     * Property: fileName
     * Stores the input of the PUT request
     *
     * @var string
     */
    protected $fileName = null;

    /**
     * Property: fileName
     * Stores the input of the PUT request
     *
     * @var string
     */
    protected $fileType = null;

     /**
     * Property: origin
      *
     * Origin
      *
      * @var string
     */
    protected $origin = null;

    /**
     * Property: status
     *
     * Status about execution of the current request
     *
     * @var string
     */
   protected $status = null;

    /**
     * Request Data result
     *
     * @var mixed
     */
    protected $data_result = null;

    public function getEndpoint()
    {
      return $this->endpoint;
    }

    public function getArgs()
    {
      return $this->args;
    }


    public function getHTTPMethod()
    {
      return $this->http_method;
    }

    public function setData($data){
      $this->data_result = $data;
    }

    public function getData(){
      return $this->data_result;
    }


    /**
     * _loadRequest
     *
     * Initialize request object in memory.
     *
     * @param string $request             Complete request
     * @param string $origin              Origin of the request
     * @param string $request_separator   Optional - Request Seperator character (default '/')
     *
     * @internal use $_SERVER to identify method value
     */
    protected function _loadRequest($request, $origin, $request_separator = '/')
    {
        // initialize mandatory attributes.
        $this->origin = $origin;
        $this->args = explode($request_separator, rtrim($request, $request_separator));
        $this->initRequest = $request;
        $this->endpoint = array_shift($this->args);

        // Identify Method
        $this->http_method = $_SERVER['REQUEST_METHOD'];

        // Specific Case (DELETE or PUT) === POST + HTTP_X_HTTP_METHOD (with real method (PUT or DELETE))
        if ($this->http_method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->http_method = 'DELETE';
            } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->http_method = 'PUT';
            } else {
                throw new Exception("APIRequest - Unexpected Header - Method can't be found.");
            }
        }
        // Build Request!
        $this->_buildRequest();
        $this->status = 'INIT';

    }//end _loadRequest()

    /**
     * _buildRequest
     *
     * Build request in memory according the method.
     */
    private function _buildRequest()
    {
        switch ($this->http_method) {
            case 'DELETE':
            case 'POST':
                $this->request = $this->_cleanInputs($_POST);
                break;
            case 'GET':
                $this->request = $this->_cleanInputs($_GET);
                break;
            case 'PUT':
                // $lArr = $this->_parsePut();
                parse_str(urldecode(file_get_contents("php://input")), $lArrParams);
                $this->args = array_merge($this->args, $lArrParams);
                $this->file = file_get_contents("php://input");
                $this->_extractFilesInformationAndContentFromInputStream();
                break;
            default:
                throw new Exception("APIRequest - Invalid Method.");
                break;
        }
    }//end _buildRequest()


    /**
     * Send HTTP response
     *
     * @param mixed     $data       Data provided
     * @param intger    $status     HTTP Status Code
     *
     * @return mixed    HTTP Response
     */
    public function _response($status = 200)
    {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        return $this->getFormattedRequestResponse();
    }


    /**
     * Send HTTP response specific Content-Type
     *
     * @param mixed     $data               Data provided
     * @param string    $pStrContentType    Content Type
     * @param intger    $status             HTTP Status Code
     *
     * @return mixed    HTTP Response
     */
    protected function _responseSpecificType($data, $pStrContentType, $status = 200)
    {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: ".$pStrContentType);
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        return $this->getSpecificFormattedRequestResponse($data);
    }

    abstract function getFormattedRequestResponse();
    abstract function getSpecificFormattedRequestResponse($msg);


    /**
     * Returns Status Request
     *
     * @param string $code
     *
     * @return integer  HTTP Status Code
     */
    private function _requestStatus($code)
    {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code]) ? $status[$code] : $status[500];
    }

    // =========================================================================
    // Protected Tooling methods
    // =========================================================================

    /**
     * Prepare files data
     *
     * @param string $raw_putdata   Optional - All file informations managed by WebServer - if null, file property value is taken.
     */
    protected function _extractFilesInformationAndContentFromInputStream($raw_putdata=null)
    {
        $lStrFileAndHeaderData = (is_null($raw_putdata))?$this->file:$raw_putdata;
        if(!is_null($raw_putdata)){
          $this->file=$raw_putdata;
        }
        $this->fileContent = '';

        // Generate unique temporay filenames.
        $lStrInTmpFile = tempnam('/tmp', 'UPL-INTMPFILE_');
        $lStrOutTmpFile = tempnam('/tmp', 'UPL-OUTTMPFILE_');

        // Store Content in Tmp File
        file_put_contents($lStrInTmpFile, $lStrFileAndHeaderData);

        // TODO Optimisation - utiliser fgets

        // Reading line by lines to split ContentHeader and FileContent !
        $lArrlines = file($lStrInTmpFile);
        foreach ($lArrlines as $lIntNumber => $lStrlineContent) {
            // Ignoring first and end line
            if (intval($lIntNumber) !== 0 && intval($lIntNumber) !== (count($lArrlines)-1)) {
                // Identifying Content*
                if (strcmp(str_replace('Content', '', $lStrlineContent), $lStrlineContent) !== 0) {
                    // Content-Disposition: form-data; name="fileUpload"; filename="AppMainImage.png"
                    $lArrMatches = null;
                    $lStrResult = null;

                    $lStrPattern_File = '/filename=\"(.*?)\"/i';
                    preg_match($lStrPattern_File, $lStrlineContent, $lArrMatches);

                    // var_dump($lArrMatches);
                    // var_dump($lStrlineContent);

                    // Seeking filename !
                    if (count($lArrMatches)>1) {
                        $lStrResult = $lArrMatches[1];

                        // Filename founded!
                        if (!empty($lStrResult)) {
                            $this->fileName = $lStrResult;
                        }
                    }

                    // Content-Type: image/png
                    $lArrMatches = null;
                    $lStrResult = null;
                    $lStrPattern_ContentType = '/Content-Type: (.*\/?)/i';
                    preg_match($lStrPattern_ContentType, $lStrlineContent, $lArrMatches);

                    //var_dump($lArrMatches);

                    // Seeking filetype !
                    if (count($lArrMatches)>1) {
                        $lStrResult = $lArrMatches[1];
                        //print_r('fileType => '.$lStrResult);
                        // Filename founded!
                        if (!empty($lStrResult)) {
                            $this->fileType = $lStrResult;
                        }
                    }
                } else { // File content.
                    if (!empty($lStrlineContent)) {
                        $this->fileContent .= $lStrlineContent;
                    }
                }
            }//end if
        }//end foreach
    }//end _extractFilesInformationAndContentFromInputStream()

    /**
     * _parsePut
     *
     * Parse $_PUT data and php://input
     */
    private function _parsePut()
    {
      /* PUT data comes in on the stdin stream */
      $putdata = fopen("php://input", "r");
      $raw_data = '';

      /* Read the data 1 KB at a time
         and write to the file */
      while ($chunk = fread($putdata, 1024)) {
          $raw_data .= $chunk;
      }

      /* Close the streams */
      fclose($putdata);

      // Fetch content and determine boundary
      $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));
      if (empty($boundary)) {
            parse_str($raw_data, $data);
            $GLOBALS[ '_PUT' ] = $data;
            return;
      }

      // Fetch each part
      $parts = array_slice(explode($boundary, $raw_data), 1);
      $data = array();

      foreach ($parts as $part) {
        // If this is the last part, break
        if ($part == "--\r\n") {
          break;
        }

        // Separate content from headers
        $part = ltrim($part, "\r\n");
        list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

        // Parse the headers list
        $raw_headers = explode("\r\n", $raw_headers);
        $headers = array();
        foreach ($raw_headers as $header) {
          list($name, $value) = explode(':', $header);
          $headers[strtolower($name)] = ltrim($value, ' ');
        }

        // Parse the Content-Disposition to get the field name, etc.
        if (isset($headers['content-disposition'])) {
          $filename = null;
          $tmp_name = null;
          preg_match(
            '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
            $headers['content-disposition'],
            $matches
          );
          list(, $type, $name) = $matches;

          //Parse File
          if (isset($matches[4])) {
            //if labeled the same as previous, skip
            if (isset($_FILES[ $matches[ 2 ] ])) {
              continue;
            }
            //get filename
            $filename = $matches[4];
            //get tmp name
            $filename_parts = pathinfo($filename);
            $tmp_name = tempnam(ini_get('upload_tmp_dir'), $filename_parts['filename']);
            //populate $_FILES with information, size may be off in multibyte situation
            $_FILES[ $matches[ 2 ] ] = array(
                'error'=>0,
                'name'=>$filename,
                'tmp_name'=>$tmp_name,
                'size'=>strlen($body),
                'type'=>$value
            );
            //place in temporary directory
            file_put_contents($tmp_name, $body);
          }
          //Parse Field
          else {
              $data[$name] = substr($body, 0, strlen($body) - 2);
          }
        }
      }
      //$GLOBALS[ '_PUT' ] = $data;
      return $data;
    }//end _parsePut()

    /**
     * Cleaning Inputs
     *
     * @param mixed $data
     * @return mixed
     */
    protected function _cleanInputs($data)
    {
        $clean_input = array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }//end _cleanInputs()

}//end class
