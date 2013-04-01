<?php

/*
 * dTR-IP is a IPv4/v6 Helper Class for PHP
 * 
 * @author Mike Mackintosh
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public Licence
 * @copyright (c) 2012 - Mike Mackintosh
 * @version 0.7.4
 * @package dTR
 */

namespace dTR\Networking;

/**
 *
 * @author Mike Mackintosh
 *
 */

class dTRIP{

    public function __construct(){

    }

}


/**
 * Helper Functions
 */

/**
 * dtr_pton
 *
 * Converts a printable IP into an unpacked binary string
 *
 * @author Mike Mackintosh - mike@bakeryphp.com
 * @param string $ip
 * @return string $bin
 */
function dtr_pton( $ip ){

    if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
        return current( unpack( "A4", inet_pton( $ip ) ) );
    }
    elseif(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
        return current( unpack( "A16", inet_pton( $ip ) ) );
    }

    throw new \Exception("Please supply a valid IPv4 or IPv6 address");

    return false;
}


/**
 * dtr_ntop
 *
 * Converts an unpacked binary string into a printable IP
 *
 * @author Mike Mackintosh - mike@bakeryphp.com
 * @param string $str
 * @return string $ip
 */
function dtr_ntop( $str ){
    if( strlen( $str ) == 16 OR strlen( $str ) == 4 ){
        return inet_ntop( pack( "A".strlen( $str ) , $str ) );
    }

    throw new \Exception( "Please provide a 4 or 16 byte string" );

    return false;
}
