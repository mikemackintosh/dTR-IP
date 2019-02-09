<?php
/*
IPv4/IPv6 network calculator for PHP
*/

namespace IPCalc;

class IP {
    
    private $version;
    private $ip;
    private $cidr;
    private $ip_long;
    private $netmask_long;
        
    public function __construct($ip, $cidr=null) {
        
        if(is_null($cidr) && ($cidrpos = strpos($ip, '/')) !== false) {
            $this->ip = substr($ip, 0, $cidrpos);
            $this->cidr = (int)substr($ip, $cidrpos+1);
        } else {
            $this->ip = $ip;
            $this->cidr = $cidr;
        }
            
        /** Detect if it is a valid IPv4 Address **/
        if(filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            if($this->cidr === null || $this->cidr < 0 || $this->cidr > 32) $this->cidr = 32;
            $this->version = 4;
            $this->netmask_v4();
        }
        
        /** Detect if it is a valid IPv6 Address **/
        elseif(filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            if($this->cidr === null || $this->cidr < 0 || $this->cidr > 128) $this->cidr = 128;
            $this->version = 6;
            $this->netmask_v6();
        }
        
        $this->ip_long = $this->Ip2Bin($this->ip);
    }
    
    public function __toString() {
        return json_encode([
            'version' => $this->getVersion(),
            'ip' => $this->getIp(),
            'cidr' => $this->getCidr(),
            'netmask' => $this->getNetmask(),
            'network' => $this->getNetwork(),
            'broadcast' => $this->getBroadcast(),
            'hostmin' => $this->getHostMin(),
            'hostmax' => $this->getHostMax(),
        ]);
    }
    
    private function netmask_v4() {
        $netmask = ((1<<32) -1) << (32-$this->cidr);
        $netmask = long2ip($netmask);
        $this->netmask_long = $this->Ip2Bin($netmask);
    }
    
    private function netmask_v6() {
        $hosts = (128 - $this->cidr);
        $networks = 128 - $hosts;
        $_m = str_repeat('1', $networks).str_repeat('0', $hosts);
        $_hexMask = null;
        foreach(str_split($_m, 4) as $segment) {
            $_hexMask .= base_convert($segment, 2, 16);
        }
        $netmask = substr(preg_replace('/([A-f0-9]{4})/', '$1:', $_hexMask), 0, -1);
        $this->netmask_long = $this->Ip2Bin($netmask);
    }
    
    //Convert ip to binary
    private function Ip2Bin($ip) {
        return current(unpack('a*', inet_pton($ip)));
    }
    
    //Convert binary to ip
    private function Bin2Ip($str) {
        return inet_ntop(pack('a*', $str));
    }
    
    /**
	 * Interactive Functions
	 * @return string
	 */
    
    // Return ip version
    public function getVersion() {
        return $this->version;
    }
    
    // Return ip adress
    public function getIp() {
        return $this->ip;
    }
    
    // Return cidr prefix
    public function getCidr() {
        return $this->cidr;
    }
    
    // Return Netmask in printable format
    public function getNetmask() {
        return $this->Bin2Ip($this->netmask_long);
    }
    
    // Return network
    public function getNetwork() {
        $network = $this->ip_long & $this->netmask_long;
        return $this->Bin2Ip($network);
    }
    
    // Return Broadcast
    public function getBroadcast() {
        $broadcast = $this->ip_long | ~$this->netmask_long;
        return $this->Bin2Ip($broadcast);
    }
    
    // Return min ip adress
    public function getHostMin() {
        $hostmin = $this->ip_long & $this->netmask_long;
        $ip = $this->Bin2Ip($hostmin);
        if($this->version == 4) $ip = long2ip(ip2long($ip)+1);
        return $ip;
    }
    
    // Return max ip adress
    public function getHostMax() {
        $hostmax = $this->ip_long | ~$this->netmask_long;
        $ip = $this->Bin2Ip($hostmax);
        if($this->version == 4) $ip = long2ip(ip2long($ip)-1);
        return $ip;
    }
    
}
