<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Debug\Debugger;

/**
 * Debugger interface
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
interface DebuggerInterface
{
  /**
   * Add data to stack 
   *
   * @param   mixed     $data   The data to add to stack
   * @param   string    $label  Optionnal label
   * @return   Debug     The current object for chaining
   */
  public function activate($state=true);
  
  /**
   * Add data to stack 
   *
   * @param   mixed     $data   The data to add to stack
   * @param   string    $label  Optionnal label
   * @param   string    $group  Optionnal group
   * @return   Debug     The current object for chaining
   */
  public function add($data, $label='', $group='');
  
  /**
   * Print out the stack
   *
   * @param   bool      $backtrace   True to print the latest backtrace
   * @param   bool      $echo        True to echo the result
   * @return   \stdClass With script, style & html
   */
  public function display($backtrace=false, $echo=false);
  
  /**
   * Debug super globals (SERVER, GET, POST)
   *
   * @param   bool      $exit   Exit after print
   * @return   Debug     The current object for chaining
   */
  public function debugGlobals($exit=true);
  
  /**
   * Print out the stack
   *
   * @param   bool   $backtrace   True to print the latest backtrace
   * @return   void
   */
  public function end($backtrace=true);
}

