<?php
require_once('../lib.model/class.model.php');

class portfolio {
    private $model;
    private $contact;
    private $sectionTitles;
    private $subsectionTitles;
    private $subsections;
    private $entries;

    function  __construct() {
        $path_array = explode('/',$_SERVER['REQUEST_URI']);
        $this->model = new model($path_array[1]);
        $this->getContact();
        $this->getSectionTitles();
        //$this->getEntries();
        $this->getSubsections();
        $this->getSubsectionTitles();
/*
        print "<div>sectionTitles: </div>";
        var_dump($this->sectionTitles);
        print "<div>subectionTitles: </div>";
        var_dump($this->subsectionTitles);
        print "<div>subsections: </div>";
        var_dump($this->subsections);
        print "<div>entries: </div>";
        var_dump($this->entries);
 * 
 */
    }

    /* Only stores SQL strings for DB recreataion
     * TEMP
     */
    private function dbCreate() {
        $contentTblInit = "CREATE TABLE  `464119_portfolio`.`content` (`index` INT NOT NULL ,`section` VARCHAR( 10 ) NOT NULL ,`name` VARCHAR( 30 ) NOT NULL ,`value` VARCHAR( 250 ) NOT NULL ,INDEX (  `index` )) ENGINE = INNODB";
    }
    /* Pulls Contact information from the database and stores in $this->contact as Assoc array
     * WORKS
     */
    private function getContact() {
        $this->model->setQuery('select');
        $this->model->from('content');
        $this->model->columns(array('name','value'));
        $where = array(
            array('col'=>'section','val'=>'contact')
        );
        $this->model->where($where);
        $results =  $this->model->query();
        $this->contact = $results;
    }
    /*
    private function getEntries($index) {
        $this->model->setQuery('select');
        $this->model->from('content');
        $this->model->columns(array('value'));
        $where = array(
                array('col'=>'name','val'=>'entry'),
                array('col'=>'section','val'=>$index)
            );
        $this->model->where($where,'AND');
        print $this->model->assemble();
        $results =  $this->model->query();
        var_dump($results);
        $indexes = array();
        $i=0;
        foreach($results AS $key=>$value) {
            $indexes[$i++] = $key;
        }
        array_multisort($indexes, SORT_ASC, $results);
        $this->entries = $results;
    }
     * 
     */
    /* Gets subsections from database and stores them in $this->subsections
     * $this->subsections = array(
     *      'section'=>"sectionData",
     *
     * @todo assert data
     */
    private function getSubsections() {
        $this->model->setQuery('select');
        $this->model->from('content');
        $this->model->columns(array('section','value'));
        $where = array(
                array('col'=>'name','val'=>'subsection'),
            );
        $this->model->where($where);
        $results =  $this->model->query();
        $this->subsections = $results;
    }
    /* Gets title of all sections from th edatabase and stores them in $this->sectionTitles
     * $this->sectionTitles = array(
     *      "section"=>"title"
     * );
     * @todo assert data
     */
    private function getSectionTitles() {
        $this->model->setQuery('select');
        $this->model->from('content');
        $this->model->columns(array('section','value'));
        $where = array(
                array('col'=>'name','val'=>'title')
            );
        $this->model->where($where);
        $results =  $this->model->query();

        $this->sectionTitles = $results;

        /*
        $indexes = array();

        $i=0;
        foreach($results AS $key=>$value) {
            if(is_int($key))
                $indexes[$key] = $value;
        }
        array_multisort($indexes, SORT_ASC);
        $this->sectionTitles = $indexes;
         *
         */
    }
    /* Gets subsection Titles from database and stores them in $this->subsectionTitles
     * $this->subsectionTitles = array(
     *      "section"=>"title"
     * );
     * @todo assert data
     * @todo test combining with getSectionTitles()
     */
    private function getSubsectionTitles() {
        $this->model->setQuery('select');
        $this->model->from('content');
        $this->model->columns(array('section','value'));
        $where = array(
                array('col'=>'name','val'=>'title'),
            );
        $this->model->where($where);
        $results =  $this->model->query();
        $this->subsectionTitles = $results;
    }
    /* Retrieves and displays subsections properly
     *
     */
    private function subsections($index) {
        if(isset($this->subsections[$index])) {
            print "<div class='subsection'><div class='title'>".$this->subsectionTitles[$this->subsections[$index]]."</div>";
            $this->entries($this->subsections[$index]);
            "</div>";
        }
    }
    /* Retrieves and displays all entries listed for a section
     *
     */
    private function entries($index) {
        $this->model->setQuery('select');
        $this->model->from('content');
        $this->model->columns(array('index','value'));
        $where = array(
                array('col'=>'name','val'=>'entry'),
                array('col'=>'section','val'=>$index)
            );
        $this->model->where($where,'AND');
        $results =  $this->model->query();
        $this->entries = $results;

        $this->model->setQuery('select');
        $this->model->from('content');
        $this->model->columns(array('value'));
        $where = array(
                array('col'=>'name','val'=>'format.bullets'),
                array('col'=>'section','val'=>$index)
            );
        $this->model->where($where, 'AND');
        $bullets =  $this->model->query();
        $bullets = $bullets['value'];

        if($this->entries!=NULL)
            foreach($this->entries AS $key=>$entry) {
                print "<div class='entry'>";
                $bullets ? print "<span class='bullet'>&nbsp;</span>" : print "";
                print $entry."</div>";
            }
    }

    /* Retrieves and displays contact information properly
     *
     */
    public function contact() {
        print "<div id='contact'>\n\t\t<div class='spacer'>&nbsp;</div>\n\t\t<div class='spacer'>&nbsp;</div>\n\t\t<div id='name'>".$this->contact['name']."</div>\n"
            ."\t\t<div class='address'>".$this->contact['address1']."</div>\n";
        $this->contact['address2']!=NULL ? print "\t\t<div class='address'>".$this->contact['address2']."</div>\n" : print '';
        print "\t\t<div id='phone'>".$this->contact['phone']."</div>\n"
            ."\t\t<div id='email'><a href='".$this->contact['email']."'>".$this->contact['email']."</a></div>\n\t</div>\n";
    }
    /*
     * Retrieves and displays section information properly
     */
    public function sections() {
        print "<div id='resume'>";
        foreach($this->sectionTitles AS $index=>$title) {
            if (is_int($index)) {
               print "<div class='section'><div class='title'>$title</div>";
               $this->subsections($index);
               $this->entries($index);
               print "</div>";
            }
        }
        print "</div>";
    }
}

$port = new portfolio();
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css" />
    </head>
    <body>
        <?php
            $port->contact();
            $port->sections();
        ?>
    </body>
</html>
