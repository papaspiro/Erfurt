<?php
require_once "Link.php";

//under construction
class Erfurt_Sparql_Query2_Abstraction_ClassNode 
{
	protected $shownproperties = array();
	
	protected $type;
	protected $classVar;
	
	protected $outgoinglinks;
	protected $query;
	
	public function __construct(Erfurt_Sparql_Query2_Abstraction_RDFSClass $type, Erfurt_Sparql_Query2 $query, $varName = null){
		$this->type = $type;
		$this->query = $query;
		if($varName == null)
			$this->classVar = new Erfurt_Sparql_Query2_Var($type->getIri());
		else
			$this->classVar = new Erfurt_Sparql_Query2_Var($varName);
			
		$typePart= new Erfurt_Sparql_Query2_Triple($this->classVar, new Erfurt_Sparql_Query2_A(), $type->getIri());
		$subclasses = $type->getSubclasses();
		if(!empty($subclasses)){
			$union = new Erfurt_Sparql_Query2_GroupOrUnionGraphPattern();
			$unionpart = new Erfurt_Sparql_Query2_GroupGraphPattern();
			$unionpart->addElement($typePart);
			$union->addElement($unionpart);
			foreach($subclasses as $subclass){
				$unionpart = new Erfurt_Sparql_Query2_GroupGraphPattern();
				$unionpart->addElement(new Erfurt_Sparql_Query2_Triple($this->classVar, new Erfurt_Sparql_Query2_A(), $subclass));
				$union->addElement($unionpart);
			}
			$typePart = $union;
		}
		$this->query->getWhere()->addElement($typePart);
	}
	
	public function addShownProperty($predicate, $name = null){
		if(is_string($predicate)){
			$predicate = new Erfurt_Sparql_Query2_IriRef($predicate);
		}
		if(!($predicate instanceof Erfurt_Sparql_Query2_IriRef)){
			throw new RuntimeException("Argument 1 passed to Erfurt_Sparql_Query2_Abstraction_ClassNode::addFilter must be an instance of Erfurt_Sparql_Query2_IriRef instance of ".typeHelper($predicate)." given");
		}
		
		$optionalpart = new Erfurt_Sparql_Query2_OptionalGraphPattern();
			
		$this->shownproperties[] = $predicate;
		if($name == null)
			$var = new Erfurt_Sparql_Query2_Var($predicate);
		else 
			$var = new Erfurt_Sparql_Query2_Var($name);
		
		$optionalpart->addElement(new Erfurt_Sparql_Query2_Triple($this->classVar, $predicate, $var));
		$this->query->getWhere()->addElement($optionalpart);
		
		$this->query->addProjectionVar($var);
		return $this; //for chaining
	}
	
	public function addLink($predicate, Erfurt_Sparql_Query2_Abstraction_ClassNode $target){
		if(is_string($predicate)){
			$predicate = new Erfurt_Sparql_Query2_IriRef($predicate);
		}
		if(!($predicate instanceof Erfurt_Sparql_Query2_IriRef)){
			throw new RuntimeException("Argument 1 passed to Erfurt_Sparql_Query2_Abstraction_ClassNode::addFilter must be an instance of Erfurt_Sparql_Query2_IriRef instance of ".typeHelper($predicate)." given");
		}		
		
		$this->outgoinglinks[] = new Erfurt_Sparql_Query2_Abstraction_Link($predicate, $target);
		$this->query->getWhere()->addElement(new Erfurt_Sparql_Query2_Triple($this->classVar, $predicate, new Erfurt_Sparql_Query2_Var($target->getClass()->getIri())));
		return $this; //for chaining
	}
	
	public function addFilter($predicate, $type, $value){
		if(is_string($predicate)){
			$predicate = new Erfurt_Sparql_Query2_IriRef($predicate);
		}
		if(!($predicate instanceof Erfurt_Sparql_Query2_IriRef)){
			throw new RuntimeException("Argument 1 passed to Erfurt_Sparql_Query2_Abstraction_ClassNode::addFilter must be an instance of Erfurt_Sparql_Query2_IriRef instance of ".typeHelper($predicate)." given");
		}
		$propVar = new Erfurt_Sparql_Query2_Var($predicate);
		$this->query->getWhere()->addElement(new Erfurt_Sparql_Query2_Triple($this->getClassVar(), $predicate, $propVar));
		switch($type){
			case "contains":
				$this->query->getWhere()->addElement(new Erfurt_Sparql_Query2_Filter(new Erfurt_Sparql_Query2_Regex($propVar, new Erfurt_Sparql_Query2_RDFLiteral($value))));
			break;
		}
		
		return $this;
	}
	
	public function getClass(){
		return $this->type;
	}
	
	public function getClassVar(){
		return $this->classVar;
	}
}
?>