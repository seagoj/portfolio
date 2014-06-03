<?php
require_once('beta.dbg.php');
/**
 * Description of class
 *
 * @author jds
 */
class dbg
{
    private $beta;
    function __construct()
    {
        $this->beta = new beta('class.dbg.php');
        //$this->beta->setBreak(get_defined_vars());
    }
    public function msg($message, $method='', $exception=false, $file='', $line='')
    {
        print "<div class='err'>";
        $method=='' ? print '' : print "<span style='color:red;'>$method</span>: ";
        print "<span style='color:black'>$message";
        $file=='' ? print '' : print "in file $file";
        $line=='' ? print '' : print "on line $line";
        print "</span></div>";
        if($exception) throw Exception ($msg);
        $this->beta->setBreak(get_defined_vars());
    }
    public function vardump($var, $label='')
    {
        $dump = var_export($var, true);
        print "<div class='err'>";
        $label=='' ? print '' : print "<span style='color:red;'>$label</span>: ";
        print "<span style='color:black;'>$dump</span></div>";
    }
    public function assert($term) {
        assert_options(ASSERT_ACTIVE, true);
        assert_options(ASSERT_WARNING, true);
        assert_options(ASSERT_BAIL, false);
        assert_options(ASSERT_QUIET_EVAL, false);
        assert_callback(ASSERT_CALLBACK, $this->msg($message,'', $script, $line));

        assert($term);
    }
}

$unit = new dbg();
$unit->msg("test");
?>
