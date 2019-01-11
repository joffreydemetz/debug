<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Debug\Debugger;

use JDZ\Debug\Debug;
use JDZ\Debug\Item\CliItem as Item;

/**
 * CLI debugger
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class CliDebugger extends Debug
{
  public function add($data, $label='', $group='')
  {
    $this->stack[] = new Item($data, $label, $group);
    return $this;
  }
  
  public function display($backtrace=false, $echo=false)
  {
    if ( !$this->showDisplay() ){
      return;  
    }
    
    foreach($this->stack as $key => $item){
      if ( $label = $item->getLabel() ){
        dump("-- ".$label." --");
      }
      dump($item->getData());
    }
    
    if ( $backtrace === true ){
      $trace[] = '# | File | Line | Function';
      
      $i=1;
      foreach($this->getBacktrace() as $_trace){
        $trace[] = $i.' | '.$_trace['file'].' | '.$_trace['line'].' | '.$_trace['function'];
      }
      
      dump(implode(PHP_EOL, $trace));
    }
    
    $this->stack = [];
  }
  
  public function debugGlobals($exit=true)
  {
    ksort($_SERVER);
    
    $this->add($_SERVER, 'SERVER');
    
    if ( $exit === true ){
      $this->activate();
      $this->end();
    }
    
    return $this;
  }
  
  public function end($backtrace=true)
  {
    if ( !$this->showDisplay() ){
      return;  
    }
    
    /* if ( !headers_sent() ){
      header("Content-type: text/html; charset=utf8");
    } */
    
    $this->display($backtrace);
    
    exit(1);
  }
}

