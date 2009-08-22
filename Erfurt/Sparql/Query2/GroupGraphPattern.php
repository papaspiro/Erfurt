<?php
/**
 * Erfurt_Sparql Query - GroupGraphPattern.
 * 
 * @package    query
 * @author     Jonas Brekle <jonas.brekle@gmail.com>
 * @copyright  Copyright (c) 2008, {@link http://aksw.org AKSW}
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * @version    $Id$
 */
class Erfurt_Sparql_Query2_GroupGraphPattern extends Erfurt_Sparql_Query2_GroupHelper
{
	public function addElement($member){
		if(!is_a($member, "Erfurt_Sparql_Query2_GroupGraphPattern") && !is_a($member, "Erfurt_Sparql_Query2_IF_TriplesSameSubject") && !is_a($member, "Erfurt_Sparql_Query2_Filter")){
			throw new RuntimeException("Argument 1 passed to Erfurt_Sparql_Query2_GroupGraphPattern::addElement must be an instance of Erfurt_Sparql_Query2_GroupGraphPattern or Erfurt_Sparql_Query2_Triple or Erfurt_Sparql_Query2_Filter, instance of ".typeHelper($member)." given");
			return;
		}
		$this->elements[] = $member;
		$member->newUser($this);
		return $this; //for chaining
	}
	
	public function getSparql(){
		$sparql = "{ \n";
		
		for($i=0; $i < count($this->elements); $i++){
			$sparql .= $this->elements[$i]->getSparql();
			if(is_a($this->elements[$i], "Erfurt_Sparql_Query2_IF_TriplesSameSubject") && isset($this->elements[$i+1]) && is_a($this->elements[$i+1], "Erfurt_Sparql_Query2_IF_TriplesSameSubject")){
				$sparql .= " ."; //realisation of TriplesBlock
			} 
			$sparql .= " \n";
		}
		
		return $sparql."} \n";
	}
	
	public function getVars(){
		$vars = array();
		
		foreach($this->elements as $element){
			$vars = array_merge($vars, $element->getVars());
		}
		
		return $vars;
	}
	
	
	public function setElement($i, $member){
		if(!is_a($member, "Erfurt_Sparql_Query2_GroupGraphPattern") && !is_a($member, "Erfurt_Sparql_Query2_IF_TriplesSameSubject") && !is_a($member, "Erfurt_Sparql_Query2_Filter")){
			throw new RuntimeException("Argument 2 passed to Erfurt_Sparql_Query2_GroupGraphPattern::setElement must be an instance of Erfurt_Sparql_Query2_GroupGraphPattern or Erfurt_Sparql_Query2_IF_TriplesSameSubject or Erfurt_Sparql_Query2_Filter, instance of ".typeHelper($member)." given");
		}
		if(!is_int($i)){
			throw new RuntimeException("Argument 1 passed to Erfurt_Sparql_Query2_GroupGraphPattern::setElement must be an instance of integer, instance of ".typeHelper($i)." given");
		}
		$this->elements[$i] = $member;
		return $this; //for chaining
	}
	
	public function setElements($elements){
		if(!is_array($elements)){
			throw new RuntimeException("Argument 1 passed to Erfurt_Sparql_Query2_GroupGraphPattern::setElements : must be an array");
		}
		
		foreach($elements as $element){
			if(!is_a($element, "Erfurt_Sparql_Query2_GroupGraphPattern") && !is_a($element, "Erfurt_Sparql_Query2_IF_TriplesSameSubject") && !is_a($element, "Erfurt_Sparql_Query2_Filter")){
				throw new RuntimeException("Argument 1 passed to Erfurt_Sparql_Query2_GroupGraphPattern::setElements : must be an array of instances of Erfurt_Sparql_Query2_GroupGraphPattern or Erfurt_Sparql_Query2_IF_TriplesSameSubject or Erfurt_Sparql_Query2_Filter");
				return $this; //for chaining
			}
		}
		$this->elements = $elements;
		return $this; //for chaining
	}
}
?>
