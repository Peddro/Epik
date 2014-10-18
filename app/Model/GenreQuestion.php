<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * GenreQuestion Model
 *
 * @package app.Model
 * @property GameGenre $Genre
 * @property QuestionType $Type
 * @author Bruno Sampaio
 */
class GenreQuestion extends AppModel {
	
	/**
	 * @var string Model name
	 */
	public $name = 'GenreQuestion';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'genres_questions';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'genre_id';
	

	/**
	 * @var array Validation rules
	 */
	public $validate = array();


	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'Genre' => array(
			'className' => 'GameGenre',
			'foreignKey' => 'genre_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Type' => array(
			'className' => 'QuestionType',
			'foreignKey' => 'type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	
	/**
	 * Get the type of questions and helps permitted by a specific game genre.
	 *
	 * @param int $genreId - the genre id.
	 * @param array $icons - the types of questions permitted.
	 * @param array $allHelps - all helps types permitted.
	 * @param mixed $questionsHelps - the types of helps permitted for each type of question.
	 * @param mixed $groupsHelps - the types of helps permitted for groups of questions.
	 */
	public function getGenreQuestions($genreId, &$icons=array(), &$allHelps=array(), &$questionsHelps=null, &$groupsHelps=null) {
		$data = $this->find('all', array(
			'fields' => array('Type.id', 'Type.icon', 'GenreQuestion.resource', 'GenreQuestion.hints', 'GenreQuestion.remove'), 
			'conditions' => array($this->name.'.genre_id' => $genreId), 
			'order' => array('Type.id')
		));
		
		foreach($data as $item) {
			$name = $item['Type']['icon'];
			$icons[$item['Type']['id']] = $name;
			
			if(!is_null($questionsHelps)) {
				if(!isset($questionsHelps[$name])) $questionsHelps[$name] = array();
				foreach($item['GenreQuestion'] as $key => $value) {

					if($value) {
						$allHelps[$key] = 1;
						$questionsHelps[$name][$key] = array('use' => 1, 'selected' => 0);
						if(!is_null($groupsHelps)) {
							$groupsHelps[$key] = array('use' => 1);
						}
					}
				}
			}
		}
	}
}
