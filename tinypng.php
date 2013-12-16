<?php

/**
 * TinyPNG Class
 * 
 * @author Michael Donaldson
 * @version 0.1.0
 * @copyright Copyright (c), Michael Donaldson. All rights reserved.
 * @license MIT
 */
class TinyPNG
{
  // API Endpoint
  const API_ENDPOINT = "https://api.tinypng.com/shrink";
  
  /**
   * Do we need to use the cacert.pem file?
   *
   * @var boolean
   */
  private $use_pem;

  /**
   * API Key for TinyPNG
   *
   * @var string
   */
  private $api_key;
  
  /**
   * Instance of cURL
   *
   * @var resource
   */
  private $curl;
  
  /**
   * Constructor
   *
   * @param string $api_key API Key for TinyPNG
   * @param boolean $use_pem If you are having issues connecting to the API endpoint, set this option to true
   */
  function __construct($api_key, $use_pem = false)
  {
    // Set API Key
    $this -> api_key = $api_key;
    // Are we using the pem file?
    $this -> use_pem = $use_pem;
    // Init cURL
    $this -> curl = curl_init();
  }
  
  /**
   * Compress
   *
   * Runs through the specified images, sending a request to TinyPNG to compress each image. The response for
   * each request is then stored in an array and returned upon completion of the final request.
   *
   * @param string|array $input This can be either a single path name or an array of input/output path name pairs
   * @param string $output This should be a single path name or left blank if an array was provided to $input
   *
   * @return string|array Returns a single error or an array of responses received from TinyPNG
   */
  public function compress($input, $output = "")
  {
    // Check that at least one image has been provided
    if($input !== null){
      // Single or Multiple images?
      if(is_array($input)){
        // Multiple images
        $response = array();
        // Loop through images, running compression on each
        foreach($input as $in => $out)
        {
          // Push result into 'response' array
          $response[$in] = $this -> compressImage($in, $out);
        }
        // Return response
        return $response;
      } else {
        // Single image
        if($output !== null){
          return $this -> compressImage($input, $output);
        } else {
          return "Invalid output path provided.";
        }
      }
    } else {
      return "Invalid input path provided.";
    }
  }
  
  /**
   * Compress Image
   *
   * This is a private function and is called by the 'compress' function to send a request to TinyPNG
   * to compress the specified image, if the request is successful the image will be saved in the
   * location defined in $output.
   *
   * @param string $input This should be a single path name to the image that is to be compressed
   * @param string $output This should be a single path name to where the compressed image should be saved
   *
   * @return array Returns the response from TinyPNG
   */
  private function compressImage($input, $output)
  {
    // Set cURL options
    curl_setopt_array($this -> curl, array(
      CURLOPT_URL             => self::API_ENDPOINT,
      CURLOPT_USERPWD         => "api:" . $this -> api_key,
      CURLOPT_POSTFIELDS      => file_get_contents($input),
      CURLOPT_BINARYTRANSFER  => true,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_HEADER          => true,
      CURLOPT_SSL_VERIFYPEER  => true
    ));

    // Check if we need to use the pem file
    if($this -> use_pem){
      curl_setopt($this -> curl, CURLOPT_CAINFO, __DIR__ . "/cacert.pem");
    }
    
    // Execute request
    $response = curl_exec($this -> curl);
    
    // Extract response body
    $headsize = curl_getinfo($this -> curl, CURLINFO_HEADER_SIZE);
    $body = json_decode(substr($response, $headsize), true);
    
    // Check to see if the request was successfull
    if(curl_getinfo($this -> curl, CURLINFO_HTTP_CODE) === 201){
      // Success, get headers
      $headers = substr($response, 0, curl_getinfo($this -> curl, CURLINFO_HEADER_SIZE));
      // Loop through headers, look for the location of the compressed image
      foreach (explode("\r\n", $headers) as $header) {
        if (substr($header, 0, 10) === "Location: ") {
          // Get file
          $file = file_get_contents(substr($header, 10));
          // Save file
          file_put_contents($output, $file);
          // Return
          return $body;
        }
      }
      // Didn't find header, error
      return "Sorry, an unexpected error has occurred.";
    } else {
      // Error, return body (body could contain the reason for the error)
      return $body;
    }
  }
}