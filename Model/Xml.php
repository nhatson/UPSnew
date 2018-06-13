<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_Ups
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Ups\Model;

use Bss\Ups\Model\Xmlcontainer;

class Xml
{
	public $current_tag = [];
    public $xml_parser;
    public $Version = 1.0;
    public $tagtracker = [];
    public $tagdata = [];
    public $xmlcontainer;
    public function __construct(
        Xmlcontainer $xmlcontainer
    ) {
        $this->xmlcontainer = $xmlcontainer;
    }

    public function startElement($parser, $name, $attrs) {
        array_push($this->current_tag, $name);
        $curtag = implode("_",$this->current_tag);

        if(isset($this->tagtracker["$curtag"])) {
            $this->tagtracker["$curtag"]++;
        } else {
            $this->tagtracker["$curtag"]=0;
        }

        if(count($attrs)>0) {
            $j = $this->tagtracker["$curtag"];
            if(!$j) $j = 0;

            if(!is_object($GLOBALS[$this->identifier]["$curtag"][$j])) {
                $GLOBALS[$this->identifier]["$curtag"][$j] = $this->xmlcontainer;
            }

            $GLOBALS[$this->identifier]["$curtag"][$j]->store("attributes",$attrs);
        }
    }
    
    public function endElement($parser, $name) {
        $curtag = implode("_",$this->current_tag);     // piece together tag
        //var_dump($this->tagdata["$curtag"]);die;
        if(!isset($this->tagdata["$curtag"])) {
            $popped = array_pop($this->current_tag); // or else we screw up where we are
            return;     // if we have no data for the tag
        } else {
            $TD = $this->tagdata["$curtag"];
            unset($this->tagdata["$curtag"]);
        }

        $popped = array_pop($this->current_tag);

        if(sizeof($this->current_tag) == 0) return;     // if we aren't in a tag

        $curtag = implode("_",$this->current_tag);     // piece together tag
                                // this time for the arrays

        $j = $this->tagtracker["$curtag"];
        if(!$j) $j = 0;

        if(!isset(($GLOBALS[$this->identifier]["$curtag"][$j]))) {
            $GLOBALS[$this->identifier]["$curtag"][$j] =  $this->xmlcontainer;
        }
        $GLOBALS[$this->identifier]["$curtag"][$j]->store($name,$TD); #$this->tagdata["$curtag"]);
        unset($TD);
        return TRUE;
    }

    public function characterData($parser, $cdata) {
        $curtag = implode("_",$this->current_tag); // piece together tag
        $this->tagdata["$curtag"] = '';
        $this->tagdata["$curtag"] .= $cdata;
    }

    public function __xml($data,$identifier='xml') {   
        $this->identifier = $identifier;

        // create parser object
        $this->xml_parser = xml_parser_create();

        // set up some options and handlers
        xml_set_object($this->xml_parser,$this);
        xml_parser_set_option($this->xml_parser,XML_OPTION_CASE_FOLDING,0);
        xml_set_element_handler($this->xml_parser, "startElement", "endElement");
        xml_set_character_data_handler($this->xml_parser, "characterData");

        if (!xml_parse($this->xml_parser, $data, TRUE)) {
            sprintf("XML error: %s at line %d",
            xml_error_string(xml_get_error_code($this->xml_parser)),
            xml_get_current_line_number($this->xml_parser));
        }

        // we are done with the parser, so let's free it
        xml_parser_free($this->xml_parser);

    }  // end constructor: function xml()    
}