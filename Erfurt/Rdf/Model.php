<?php

/**
 * Represents a basic RDF graph and some functionality that goes beyond RDF.
 *
 * @package    rdf
 * @author     Philipp Frischmuth
 * @author     Norman Heino <norman.heino@gmail.com>
 * @copyright  Copyright (c) 2008, {@link http://aksw.org AKSW}
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * @version    $Id$
 */
class Erfurt_Rdf_Model
{
    /**
     * The model base IRI. If not set, defaults to the model IRI.
     * @var string
     */
    protected $_baseIri = null;
    
    /**
     * Denotes whether the model is editable by the current agent.
     * @var boolean
     */
    protected $_isEditable = false;
    
    /**
     * The model IRI
     * @var string
     */
    protected $_modelIri = null;
    
    /**
     * An array of namespace IRIs (keys) and prefixes 
     * @var array
     * @todo remove hard-coded mock namespaces
     */
    protected $_namespaces = array(
        'http://www.w3.org/1999/02/22-rdf-syntax-ns#' => 'rdf', 
        'http://www.w3.org/2000/01/rdf-schema#'       => 'rdfs', 
        'http://www.w3.org/2002/07/owl#'              => 'owl', 
        'http://ns.ontowiki.net/SysOnt/'              => 'SysOnt', 
        'http://purl.org/dc/elements/1.1/'            => 'dc', 
        'http://xmlns.com/foaf/0.1/'                  => 'foaf', 
        'http://usefulinc.com/ns/doap#'               => 'doap', 
        'http://xmlns.com/wordnet/1.6/'               => 'wordnet', 
        'http://www.w3.org/2004/02/skos/core#'        => 'skos', 
        'http://rdfs.org/sioc/ns#'                    => 'sioc', 
        'http://swrc.ontoware.org/ontology#'          => 'swrc', 
        'http://ns.aksw.org/e-learning/lcl/'          => 'lcl', 
        'http://www.w3.org/2003/01/geo/wgs84_pos#'    => 'geo', 
        // 'nodeID://'                                   => '_'
    );
    
    /**
     * An array of properties used in this model to express
     * a resource's human-readable representation.
     * @var array
     * @todo remove hard-coded mock title properties
     */
    protected $_titleProperties = array(
        'http://www.w3.org/2000/01/rdf-schema#label', 
        'http://purl.org/dc/elements/1.1/title'
    );
    
    // ------------------------------------------------------------------------
    // --- Magic methods ------------------------------------------------------
    // ------------------------------------------------------------------------
    
    /**
     * Constructor.
     *
     * @param string $modelIri
     * @param string $baseIri
     */ 
    public function __construct($modelIri, $baseIri = null) 
    {
        $this->_modelIri = $modelIri;
        $this->_baseIri  = $baseIri;
    }
    
    /**
     * Returns a string representing the model instance. For convenience
     * reasons this is in fact the model IRI.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getModelIri();
    }
    
    // ------------------------------------------------------------------------
    // --- Public methods -----------------------------------------------------
    // ------------------------------------------------------------------------
    
    /**
     * Adds a statement to this model
     *
     * @param string $subject
     * @param string $predicate
     * @param string $object
     * @param array $options
     */
    public function addStatement($subject, $predicate, $object, $options)
    {   
        $this->getStore()->addStatement($this->_modelIri, $subject, $predicate, $object, $options);
        
        return $this;
    }
    
    /**
     * Adds multiple statements to this model.
     *
     * Accepts a plain object (an instance of stdClass) that has statements
     * as nested properties. This object is exactly what you get when you json_decode 
     * a RDF/JSON string ({@link http://n2.talis.com/wiki/RDF_JSON_Specification}).
     * Note, that we do not use the RDF/PHP specification.
     *
     * @param stdClass $statements
     */
    public function addMultipleStatements(stdClass $statements)
    {
        $this->getStore()->addMultipleStatements($this->_modelIri, $statements);
        
        return $this;
    }
    
    /**
     * Deletes the statement denoted by subject, predicate, object.
     *
     * @param string $subject
     * @param string $predicate
     * @param string $object
     */
    public function deleteStatement($subject, $predicate, $object)
    {
        $this->getStore()->deleteStatement($this->_modelIri, $subject, $predicate, $object);
    }
    
    /**
     * Deletes all statements contained in the RDF/JSON object from this model.
     *
     * @param string $subject
     * @param string $predicate
     * @param string $object
     */
    public function deleteMultipleStatements(stdClass $statements)
    {
        $this->getStore()->deleteMultipleStatements($this->_modelIri, $statements);
    }
    
