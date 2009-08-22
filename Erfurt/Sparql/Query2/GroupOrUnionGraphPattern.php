<?php
/**
 * Erfurt_Sparql Query - GroupOrUnionGraphPattern.
 * 
 * @package    query
 * @author     Jonas Brekle <jonas.brekle@gmail.com>
 * @copyright  Copyright (c) 2008, {@link http://aksw.org AKSW}
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * @version    $Id$
 */
class Erfurt_Sparql_Query2_GroupOrUnionGraphPattern extends Erfurt_Sparql_Query2_GroupGraphPattern
{	
	public function getSparql(){
		$sparql = "";
		
		for($i = 0; $i < count($this->elements); $i++){
			$sparql .= $this->elements[$i]->getSparql();
			if($i < (count($this->elements)-1)){
				$sparql .= " UNION ";
			}
		}
		
		return $sparql;
	}
	
	public function addElement(Erfurt_Sparql_Query2_GroupGraphPattern $element){
		$this->elements[] = $element;
		$element->newUser($this);
		return $this; //for chaining
	}
	
	public function setElement($i, Erfurt_Sparql_Query2_GroupGraphPattern $element){
		if(!is_int($i)){
			throw new RuntimeException("Argument 1 passed to Erfurt_Sparql_Query2_GroupOrUnionGraphPattern::setElement must be an instance of integer, instance of ".typeHelper($member)." given");
		}
		$this->elements[$i] = $element;
	}
	
	public function setElements($elements){
		if(!is_array($elements)){
			throw new RuntimeException("Argument 1 passed to Erfurt_Sparql_Query2_GroupGraphPattern::setElements : must be an array");
		}
		
		foreach($elements as $element){
			if(!is_a($element, "Erfurt_Sparql_Query2_GroupGraphPattern")){
				throw new RuntimeException("Argument 1 passed to Erfurt_Sparql_Query2_GroupOrUnionGraphPattern::setElements : must be an array of instances of Erfurt_Sparql_Query2_GroupGraphPattern");
				return $this; //for chaining
			}
		}
		$this->elements = $elements;
		return $this; //for chaining
	}
}
?>
