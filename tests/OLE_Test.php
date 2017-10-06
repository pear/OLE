<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | Copyright (c) 2017 The PHP Group                                     |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Alexey Kopytko <alexey@kopytko.com>                          |
// +----------------------------------------------------------------------+
//
// $Id$

class OLE_Test extends PHPUnit_Framework_TestCase
{
    public function testAsc2Ucs()
    {
        $ucs = OLE::Asc2Ucs('abc123');
        $this->assertEquals("a\000b\000c\0001\0002\0003\000", $ucs);
    }

    public function testLocalDate2OLE()
    {
        $data = OLE::LocalDate2OLE(1000000000);
        $this->assertEquals("\x00\x28\xe7\xb4\x1c\x39\xc1\x01", $data);
    }

    public function testWrite()
    {
        $OLE = new OLE_PPS_File(OLE::Asc2Ucs('Example'));
        $res = $OLE->init();
        $this->assertTrue($res);
        $OLE->append("\x00\x00\x00\x00\x00\x00");
        $OLE->append("\x01\x01\x01\x01\x01\x01");

        $root = new OLE_PPS_Root(1000000000, 1000000000, array($OLE));
        ob_start();
        $res = $root->save('-');
        $data = ob_get_clean();
        $this->assertTrue($res);

        // that's 2560 bytes of binary data to compare: using sha1 for simplicity
        $this->assertEquals(2560, strlen($data));
        $this->assertEquals('65a2a47ce025b35202dee0b706f541accb438488', sha1($data));
    }
}