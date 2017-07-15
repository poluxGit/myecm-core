<?php

namespace Core\API;
/**
 * APIRequest Class File definition
 *
 * @package MyECM
 * @subpackage API_RESTful
 */
class APIRequest extends AbstractRequest {

  /**
   * Constructor: __construct
   */
  public function __construct($request, $origin)
  {
    $this->_loadRequest($request, $origin);
  }

  public function getFormattedRequestResponse(){
    $lArrResultRequest = [];
    $lArrResultRequest['INFO_REQUEST']['METHOD']      = $this->getHTTPMethod();
    $lArrResultRequest['INFO_REQUEST']['PARAMETERS']  = $this->getArgs();
    $lArrResultRequest['INFO_REQUEST']['ENDPOINT']    = $this->getEndpoint();
    $lArrResultRequest['INFO_REQUEST']['NBROWS']      = count($this->getData());
    $lArrResultRequest['DATA']                        = $this->getData();
    return json_encode($lArrResultRequest);
  }

  public function getSpecificFormattedRequestResponse($msg){
    $lArrResultRequest = [];
    $lArrResultRequest['INFO_REQUEST']['METHOD']      = $this->getHTTPMethod();
    $lArrResultRequest['INFO_REQUEST']['PARAMETERS']  = $this->getArgs();
    $lArrResultRequest['INFO_REQUEST']['ENDPOINT']    = $this->getEndpoint();
    $lArrResultRequest['DATA']                        = $msg;
    return json_encode($lArrResultRequest);
  }

}//end class
