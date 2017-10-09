<?php

class cx_validate{
    
    public $request = array();
    public $rules = array();
    public $cluster = array();
    public $messages = array();
    
    //Cluster all the request and rules 
    public function cluster(){
        $request = $this->request;
        $rules = $this->rules;     
        foreach($rules as $label => $rule ){
            $label = preg_replace("/[^A-Za-z0-9-_?!]/","",$label);
            $value = trim($request[$label]);
            $conditions = explode('|', $rule);
            foreach( $conditions as $method ){
                if( strpos($method, ':') === false ){
                    $this->cluster[$label . '.' . $method] = ['value' => $value];
                } else {
                    $conditions = explode(':', $method);
                    $method = $conditions[0];
                    $match = $conditions[1];
                    if( $this->is_required( $label ) or !empty($value) ){
                        $this->cluster[$label . '.' . $method] = ['value' => $value, 'match' => $match];                        
                    }
                }
            } 
        }
    }

    //This is where all the method fires off and errors are being handle dynamically from the rules method
    public function errors(){
        $cluster = $this->cluster;
        if( count($cluster) ){
            $errors = array();
            foreach( $cluster as $key => $data ){
                $key = explode('.', $key);
                $label = $key[0];
                $method = $key[1];
                if( empty($errors[$label]) ){
                    if( empty($this->is_required( $label )) and empty(trim($data['value'])) ){
                      continue;
                    } else {
                        $message = $this->$method( $label, $data );                        
                    }
                    if( !empty( $message ) ){
                        $errors[$label] = $message;
                    }             
                }
            }
        }
        return json_encode( $errors );
    }

    //Initiates the request and rule based validation
    public function validate($request, $rules, $alts = array() ){
        $this->request = $request;
        $this->rules = $rules;
        $this->cluster();
        echo $this->errors( $request, $rules );
    }
    
    //Check to see if the field is a required
    public function is_required( $label ){
        $cluster = $this->cluster;
        if( !empty($cluster[$label.'.'.'required']) ){
            return true;
        }
    }

    //This rule fires off to check if the field contains any value
    public function required( $label, $data ){
        if( empty($data['value']) ){
            return 'The ' . $label . ' field is required.';
        }
    }

     //This rule fires off to check if the field matches the min condition
     public function min( $label, $data ){
        if( strlen($data['value']) < $data['match'] ){
            return 'The ' . $label . ' field should be minimum ' . $data['match'] .' characters.';
        }
    }

     //This rule fires off to check if the field matches the max condition
     public function max( $label, $data ){
        if( strlen($data['value']) > $data['match'] ){
            return 'The ' . $label . ' field should have maximum ' . $data['match'] .' characters.';
        }
    }

     //This rule fires off to check if the field is a email address format
     public function email( $label, $data ){
        if( !filter_var($data['value'], FILTER_VALIDATE_EMAIL) ){
            return 'The ' . $label . ' field contains an invalid email address.';
        }
    }

     //This rule fires off to check if the field contains a valid url format
     public function url( $label, $data ){
        if( !filter_var($data['value'], FILTER_VALIDATE_URL) ){
            return 'The ' . $label . ' field contains an invalid url format.';
        }
    }

     //This rule fires off to check if the field contains any type of urls
     public function disallow_links( $label, $data ){
        if( preg_match("/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/", $data['value'])){
            return 'The ' . $label . ' field does not allow links.';
        }
    }

    //This rule fires off to check if the field contains any email adress
    public function disallow_emails( $label, $data ){
        if( preg_match("/[a-z0-9_\-\+]+@[a-z0-9\-]+\.([a-z]{2,3})(?:\.[a-z]{2})?/i", $data['value']) ){
            return 'The ' . $label . ' field does not allow email address.';
        }
    }

     //This custom rule fires off to check if the field matches the conditions from the pattern
     public function name( $label, $data ){
        if( !preg_match('/^([a-zA-Z]+[\'-]?[a-zA-Z]+[ ]?)+$/', $data['value']) ){
            return 'The ' . $label . ' field should only contain valid alphabetical characters.';
        }
    }

     //This custom rule fires off to check if the field matches the conditions from the pattern
     public function phone( $label, $data ){
        if( !preg_match("/^[0-9-+() ]+$/i", $data['value']) ){
            return 'The ' . $label . ' field contains invalid phone number.';
        }
    }

}