    /**
     * Deletes all statements that match a certain triple pattern.
     *
     * The triple patterns is denoted by subject, predicate, object
     * where one or two can be <code>null</code>.
     *
     * @param string|null $subjectSpec
     * @param string|null $predicateSpec
     * @param string|null $objectSpec
     */
    public function deleteMatchingStatements($subjectSpec, $predicateSpec, $objectSpec)
    {
        $this->getStore()->deleteMatchingStatements($this->_modelIri, $subjectSpec, $predicateSpec, $objectSpec);
    }
    
    /**
     * Returns the model base IRI
     *
     * @return string
     */
    public function getBaseIri()
    {
        if (null === $this->_baseIri) {
            return $this->_modelIri;
        }
        
        return $this->_baseIri;
    }
    
    /**
     * Returns the model IRI
     *
     * @return string
     */
    public function getModelIri() 
    {    
        return $this->_modelIri;
    }
    
    /**
     * Returns an array of namespace IRIs (keys) and prefixes defined
     * in this model's source file.
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->_namespaces;
    }
    
    /**
     * Resource factory method
     *
     * @return Erfurt_Rdf_Resource
     */
    public function getResource($resourceIri)
    {
        require_once 'Erfurt/Rdf/Resource.php';
        return new Erfurt_Rdf_Resource($resourceIri, $this);
    }
    
    /**
     * Returns an array of properties used in this model to express
     * a resource's human-readable representation.
     *
     * @return array
     */
    public function getTitleProperties()
    {
       return $this->_titleProperties; 
    }
    
    /**
     * Sets this model's editable flag.
     *
     * @param boolean $editableFlag
     */
    public function setEditable($editableFlag)
    {
        $this->_isEditable = $editableFlag;
        
        return $this;
    }
    
    /**
     * Updates this model if the mutual difference of 2 RDF/JSON objects.
     *
     * Added statements are those that are found in $changed but not in $original, 
     * removed statements are found in $original but not in $changed.
     *
     * @param stdClass statementsObject1
     * @param stdClass statementsObject2
     */
    public function updateWithMutualDifference(stdClass $original, stdClass $changed)
    {
        $addedStatements   = $this->_getStatementsDiff($changed, $original);
        $removedStatements = $this->_getStatementsDiff($original, $changed);
        
        $this->addMultipleStatements($addedStatements);
        $this->deleteMultipleStatements($removedStatements);
        
        return $this;
    }
    
    // ------------------------------------------------------------------------
    // --- Private/protected methods ------------------------------------------
    // ------------------------------------------------------------------------
    
    /**
     * Calculates the difference of two RDF/JSON objects.
     *
     * The difference will contain any statement in the first object that
     * is not contained in the second object.
     *
     * @param stdClass statementsObject1
     * @param stdClass statementsObject2
     *
     * @return stdClass a RDF/JSON object
     */
    private function _getStatementsDiff(stdClass $statementsObject1, stdClass $statementsObject2)
    {
        // we start with a clone of object 1
        // TODO: don't mess with the original 
        // $statementsObject1's predicates
        $difference = clone $statementsObject1;
        
        // check for each subject if it is found in object 2
        // if it is not, continue immediately
        foreach ($difference as $subject => $predicates) {
            if (!isset($subject, $statementsObject2)) {
                continue;
            }
            
            // check for each predicate if it is found in the current 
            // subject's predicates of object 2, if it is not, continue immediately
            foreach ($predicates as $predicate => $objects) {
                if (!isset($predicate, $statementsObject2->$subject)) {
                    continue;
                }
                
                // for each object we have to check if it exists in object 2
                // (subject and predicate are identical up here)
                foreach ($objects as $key => $object) {
                    foreach ($statementsObject2->$subject->$predicate as $object2) {
                        if ($object->type == $object2->type && $object->value == $object2->value) {
                            // remove identical objects from the difference
                            unset($difference->$subject->{$predicate}[$key]);
                            
                            // remove empty predicates
                            if (count($difference->$subject->$predicate) == 0) {
                                unset($difference->$subject->$predicate);
                            }
                            
                            // remove empty subjects
                            if (count((array) $difference->$subject) == 0) {
                                unset($difference->$subject);
                            }
                        }
                    }
                }
            }
        }

        return $difference;
    }
    
    // ------------------------------------------------------------------------
    
    public function sparqlQueryWithPlainResult($query)
    {    
        return $this->getStore()->executeSparql($this, $query);
    }
    
    public function getStore()
    {    
        require_once 'Erfurt/App.php';
        return Erfurt_App::getInstance()->getStore();
    }
}
