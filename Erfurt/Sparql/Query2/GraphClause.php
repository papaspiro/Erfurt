<?php
/**
 * Erfurt_Sparql Query - GraphClause.
 * 
 * @package    query
 * @author     Jonas Brekle <jonas.brekle@gmail.com>
 * @copyright  Copyright (c) 2008, {@link http://aksw.org AKSW}
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * @version    $Id$
 */
class Erfurt_Sparql_Query2_GraphClause
{
	protected $graphIri;
	protected $named = false;
	
	public function __construct(Erfurt_Sparql_Query2_IriRef $iri){
		$this->graphIri = $iri;
		
		if(func_num_args()>1){
			$setNamed = func_get_arg(1);
			if(is_bool($setNamed)){
				if($setNamed) $this->named = true;
			} else {
				throw new RuntimeException("Argument 2 passed to Erfurt_Sparql_Query2_GraphClause::__construct must be an instance of bool, instance of ".typeHelper($setNamed)." given");
			}
		}
	}
	
	public function isNamed(){
		return $this->named;
	}
	public function setNamed($bool){
		$this->named = $bool;
		return $this; // for chaining
	}
	
	public function getGraphIri(){
		return $this->graphIri;
	}
	
	public function getSparql(){
		return ($this->named ? "NAMED ": "") . $this->graphIri->getSparql();
	}
	
	public function __toString() 
    {    
        return $this->getSparql();
    }
}
?>
