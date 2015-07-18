<?php

namespace BeubiQA\Tests;

use BeubiQA\Tests\SeleniumTestCase;
use BeubiQA\Application\Command\DownloadSeleniumCommand;
use Symfony\Component\Console\Tester\CommandTester;

class FunctionalTest extends SeleniumTestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough permissions
     */
    public function test_Get_Without_Permissions()
    {
        $getCmdTester = new CommandTester(new DownloadSeleniumCommand());
        $getCmdTester->execute(array(
             '-d' => '/opt/'
        ));
    }
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Selenium jar not found
     */
    public function test_Start_Does_Not_Exists()
    {
        $this->startSelenium(array(), array('-l' => 'no_selenium.jar'));
    }
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage The Firefox-profile you set is not available.
     */
    public function test_Start_Cmd_Firefox_Profile_That_Does_Not_Exists()
    {
        $this->startSelenium(array('-p' => '/sasa/'));
    }
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Timeout
     */
    public function test_Start_Cmd_With_Short_Timeout()
    {
        $this->startSelenium(array('-t' => 0));
    }
    private function exeGetCmd()
    {
        $getCmd = new DownloadSeleniumCommand();
        $getCmd->setHttpClient($this->httpClient);
        $getCmdTester = new CommandTester($getCmd);
        $getCmdTester->execute(array(
             '-d' => $this->seleniumJarDir
        ));
        return $getCmdTester;
    }
    public function test_Get_Will_Download_a_File()
    {
        unlink($this->seleniumJarLocation);
        $getCmdTester = $this->exeGetCmd();
        $this->assertFileExists($this->seleniumJarLocation);
        $this->assertEquals('deb2a8d4f6b5da90fd38d1915459ced2e53eb201', sha1_file($this->seleniumJarLocation));
        $this->assertContains('Done', $getCmdTester->getDisplay());

        $getCmdTester = $this->exeGetCmd();
        $this->assertFileExists($this->seleniumJarLocation);
        $this->assertContains('Skipping download as the file already exists.', $getCmdTester->getDisplay());
        $this->assertContains('Done', $getCmdTester->getDisplay());
    }
    
    public function test_Start_Works()
    {
        $output = $this->startSelenium();
        $this->assertSeleniumIsRunning();
        $this->assertContains($this->seleniumBasicCommand.' > selenium.log 2> selenium.log', $output);
    }
    
    public function test_Start_Cmd_Firefox_Profile()
    {
        $profileDirPath = __DIR__.'/Resources/firefoxProfile/';
        $output = $this->startSelenium(array('-p' => $profileDirPath));
        
        $this->assertSeleniumIsRunning();
        $this->assertContains($this->seleniumBasicCommand.' -firefoxProfileTemplate '.$profileDirPath.' > selenium.log 2> selenium.log', $output);
    }
    
    public function test_Start_Cmd_XVFB()
    {
        $output = $this->startSelenium(array('--xvfb' => true));
        $this->assertContains('DISPLAY=:1 /usr/bin/xvfb-run --auto-servernum --server-num=1 '.$this->seleniumBasicCommand, $output);
        $this->assertSeleniumIsRunning();
    }
}
