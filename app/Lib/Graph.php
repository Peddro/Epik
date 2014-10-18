<?php
/**
 * Data Structure
 */

/**
 * Oriented Graph
 *
 * @package app.Lib
 * @author Bruno Sampaio
 */
class Graph {
	
	/** 
	 * @var array List of input vertices for each vertex.
	 */
	private $inAdjacents;
	
	/**
	 * @var array List of output vertices for each vertex.
	 */
	private $outAdjacents;
	
	/**
	 * Initialize the Graph
	 */
	public function __construct() {
		$this->inAdjacents = array();
		$this->outAdjacents = array();
	}
	
	
	/**
	 * Destroy the Graph
	 */
	public function __destruct() {
		unset($this->inAdjacents);
		unset($this->outAdjacents);
	}
	
	
	/**
	 * Get the Vertex Input Degree
	 *
	 * @param int/string $id - the vertex id.
	 * @return int
	 */
	public function inDegree($id) {
		return count($this->inAdjacents[$id]);
	}
	
	
	/**
	 * Get the Vertex Output Degree
	 *
	 * @param int/string $id - the vertex id.
	 * @return int
	 */
	public function outDegree($id) {
		return count($this->outAdjacents[$id]);
	}
	
	
	/**
	 * Get the Adjacent Input Vertices of a certain Vertex
	 *
	 * @param int/string $id - the vertex id.
	 * @return array
	 */
	public function inAdjacentVertices($id) {
		return $this->inAdjacents[$id];
	}
	
	
	/**
	 * Get the Adjacent Output Vertices of a certain Vertex
	 *
	 * @param int/string $id - the vertex id.
	 * @return array
	 */
	public function outAdjacentVertices($id) {
		return $this->outAdjacents[$id];
	}
	
	
	/**
	 * Get the Number of Vertices
	 *
	 * @return int
	 */
	public function countVertices() {
		return count($this->inAdjacents);
	}
	
	
	/**
	 * Get Vertices List
	 * 
	 * @return array
	 */
	public function getVertices() {
		return array_keys($this->inAdjacents);
	}
	
	
	/**
	 * Add Vertex to the Graph
	 *
	 * @param int/string $id - the vertex id.
	 */
	public function addVertex($id) {
		if(!isset($this->inAdjacents[$id])) {
			$this->inAdjacents[$id] = array();
		}
		
		if(!isset($this->outAdjacents[$id])) {
			$this->outAdjacents[$id] = array();
		}
	}
	
	
	/**
	 * Insert Graph Edge between two Vertices
	 *
	 * @param int/string $begin - the starting vertex id.
	 * @param int/string $end - the ending vertex id.
	 */
	public function insertEdge($begin, $end) {
		
		// Add Vertices
		$this->addVertex($begin);
		$this->addVertex($end);
		
		// Add $begin as $end input vertex
		$this->inAdjacents[$end][] = $begin;
		
		// Add $end as $begin output vertex
		$this->outAdjacents[$begin][] = $end;
	}
	
}