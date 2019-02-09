# IPv4/IPv6 network calculator for PHP

This class takes both IPv6 and IPv4 addresses into consideration.
You can create the class by passing an IP and CIDR mask one of two ways:
```
$net = new IPCalc\IP('fe80:dead:15:a:bad:1dea:11:2234/93');
// or
$net = new IPCalc\IP('fe80:dead:15:a:bad:1dea:11:2234', 93);
```
You can then access different aspects of a network:
```
$net->getVersion();    // 6                                  # IP version
$net->getIp();         // fe80:dead:15:a:bad:1dea:11:2234    # Actual IP address
$net->getCidr()        // 93                                 # Cidr prefix
$net->getNetmask()     // ffff:ffff:ffff:ffff:ffff:fff8::    # Netmask in printable format
$net->getNetwork()     // fe80:dead:15:a:bad:1de8::          # Address of current IP network (for ipv4)
$net->getBroadcast()   // fe80:dead:15:a:bad:1def:ffff:ffff  # Broadcast address of network (for ipv4)
$net->getHostMin()     // fe80:dead:15:a:bad:1de8::          # First IP adress
$net->getHostMax()     // fe80:dead:15:a:bad:1def:ffff:ffff  # Last IP adress
```
There is a `__toString()` method which will return a JSON-encoded string on the class object:
```
echo new IPCalc\IP('192.168.1.0/24');
```
The above would return:
```
{
    "version":4,
    "ip":"192.168.1.0",
    "cidr":24,
    "netmask":"255.255.255.0",
    "network":"192.168.1.0",
    "broadcast":"192.168.1.255",
    "hostmin":"192.168.1.1",
    "hostmax":"192.168.1.254"
}
```

#### Notes:

Although IPv6 does not use the concept of networks and broadcasts, the ranges are still needed to do inclusive searches. Also, IPv6 has a subnet segment, but can still be supernetted/subnetted, which this takes into consideration.
