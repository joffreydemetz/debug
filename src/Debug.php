<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Debug;

use JDZ\Debug\Debugger\DebuggerInterface;
use JDZ\Debug\Debugger\CliDebugger;
use JDZ\Debug\Debugger\HtmlDebugger;

/**
 * Abstract Debugger
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class Debug implements DebuggerInterface
{
  /**
   * Debuging mode
   * 
   * @var   bool
   */
  protected $active;
  
  /**
   * The current debug stack
   * 
   * @var   array
   */
  protected $stack;
  
  /**
   * Group some items of the stack
   * 
   * @var   array
   * @todo   building the idea
   */
  protected $groups;
  
  /**
   * The debugger instance
   * 
   * @var   Debug
   */
  protected static $instance;
  
  /**
   * The filesystem basepath
   * 
   * @var   string
   */
  protected static $basePath = '';
  
  /**
   * Set the base path
   * 
   * Used to strip basepath from filepaths
   * 
   * @param   string  $basePath   The application basepath
   * @return   void
   */
  public static function setBasePath($basePath)
  {
    static::$basePath = rtrim($basePath, DIRECTORY_SEPARATOR).'/';
  }
  
  /**
   * Get the debugger instance 
   *
   * @return   Debug    The debugger instance
   */
  public static function getInstance()
  {
    if ( !isset(self::$instance) ){
      if ( 'cli' === PHP_SAPI ){
        self::$instance = new CliDebugger();
      }
      else {
        self::$instance = new HtmlDebugger();
      }
    }
    return self::$instance;
  }
  
  /**
   * Constructor
   */
  public function __construct()
  {
    $this->active  = false;
    $this->stack   = [];
    $this->groups  = [];
  }
  
  /**
   * {@inheritdoc}
   */
  public function activate($state=true)
  {
    $this->active = (bool)$state;
    return $this;
  }
  
  /**
   * {@inheritdoc}
   */
  public function end($backtrace=true)
  {
    $this->display($backtrace, true);
    exit(1);
  }
  
  /**
   * Check if debugger is ok for display
   *
   * @return   bool
   */
  protected function showDisplay()
  {
    if ( $this->active === false ){
      return false; 
    }
    
    if ( count($this->stack) === 0 ){
      return false; 
    }
    
    return true;
  }
  
  /**
   * Pretty print the backtrace
   *
   * @return   void
   */
  protected function getBacktrace()
  {
    $backtrace = debug_backtrace(); 
    
    foreach($backtrace as $i => &$trace){
      foreach(['function', 'file', 'class', 'type', 'line', 'args'] as $element){
        switch($element){
          case 'function':
          case 'file':
          case 'class':
          case 'type':
          case 'line':
            if ( empty($trace[$element]) ){
              $trace[$element] = '';
            }
            break;
          
          case 'args':
            if ( empty($trace[$element]) ){
              $trace[$element] = [];
            }
            break;
        }
      }
      
      if ( preg_match("/^".preg_quote('JDZ\Debug')."(.*)$/", $trace['class'], $m) ){
        if ( $m[1] !== '\Debug' || $trace['function'] !== 'end' ){
          unset($backtrace[$i]);
        }
      }
      
      if ( self::$basePath !== '' && trim($trace['file']) !== '' ){
        $trace['file'] = str_replace(self::$basePath, '../', $trace['file']);
      }
      
      $function = '';
      if ( !empty($trace['function']) ){
        if ( !empty($trace['class']) ){
          $function = $trace['class'].'::'.$trace['function'].'()';
        }
        else {
          if ( preg_match("/^(require|include)(_once)?$/", $trace['function']) ){
            if ( empty($trace['args']) ){
              $trace['args'] = [''];
            }
            elseif ( self::$basePath !== '' && $trace['args'][0] !== '' ){
              $trace['args'][0] = str_replace(self::$basePath, '../', $trace['args'][0]);
            }
            $function = $trace['function'].' "'.$trace['args'][0].'"';
          }
          else {
            $function = $trace['function'].'()';
          }
        }
      }
      $trace['function'] = $function;
      
      foreach(['function', 'file', 'class', 'type', 'line', 'args'] as $element){
        switch($element){
          case 'function':
          case 'file':
          case 'class':
          case 'type':
          case 'line':
            if ( trim($trace[$element]) === '' ){
              $trace[$element] = ' - ';
            }
            break;
          
          case 'args':
            if ( empty($trace[$element]) ){
              $trace[$element] = [];
            }
            break;
        }
      }
    }
    
    $backtrace = array_reverse($backtrace);
    $backtrace = array_values($backtrace);
    
    // dump($backtrace);
    return $backtrace;
  }
}

