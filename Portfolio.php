<?php
/**
 * Class Portfolio
 *
 * @category Seagoj
 * @package  Portfolio
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://github.com/seagoj/portfolio/LICENSE MIT
 * @link     http://github.com/seagoj/portfolio
 */

namespace Seagoj;

class Portfolio
{
    private $log;
    public $model;
    private $config;
    private $markdown;
    private $input;
    
    /**
     * Portfolio Constructor
     */
    public function __construct($options = [])
    {
        $defaults = [
            'file' => 'redis.json'
        ];

        $this->config = array_merge($defaults, $options);
        $this->log = new \Devtools\Log();
        $this->model = new \Devtools\Model();
        $this->markdown = new \Devtools\Markdown(['htmlTag' => false]);
        
        $this->loadDatastore();

        // $this->page = $this->getSection("portfolio.page");
        // $this->map = explode(",", $this->page['map']);
       
        // foreach ($this->map as $location) {
        //     $this->$location = $this->model->getAll('portfolio.'.$location);
        // }
    }

    /**
     * Portfolio.body()
     *
     * Prints the body of the portfolio
     *
     * @return void
     **/
    public function body()
    {
        $output = array();
        $first =true;
        foreach (explode("\n", $this->markdown->convert('../lib/Portfolio.md')) as $line) {
            if (substr($line, 0, 3)=="<h3") {
                if (!$first) {
                    array_push($output, "</div>\n<div class='well span10'>".$line);
                } else {
                    array_push($output, "<div class='well span10'>".$line);
                    $first = false;
                }
            } elseif (substr($line, 0, 5)=="</ul>" && $header) {
                array_push($output, $line."</div>");
                $header = false;
            } else {
                array_push($output, $line);
            }
        }
        print implode("\n", $output);
        // print "<div class='well span10'>".$this->markdown->convert('../lib/Portfolio.md')."</div>";

        /*
        $count = 1;
        while (isset($this->{'section'.$count})) {
            $this->printSection('section'.$count++);
        }
        */
    }

    /**
     * Portfolio.contact()
     *
     * Prints the contact section of the portfolio
     *
     * @return void
     **/
    public function contact()
    {
        $contact = $this->getSection('portfolio.contact');

        print "<div id='name'><span class='icon'></span>".$contact['name']."</div>"
            ."<div id='address'><span class='icon'></span>".$contact['address1']."</div>"
            ."<div id='citystatezip'><span class='icon'></span>"
                .$contact['city'].", ".$contact['state']." ".$contact['zip']."</div>"
            ."<div id='phone'><span class='icon'></span>".$contact['phone']."</div>"
            ."<div id='email'><span class='icon'></span><a href='mailto:".$contact['email']."'>"
                .$contact['email']."</a></div>"
            ."<div id='github'><span class='icon'></span>".$contact['github']."</div>";
    }

    /**
     * Portfolio.projects()
     *
     * Prints the projects from GitHub
     *
     * @return void
     **/
    public function projects()
    {
        $git = new \Devtools\Git();
        $git->user('seagoj');
        $list = $git->listRepos();
        if ($list) {
            print "</div><div class='well span10'><h3>Current projects</h3>\n";
            print "<ul>";
            $count = 1;
            foreach ($list as $repo) {
                $name = $git->get($repo, 'name');
                $url = $git->get($repo, 'svn_url');
                $description = $git->get($repo, 'description');
                if (substr($repo, strpos($repo, '/')+1, 9)=='cookbook-') {
                    $link = $url.'/raw/master/recipes/default.rb';
                } else {
                    $link = $url.'/raw/master/src/index.php';
                }
                
                // $file_headers = @get_headers($url);
                // if ($file_headers[0] != 'HTTP/1.1 404 Not Found') {
                //     print '<div id="codesample'.$count.'">'
                //         .'<a class="sampleClose">x</a>'
                //         .'<div class="title">Code Sample</div>'
                //         .'<pre class="prettyprint">'
                //         .'<code class="language-php">'.$code."</code></pre></div>";
                //     print "<div class='project'><a id='sample".$count++."' href='#'>"
                //.strtoupper($name)."</a>:\t".$description."</div>";
                // } else {
                    print "<div class='project'><a id='sample".$count++."' href='".$url."'>"
                        .strtoupper($name)."</a>:\t".$description."</div>";
                // }

                
            }
            print "</ul>";
            print "</div>";
        }
    }

    /**
     * Portfolio.printSection()
     *
     * Prints a single section of the body
     *
     * @param string $map Hash for the section to be printed
     *
     * @return  void
     **/
    private function printSection($map)
    {
        //print "<div class='section'>";
        print '<div class="well span10">';
        $this->sectionTitle($map);
        $this->sectionDescription($map);
        $this->sectionEntries($map);
        
        if ($this->hasSubsection($map)) {
            $this->printSubsections($map);
        }
        print '</div>';
    }

    /**
     * Portfolio.hasSubsection()
     *
     * Prints a single section of the body
     *
     * @param string $map Hash for the current section
     *
     * @return  bool True: subsection exists; False: Subsection does not exist
     **/
    private function hasSubsection($map)
    {
        return key_exists('_title11', $this->$map);
    }

