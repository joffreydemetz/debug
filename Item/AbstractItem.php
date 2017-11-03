<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Debug\Item;

/**
 * Abstract debugger stack item
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class AbstractItem implements ItemInterface 
{
  /**
   * Item data
   * 
   * @var 	mixed
   */
  protected $data;
  
  /**
   * Item label
   * 
   * @var 	string
   */
  protected $label;
  
  /**
   * Item group
   * 
   * @var 	string
   */
  protected $group;
  
  /**
   * Constructor
   * 
   * @param 	mixed   $data     Item data
   * @param 	string  $label    Item label
   * @param 	string  $group    Item group
   */
  public function __construct($data, $label='', $group='')
  {
    $this->data    = $data;
    $this->label   = $label;
    $this->group   = $group;
    
    if ( is_object($this->data) ){
      if ( method_exists($this->data, 'getProperties') ){
        $this->data = $this->data->getProperties();
      }
      elseif ( method_exists($this->data, 'export') ){
        $this->data = $this->data->export();
      }
    }
  }
  
  /**
   * {@inheritDoc}
   */
  public function getData()
  {
    return $this->data;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getLabel()
  {
    return $this->label;
  }
}

