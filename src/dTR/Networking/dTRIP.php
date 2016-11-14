<?php
/*
 * dTR-IP is a IPv4/v6 Helper Class for PHP
 *
 * @author Mike Mackintosh
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public Licence
 * @copyright (c) 2013 - Mike Mackintosh <mike@bakeryphp.com>
 * @version 2.2.4
 * @package dTR
 */

namespace dTR\Networking;

/**
 * dTR-IP - Class
 *
 * @author Mike Mackintosh
 *
 * @returns json_encoded objects
 */
class dTRIP{

    private $ip_long;
	public $ip;
	public $cidr;
	public $version;

	/**
	 * __construct()
	 *
	 * @param string $ip
	 * @param int $cidr
	 * @throws \Exception on invalid arguments
	 */
	function __construct($ip, $cidr = NULL){

		$this->ip = $ip;
		$this->cidr = $cidr;

		if( is_null($this->cidr) AND ($cidrpos = strpos($this->ip, "/")) !== false){

			$this->ip = substr($ip, 0, $cidrpos);
			$this->cidr = substr($ip, $cidrpos+1);

		}

		/** **/
		$this->ip_long = dtr_pton( $this->ip );

		/** Detect if it is a valid IPv4 Address **/
		if(filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){

			$this->version = 4;
			$this->netmask = $this->netmask();

			return true;

		}

		/** Detect if it is a valid IPv6 Address **/
		elseif(filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){

			$this->version = 6;
			$this->netmask = $this->netmask();

			return true;

		}


		/** If not, throw error **/
		throw new \Exception("Invalid IP/CIDR combination supplied");

		return false;
	}

	/**
	 * Magic Functions
	 * @return string
	 */
	public function __toString(){

		return json_encode(
			array(
				"ip" => $this->ip,
				"cidr" => $this->cidr,
				"netmask" => dtr_ntop($this->netmask),
				"network" => $this->network(),
				"broadcast" => $this->broadcast(),
			)
		);

	}

	public function __call($method, $args){

		/** Is there a generic function? **/
		if(method_exists($this, $method)){
			return $this->$method($args);
		}
		/** Is there a version specific function? **/
		elseif(method_exists($this, $method."_v".$this->version)){
			return $this->{$method."_v".$this->version}($args);
		}

		throw new \Exception("Invalid access method");

		return false;

	}

	/**
	 * Network Functions
	 * @return string
	 */
	public function network(){

		$network = $this->ip_long & $this->netmask;
        return $this->network = dtr_ntop($network);

	}

	/**
	 * Netmask Functions
	 * @return string
	 */
	function netmask_v4()
	{
		$netmask = ((1<<32) -1) << (32-$this->cidr);
		return dtr_pton(long2ip($netmask));
	}

	function netmask_v6()
	{
		$hosts = (128 - $this->cidr);
		$networks = 128 - $hosts;

		$_m = str_repeat("1", $networks).str_repeat("0", $hosts);

		$_hexMask = null;
		foreach( str_split( $_m, 4) as $segment){
		  $_hexMask .= base_convert( $segment, 2, 16);
		}

		$mask = substr(preg_replace("/([A-f0-9]{4})/", "$1:", $_hexMask), 0, -1);

		return dtr_pton($mask);
	}

	/**
	 * Netmask Functions
	 * @return string
	 */
	function broadcast()
	{
		$broadcast = $this->ip_long | ~($this->netmask);
		$this->broadcast = dtr_ntop($broadcast);
		return $this->broadcast;
	}

	/**
	 * Interactive Functions
	 * @return string
	 */

	// Return IP
	public function getIP(){
		return $this->ip;
	}

	// Return CIDR
	public function getCIDR(){
		return $this->cidr;
	}

	// Return Netmask in printable format
	public function getNetmask(){
		return dtr_ntop($this->netmask);
	}

	// Return network
	public function getNetwork(){
		return $this->network();
	}

	// Return Broadcast
	public function getBroadcast(){
		return $this->broadcast();
	}

}

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
        return current( unpack( "a4", inet_pton( $ip ) ) );
    }
    elseif(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
        return current( unpack( "a16", inet_pton( $ip ) ) );
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
        return inet_ntop( pack( "a".strlen( $str ) , $str ) );
    }

    throw new \Exception( "Please provide a 4 or 16 byte string" );

    return false;
}
