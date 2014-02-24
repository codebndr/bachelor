<?php

namespace Codebender\UtilitiesBundle\Tests\Handler;

use Codebender\UtilitiesBundle\Handler\DefaultHandler;

class DefaultHandlerUnitTest extends \PHPUnit_Framework_TestCase
{
	public function testGet_data()
	{
		$handler = new DefaultHandler();

		//Check for No Data
		$result = $handler->get_data("http://codebender.cc/","", "");
		$this->assertNotEmpty($result);
		$this->assertStringMatchesFormat('%a<html>%a</html>%a', $result);

		//Check for wrong URL
		$result = $handler->get_data("http://codebender.cc/nonexistantpage", "", "");
        $this->assertNotEmpty($result); ##TODO: This appears to have changed significantly?
        $this->assertStringMatchesFormat('%a<html>%a<title>302 Found</title>%a</html>%a', $result);

		//Check for POST Data
		$result = $handler->get_data("http://www.htmlcodetutorial.com/cgi-bin/mycgi.pl","data", "test");
		$this->assertNotEmpty($result);
		$this->assertStringMatchesFormat('%a<TR VALIGN=TOP><TH ROWSPAN=1>data</TH><TD><PRE>test</PRE></TD></TR>%a', $result);
	}

	public function testGet()
	{
		$handler = new DefaultHandler();

		//Check for No Data
		$result = $handler->get("http://codebender.cc/");
		$this->assertNotEmpty($result);
		$this->assertStringMatchesFormat('%a<html>%a</html>%a', $result);

		//Check for wrong URL
		$result = $handler->get("http://codebender");
		$this->assertFalse($result);
	}

    public function testPost_raw_data()
    {
        $handler = new DefaultHandler();

        //Check for No Data
        $result = $handler->post_raw_data("http://codebender.cc/","");
        $this->assertNotEmpty($result);
        $this->assertStringMatchesFormat('%a<html>%a</html>%a', $result);

        //Check for wrong URL
        $result = $handler->post_raw_data("http://codebender.cc/nonexistantpage","");
        $this->assertNotEmpty($result); ##TODO: This appears to have changed significantly?
        $this->assertStringMatchesFormat('%a<html>%a<title>302 Found</title>%a</html>%a', $result);;

        //Check for POST Data
        $result = $handler->post_raw_data("http://www.htmlcodetutorial.com/cgi-bin/mycgi.pl","data=test");
        $this->assertNotEmpty($result);
        $this->assertStringMatchesFormat('%a<TR VALIGN=TOP><TH ROWSPAN=1>data</TH><TD><PRE>test</PRE></TD></TR>%a', $result);
    }

	public function testDefault_text()
	{
		chdir("web/");

		$handler = new DefaultHandler();

		//Check for wrong URL
		$result = $handler->default_text();
		$this->assertStringMatchesFormat('%asetup()%aloop()%a', $result);
	}

	public function testGet_gravatar()
	{
		$handler = new DefaultHandler();

		//Check for wrong URL
		$result = $handler->get_gravatar("tzikis@gmail.com");
		$this->assertEquals($result, '//www.gravatar.com/avatar/1a6a5289ac4473b5731fa9d9a3032828?s=80&d=mm&r=g');

		$result = $handler->get_gravatar("tzikis@gmail.com", 120);
		$this->assertEquals($result, '//www.gravatar.com/avatar/1a6a5289ac4473b5731fa9d9a3032828?s=120&d=mm&r=g');

        //No avatar
        $result = $handler->get_gravatar('tester@codebender.cc');
        $this->assertEquals($result, '//www.gravatar.com/avatar/0e346c8cfc4c5554ebf4fbe55280bfbe?s=80&d=mm&r=g');
	}

    public function testRead_Headers()
    {
        $handler = new DefaultHandler();

        $code = '#include <header1.h>
        #include "header2.h  "
        #include "false>
        #include \'false\'
        void setup(){}
        void loop(){}';

        $result = $handler->read_headers($code);
        $this->assertEquals($result, array('arrows' => array('header1'), 'quotes' => array('header2')));
    }

    public function testRead_libraies()
    {
        $handler = new DefaultHandler();

        $sketch_files = array(
            array('filename' => 'project.ino', 'content' => '#include <header1.h>
        #include "header2.h  "
        #include "false>
        #include \'false\'
        void setup(){}
        void loop(){}'),
            array('filename' => 'header.h', 'content' => '#include <header3.h>
        #include "header4.h  "
        #include "false>
        #include \'false\'
        void function_prototype(){}')
        );

        $result = $handler->read_libraries($sketch_files);

        $this->assertEquals($result, array('header1', 'header2'));

    }
}


