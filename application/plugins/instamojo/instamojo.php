<?php

class Instamojo {
    const version = '1.1';

    protected $curl;
    protected $endpoint = 'https://www.instamojo.com/api/1.1/';
    protected $api_key = null;
    protected $auth_token = null;

    /**
    * @param string $api_key
    * @param string $auth_token is available on the d
    * @param string $endpoint can be set if you are working on an alternative server.
    * @return array AuthToken object.
    */
    public function __construct($api_key, $auth_token=null, $endpoint=null) 
    {
        $this->api_key = (string) $api_key;
        $this->auth_token = (string) $auth_token;
        if(!is_null($endpoint)){
            $this->endpoint = (string) $endpoint;   
        }
    }

    public function __destruct() 
    {
        if(!is_null($this->curl)) {
            curl_close($this->curl);
        }
    }

    /**
    * @return array headers with Authentication tokens added 
    */
    private function build_curl_headers() 
    {
        $headers = array("X-Api-key: $this->api_key");
        if($this->auth_token) {
            $headers[] = "X-Auth-Token: $this->auth_token";
        }
        return $headers;        
    }

    /**
    * @param string $path
    * @return string adds the path to endpoint with.
    */
    private function build_api_call_url($path) 
    {
        return $this->endpoint . $path . '/';

    }

    /**
    * @param string $method ('GET', 'POST', 'DELETE', 'PATCH')
    * @param string $path whichever API path you want to target.
    * @param array $data contains the POST data to be sent to the API.
    * @return array decoded json returned by API.
    */
    private function api_call($method, $path, array $data=null) 
    {
        $path = (string) $path;
        $method = (string) $method;
        $data = (array) $data;
        $headers = $this->build_curl_headers();
        $request_url = $this-> build_api_call_url($path);

        $options = array();
        $options[CURLOPT_HTTPHEADER] = $headers;
        $options[CURLOPT_RETURNTRANSFER] = true;
        
        if($method == 'POST') {
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_POSTFIELDS] = http_build_query($data);
        } else if($method == 'DELETE') {
            $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        } else if($method == 'PATCH') {
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_POSTFIELDS] = http_build_query($data);         
            $options[CURLOPT_CUSTOMREQUEST] = 'PATCH';
        } else if ($method == 'GET' or $method == 'HEAD') {
            if (!empty($data)) {
                /* Update URL to container Query String of Paramaters */
                $request_url .= '?' . http_build_query($data);
            }
        }
        // $options[CURLOPT_VERBOSE] = true;
        $options[CURLOPT_URL] = $request_url;

        $this->curl = curl_init();
        $setopt = curl_setopt_array($this->curl, $options);
        $response = curl_exec($this->curl);
        $headers = curl_getinfo($this->curl);

        $error_number = curl_errno($this->curl);
        $error_message = curl_error($this->curl);
        $response_obj = json_decode($response, true);

        if($response_obj['success'] == false) {
            $message = json_encode($response_obj['message']);
            throw new Exception($message . PHP_EOL);
        }
        return $response_obj;
    }

    /**
    * @return string URL to upload file or cover image asynchronously
    */
    public function getUploadUrl()
    {
        $result = $this->api_call('GET', 'links/get_file_upload_url', array());
        return $result['upload_url'];
    }

    /**
    * @param string $file_path
    * @return string JSON returned when the file upload is complete.
    */
    public function uploadFile($file_path)
    {
        $upload_url = $this->getUploadUrl();
        $file_path = realpath($file_path);
        $file_name = basename($file_path);
        $ch = curl_init();
        $data = array('fileUpload'=>'@'.$file_path);
        curl_setopt($ch, CURLOPT_URL, $upload_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($ch);
    }

    /**
    * Uploads any file or cover image mentioned in $link and 
    * updates it with the json required by the API.
    * @param array $link
    * @return array $link updated with uploaded file information if applicable.
    */
    public function uploadMagic(array $link)
    {
        if($link['file_upload']) {
            $file_upload_json = $this->uploadFile($link['file_upload']);
            $link['file_upload_json'] = $file_upload_json;
            unset($link['file_upload']);
        }
        if($link['cover_image']) {
            $cover_image_json = $this->uploadFile($link['cover_image']);
            $link['cover_image_json'] = $cover_image_json;
            unset($link['cover_image']);
        }
        return $link;        
    }

    /**
    * Authenticate using username and password of a user.
    * Automatically updates the auth_token value.
    * @param array $args contains username=>USERNAME and password=PASSWORD 
    * @return array AuthToken object.
    */
    public function auth(array $args)
    {
        $response = $this->api_call('POST', 'auth', $args);
        $this->auth_token = $response['auth_token']['auth_token']; 
        return $this->auth_token; 
    }

    /**
    * @return array list of Link objects.
    */
    public function linksList() 
    {
        $response = $this->api_call('GET', 'links', array());   
        return $response['links'];
    }

    /**
    * @return array single Link object.
    */  
    public function linkDetail($slug) 
    {
        $response = $this->api_call('GET', 'links/' . $slug, array()); 
        return $response['link'];
    }

    /**
    * @return array single Link object.
    */  
    public function linkCreate(array $link) 
    {   
        $link = $this->uploadMagic($link);
        $response = $this->api_call('POST', 'links', $link);
        return $response['link'];
    }

    /**
    * @return array single Link object.
    */  
    public function linkEdit($slug, array $link) 
    {
        $link = $this->uploadMagic($link);
        $response = $this->api_call('PATCH', 'links/' . $slug, $link);
        return $response['link'];
    }

    /**
    * @return array single Link object.
    */  
    public function linkDelete($slug) 
    {
        $response = $this->api_call('DELETE', 'links/' . $slug, array());
        return $response;
    }

    /**
    * @return array list of Payment objects.
    */  
    public function paymentsList($limit = null, $page = null) 
    {
        $params = array();
        if (!is_null($limit)) {
            $params['limit'] = $limit;
        }

        if (!is_null($page)) {
            $params['page'] = $page;
        }

        $response = $this->api_call('GET', 'payments', $params);
        return $response['payments'];
    }

    /**
    * @param string payment_id as provided by paymentsList() or Instamojo's webhook or redirect functions.
    * @return array single Payment object.
    */  
    public function paymentDetail($payment_id) 
    {
        $response = $this->api_call('GET', 'payments/' . $payment_id, array()); 
        return $response['payment'];
    }
}
?>
