dTR-IP
===============

An IPv4/v6 Helper Suite for PHP

#Usage:

This class takes both IPv6 (expanded and condensed) and IPv4 addresses into consideration.

You can create the class by passing an IP and CIDR mask one of two ways:

    $net = new dTRIP("fe80:dead:15:a:bad:1dea:11:2234/93");
    // or
    $net = new dTRIP("fe80:dead:15:a:bad:1dea:11:2234", 93);

You can then access different aspects of a network:

    $net->getIP(); // returns IP
    $net->getCIDR(); // returns CIDR
    $net->getNetwork() // return Network ID
    $net->getBroadcast() // return Broadcast ID
    
There is a `__toString()` method which will return a JSON-encoded string on the class object:

    echo new dTRIP("fe80:dead:15:a:bad:1dea::/65");
    
The above would return:

    {
      "ip":"fe80:dead:15:a:bad:1dea::",
      "cidr":"65",
      "netmask":"ffff:ffff:ffff:ffff:8000::",
      "network":"fe80:dead:15:a::",
      "broadcast":"fe80:dead:15:a:7fff:ffff:ffff:ffff"
    }

# Examples:

Here are some examples:

###ipv4

    $net = new dTRIP("10.22.99.199", 28);
    echo $net->getNetwork(); // 10.22.99.192

###ipv6

    $net = new dTRIP("fe80:dead:15:a:bad:1dea:11:2234", 93);
    $net->getNetwork(); // fe80:dead:15:a:bad:1de8::
    $net->getBroadcast(); // fe80:dead:15:a:bad:1def:ffff:ffff
    
#Notes:

Although IPv6 does not use the concept of networks and broadcasts, the ranges are still needed to do inclusive searches. Also, IPv6 has a subnet segment, but can still be supernetted/subnetted, which this takes into consideration.
