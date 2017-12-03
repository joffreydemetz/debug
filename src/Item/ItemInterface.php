<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Debug\Item;

/**
 * Debugger stack item
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
interface ItemInterface
{
  /**
   * Return the data
   * 
   * @return   mixed
   */
  public function getData();
  
  /**
   * Return the label
   * 
   * @return   string
   */
  public function getLabel();
}

