<?php
/**
 * Data Structure
 */

/**
 * Queue
 *
 * @package app.Lib
 * @author Bruno Sampaio
 */
class Queue {
	
	/** 
	 * @var array Memory of the queue: a circular array.
	 */
	private $contents;
	
	/**
	 * @var int Index of the element at the front of the queue.
	 */
	private $front;
	
	/**
	 * @var int Index of the element at the rear of the queue.
	 */
	private $rear;
	
	/**
	 * @var int Number of elements in the queue.
	 */
	private $currentSize;
	
	
	/**
	 * Initialize the Queue
	 */
	public function __construct() {
		$this->contents = array();
		$this->front = 0;
		$this->rear = 0;
		$this->currentSize = 0;
	}
		
	
	/**
	 * Destroy the Queue
	 */
	public function __destruct() {
		unset($this->contents);
	}
	
	
	/**
	 * Check if the Queue is empty
	 *
	 * @return $bool
	 */
	public function isEmpty() {
		return $this->currentSize == 0;
	}
	
	
	/**
	 * Get Number of Element in the Queue
	 *
	 * @return int
	 */
	public function size() {
		return $this->currentSize;
	}
	
	
	/**
	 * Insert Element
	 *
	 * @param mixed $element - the element to be inserted.
	 */
	public function enqueue($element) {
		$this->contents[$this->rear++] = $element;
		$this->currentSize++;
	}
	
	
	/**
	 * Remove Element
	 *
	 * @return mixed - the removed element.
	 */
	public function dequeue() {
		if($this->isEmpty()) {
			return null;
		}
		
		$element = $this->contents[$this->front];
		unset($this->contents[$this->front]);
		$this->front++;
		$this->currentSize--;
		return $element;
	}
}
