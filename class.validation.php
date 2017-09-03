<?php

class cx_validate{

    public function _alt($alts, $a){
        return (!empty($alts[$a]))? trim($alts[$a]): $a;        
    }

    public function required( $label, $value){
        if( empty($value) ){
            return 'The ' . $label . ' field is required.';
        }
    }

    public function min( $label, $value, $condition ){
        if( strlen($value) < $condition ){
            return 'The ' . $label . ' field should be minimum ' . $condition .' characters.';
        }
    }

    public function max( $label, $value, $condition ){
        if( strlen($value) > $condition ){
            return 'The ' . $label . ' field should have maximum ' . $condition .' characters.';
        }
    }

    public function email( $label, $value ){
        if( !filter_var($value, FILTER_VALIDATE_EMAIL) ){
            return 'The ' . $label . ' field contains an invalid email address.';
        }
    }

    public function url( $label, $value ){
        if( !filter_var($value, FILTER_VALIDATE_URL) ){
            return 'The ' . $label . ' field contains an invalid url format.';
        }
    }

    public function disallow_links( $label, $value ){
        if( preg_match("/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/", $value)){
            return 'The ' . $label . ' field does not allow links.';
        } else if( preg_match("/[a-z0-9_\-\+]+@[a-z0-9\-]+\.([a-z]{2,3})(?:\.[a-z]{2})?/i", $value) ){
            return 'The ' . $label . ' field does not allow email address.';
        }
    }

}


function cx_validate($request, $rules, $alts = array() ){

    $v = new cx_validate;
    $err = array();

    if( count($rules ) ){
        foreach($rules as $a => $b){

            $alt = $v->_alt($alts, $a);
            $c = explode('|', $b); $c = array_filter($c);
            $m = count($c); $n = 1; $o = array();
            $r = (!empty($request[$a]))?$request[$a]:'';
            
            foreach($c as $d => $e ){
                if( empty($err[$a]) ){
                    if( $e == 'required' and empty($r) ){
                        if( !empty($v->required($alt, $r)) ){
                            $err[$a] = $v->required($alt, $r);
                        }
                    } else if( strpos($e, ':') === false ){
                        if( !empty($v->$e($alt, $r)) ){
                            $err[$a] = $v->$e($alt, $r);
                        }
                   } else {
                        $n++;
                        list($h, $k) = explode(':', $e);
                        $o[$h] = $k;
                        if( $m == $n ){
                            foreach($o as $p => $q ){
                                if( $p == 'type' ){
                                   if( !empty($v->$q($alt, $r)) ){
                                        $err[$a] = $v->$q($alt, $r); break;
                                    }
                                } else {
                                    if( !empty($v->$p($alt, $r, $q)) ){
                                        $err[$a] = $v->$p($alt, $r, $q); break;
                                    }
                                }
                            }
                        } else {
                            $o[$h] = $k;
                        }
                    }
                }
            }

        }

        echo json_encode( $err );

    }

}