    /**
     * Portfolio.getSection()
     *
     * Prints a single section of the body
     *
     * @param string $tag Index of redis hash for the section to be retrieved
     *
     * @return  void
     **/
    private function getSection($tag)
    {
        return $this->model->getAll($tag);
    }

    /**
     * Portfolio.section()
     *
     * Prints a single section of the body
     *
     * @param string $map Hash for the current section
     * @param string $key Index that refers to the section title
     *
     * @return  void
     **/
    private function sectionTitle($map, $key = 'title')
    {
        $ret = $this->$map;
        //print "\t<div class='title'>".$ret[$key]."</div>\n";
        print "\t<h3>".$ret[$key]."</h3>\n";
    }

    /**
     * Portfolio.subTitle()
     *
     * Prints the title for the current subsection
     *
     * @param string $map Hash for the current subsection
     * @param string $key Index for subtitle
     *
     * @return  void
     **/
    private function subTitle($map, $key = '_title11')
    {
        print "\t<div class='subsection'>\n";
        $this->sectionTitle($map, $key);
        //die("map: $map; key: $key");
    }

    /**
     * Portfolio.sectionDescription()
     *
     * Prints the description line of the current section
     *
     * @param string $map Hash for the current section
     * @param string $key Index that refers to the section description
     *
     * @return  void
     **/
    private function sectionDescription($map, $key = 'description')
    {
        $ret = $this->$map;
        // print "\t<div class='description'>".$ret[$key]."</div>\n";
        print "\t<h4>".$ret[$key]."</h4>\n";
    }

    /**
     * Portfolio.subDescription()
     *
     * Prints the description line of the current subsection
     *
     * @param string $map Hash for the current subsection
     * @param string $key Index for subdescription
     *
     * @return  void
     **/
    private function subDescription($map, $key = '_description11')
    {
        $this->sectionDescription($map, $key);
    }

    /**
     * Portfolio.sectionEntries()
     *
     * Prints the entries of the current section
     *
     * @param string $map Hash for the current section
     * @param string $key Index that refers to the section entries
     *
     * @return  void
     **/
    private function sectionEntries($map, $key = 'entry')
    {
        $count = 1;
        $ret = $this->$map;
        print "\t<ul class='bullet'>\n";
        while ($entry = $ret[$key.$count++]) {
            print "\t\t<li class='entry'>$entry</li>\n";
        }
        print "\t</ul>\n";
    }

    /**
     * Portfolio.subEntries()
     *
     * Prints the entries for the current subsection
     *
     * @param string $map Hash for the current subsection
     * @param string $key Index for subentries
     *
     * @return  void
     **/
    private function subEntries($map, $key)
    {
        $this->sectionEntries($map, $key);
        print "</div>\n";
    }

    /**
     * Portfolio.printSubSections()
     *
     * Prints the current subsection
     *
     * @param string $map Hash for the current subsection
     *
     * @return  void
     **/
    private function printSubsections($map)
    {
        $count = 1;
        
        while (array_key_exists('_title'.$count.'1', $this->$map) ||
            array_key_exists('_description'.$count.'1', $this->$map)) {
            if (array_key_exists('_title'.$count.'1', $this->$map)) {
                $this->subTitle($map, '_title'.$count.'1');
            }

            if (array_key_exists('_description'.$count.'1', $this->$map)) {
                $this->subDescription($map, '_description'.$count.'1');
            }
            $this->subEntries($map, '_entry'.$count++);
        }
    }

    /**
     * Portfolio::loadDatastore
     * 
     * Populates model tables from supplied file or string
     *
     * @param string $input input to be loaded
     *
     * @return boolean Status of load
     **/
    private function loadDatastore()
    {
        $this->input = is_file($this->config['file']) ?
            json_decode(file_get_contents($this->config['file'])) :
            $this->config['file'];

        $ext = substr($this->config['file'], strrpos($this->config['file'], '.')+1);

        switch($ext) {
            case 'json':
                return $this->loadDatastoreJSON();
                break;
            case 'md':
            case 'markdown':
                return $this->loadDatastoreMarkdown();
                break;
            default:
                throw new \InvalidArgumentException("$ext is not a recognized data file.");
                break;
        }

    }

    /**
     * Portfolio::loadDatastoreJSON
     *
     * Populates Model tables with data from supplied json
     *
     * @return boolean Status of load
     **/
    private function loadDatastoreJSON()
    {
        foreach ($this->input as $site => $data) {
            foreach ($data as $section => $info) {
                foreach ($info as $key => $value) {
                    $hashID = "$site.$section";
                    $this->model->set($key, $value, $hashID);
                }
            }
        }
    }

    /**
     * Portfolio::loadDatastoreMarkdown
     *
     * Populates Model tables with data from supplied markdown
     *
     * @return boolean Status of load
     **/
    private function loadDatastoreMarkdown()
    {
        print $this->markdown->convert($this->input);
    }
}
