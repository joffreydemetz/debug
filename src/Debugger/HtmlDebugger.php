<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Debug\Debugger;

use JDZ\Debug\Debug;
use JDZ\Debug\Item\HtmlItem as Item;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/**
 * HTML debugger
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class HtmlDebugger extends Debug
{
  /**
   * {@inheritDoc}
   */
  public function add($data, $label='', $group='')
  {
    $this->stack[] = new Item($data, $label, $group);
    return $this;
  }
  
  /**
   * {@inheritDoc}
   */
  public function display($backtrace=false, $echo=false)
  {
    if ( !$this->showDisplay() ){
      return false;  
    }
    
    $cloner = new VarCloner();
    $dumper = new HtmlDumper();
    
    $html   = '';
    $script = '';
    $style  = '';
    
    foreach($this->stack as $key => $item){
      $value = $item->getData();
      
      $output = '';
      
      if ( is_string($value) && preg_match("/<pre class=\"query\">/", $value) ){
        $output = $value;
      }
      else {
        $dumper->dump($cloner->cloneVar($value), function($line, $depth) use (&$output){
          if ( $depth >= 0 ){
            $output .= str_repeat('  ', $depth).$line."\n";
          }
        });
        
        $output = preg_replace_callback("/<script>(.+)<\/script>/iUs", function($m) use(&$script){
          if ( preg_match("/^Sfdump\(/", $m[1]) ){
            $script .= '<script>typeof jQuery === \'undefined\' ? '.$m[1].' : jQuery(document).ready(function(){ '.$m[1].' });</script>'."\n";
          }
          elseif ( !$script ){
            $script .= '<script>'.$m[1].'</script>'."\n";
          }
          return '';
        }, $output);
        
        $output = preg_replace_callback("/<style>(.+)<\/style>/iUs", function($m) use(&$style){
          if ( !$style ){
            $style .= '<style>'.$m[1].'</style>'."\n";
          }
          return '';
        }, $output);
      }
      
      
      if ( $label = $item->getLabel() ){
        $html .= ' <h3>'.$label.'</h3>'."\n";
      }
      $html .= '<div class="item">'."\n";
      $html.= $output;
      $html .= '</div>'."\n";
    }
    
    if ( $backtrace === true ){
      $html .= ' <hr />'."\n";
      
      $html .= ' <div class="backtrace">'."\n";
      $html .= '  <table>'."\n";
      $html .= '   <thead>'."\n";
      $html .= '    <th style="width:4%;text-align:right;">#</th>'."\n";
      $html .= '    <th style="text-align:left;width:40%">File</th>'."\n";
      $html .= '    <th style="text-align:left;">Calls</th>'."\n";
      $html .= '    <th style="text-align:right;width:50px">on line</th>'."\n";
      $html .= '   </thead>'."\n";
      $html .= '   <tbody>'."\n";
      
      $i=1;
      foreach($this->getBacktrace() as $trace){
        $html .= '    <tr>'."\n";
        $html .= '     <th style="text-align:right;"> '.$i.' </th>'."\n";
        $html .= '     <td> '.$trace['file'].' </td>'."\n";
        $html .= '     <td> '.$trace['function'].' </td>'."\n";
        $html .= '     <td style="text-align:right;"> '.$trace['line'].' </td>'."\n";
        $html .= '    </tr>'."\n";
        $i++;
      }
      
      $html .= '   </tbody>'."\n";
      $html .= '  </table>'."\n";
      $html .= ' </div>'."\n";
    }
    
    $this->stack = [];
    
    if ( $echo ){
      echo $style.$html.$script;
    }
    
    return (object) [
      'script' => $script,
      'style'  => $style,
      'html'   => $html,
    ];
  }
  
  /**
   * {@inheritDoc}
   */
  public function debugGlobals($exit=true)
  {
    ksort($_SERVER);
    ksort($_GET);
    ksort($_POST);
    
    $this->add($_SERVER, 'SERVER');
    $this->add($_GET, 'GET');
    $this->add($_POST, 'POST');
    
    if ( $exit === true ){
      $this->activate();
      $this->end();
    }
    
    return $this;
  }
  
  /**
   * {@inheritDoc}
   */
  public function end($backtrace=true)
  {
    if ( !$this->showDisplay() ){
      return;  
    }
    
    if ( !headers_sent() ){
      header("Content-type: text/html; charset=utf8");
    }
    
    echo '<!DOCTYPE html>'."\n";
    echo '<html lang="fr">'."\n";
    echo ' <head>'."\n";
    echo '  <meta charset="UTF-8" />'."\n";
    echo '  <title>Debug</title>'."\n";
    echo '  <style>'."\n";
    echo '  #debugger { padding:20px 0; background:#888; }'."\n";
    echo '  #debugger > h3 { display:block; font-weight:700; margin:0 20px; padding:0; }'."\n";
    echo '  #debugger > .item { background-color:#f9f9f9; margin:10px 20px; }'."\n";
    echo '  #debugger > .backtrace { background-color:#f9f9f9; margin:10px 20px 0 20px; }'."\n";
    echo '  #debugger > .backtrace > table { width:100%; max-width:100%; background-color:transparent; border-spacing:0; border-collapse:collapse; }'."\n";
    echo '  #debugger > .backtrace > table > thead > tr > th, #debugger > .backtrace > table > tbody > tr > td { padding:8px; line-height:1.42857143; vertical-align:top; border-top: 1px solid #ddd; }'."\n";
    echo '  #debugger > .backtrace > table > thead > tr > th { vertical-align:bottom; border-bottom:2px solid #ddd; }'."\n";
    echo '  #debugger > .backtrace > table > tbody > tr > th { padding:8px; border-top: 1px solid #ddd; }'."\n";
    echo '  #debugger > .backtrace > table > thead:first-child > tr:first-child > th { border-top:0; }'."\n";
    echo '  </style>'."\n";
    echo ' </head>'."\n";
    echo ' <body>'."\n";
    echo '  <div id="debugger">'."\n";
    
    $this->display($backtrace, true);
    
    echo '  </div>'."\n";
    echo ' </body>'."\n";
    echo '</html>'."\n";
    
    exit(1);
  }
}

