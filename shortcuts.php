<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Debug data
 * 
 * @param 	mixed   $data   Some data to debug
 * @param 	string  $label  Optionnal label
 * 
 * @return 	\JDZ\Debug\Debug  The debugger instance for chaining
 * @see     \JDZ\Debug\Debug::add()
 * @author  Joffrey Demetz <joffrey.demetz@gmail.com>
 */
function debugMe($data, $label='')
{
  return \JDZ\Debug\Debug::getInstance()->add($data, $label);
}

/**
 * Export debug to a string
 * 
 * @return  string 
 * @author  Joffrey Demetz <joffrey.demetz@gmail.com>
 */
function debugExport()
{
  return \JDZ\Debug\Debug::getInstance()->display(false, false);
}
