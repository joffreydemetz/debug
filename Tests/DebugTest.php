<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Debug;

use PHPUnit\Framework\TestCase;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class DebugTest extends TestCase
{
  /**
   * Test basic debug
   * 
   * @return  void
   */
  public function testDebug()
  {
    Debug::getInstance()
      ->activate(true)
      ->add('text')
      ->add('text with label', 'Label')
      ->add((object) [ 'var1' => 1 ])
      ->add((object) [ 'var1' => 2, 'var2' => 'toizeo', 'var3' => false ], 'With label')
      ->end();
  }
}
