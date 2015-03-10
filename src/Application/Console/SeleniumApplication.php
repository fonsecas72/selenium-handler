<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BeubiQA\Application\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Application;
use BeubiQA\Application\Command\RunSeleniumCommand;

class SeleniumApplication extends Application
{

    /**
     *
     * @param InputInterface $input
     * @return string
     */
    protected function getCommandName(InputInterface $input)
    {
        return 'selenium';
    }

    /**
     *
     * @return Command[]
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new RunSeleniumCommand();
        
        return $defaultCommands;
    }

    /**
     *
     * @return InputDefinition
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();
        
        return $inputDefinition;
    }
}
