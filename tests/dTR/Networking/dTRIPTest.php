<?php

namespace dTR\Networking;

class dTRIPTest extends \PHPUnit_Framework_TestCase
{
    public function dataProviderIpRanges()
    {
        return array(
            array('10.22.99.199/28', '10.22.99.192', '10.22.99.207'),
            array('fe80:dead:15:a:bad:1dea:11:2234/93', 'fe80:dead:15:a:bad:1de8::', 'fe80:dead:15:a:bad:1def:ffff:ffff'),
            array('8.37.230.0/24', '8.37.230.0', '8.37.230.255'),
            array('192.168.100.14/24', '192.168.100.0', '192.168.100.255'),
            array('192.168.100.0/22', '192.168.100.0', '192.168.103.255'),
            array('2001:db8::/48', '2001:db8::', '2001:db8::ffff:ffff:ffff:ffff:ffff'),
        );
    }

    /**
     * @dataProvider dataProviderIpRanges
     */
    public function testIpRanges($cidr, $network, $broadcast)
    {
        $net = new dTRIP($cidr);

        $this->assertEquals($network, $net->getNetwork(), 'Wrong network');
        $this->assertEquals($broadcast, $net->getBroadcast(), 'Wrong broadcast');
    }
}
