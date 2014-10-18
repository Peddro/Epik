<?php
/**
 * Imports
 */
App::uses('AppHelper', 'View/Helper');

/**
 * Modal Helper
 *
 * @package app.View.Helper
 * @author Bruno Sampaio
 */
class ModalHelper extends AppHelper {
	
	/**
	 * @var array Helpers used by this Helper
	 */
	public $helpers = array('Html', 'Form', 'Js');
	
	
	/**
	 * Modal Window Default Content Area
	 * @var $update
	 */
	public $update = '#modal_content';
	
	
	/**
	 * Constructor
	 * @param View $view
	 * @param array $settings
	 */
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
	}
	
	
	/**
	 * Creates a selector item to be displayed on a Select Section.
	 * 
	 * @param string $title - the item content.
	 * @param string $icon - the item icon.
	 * @param array $url - the url to where the item shall redirect when clicked.
	 * @return string
	 */
	public function selectorItem($title, $icon, $url) {
		return
			'<tr class="item">
				<td class="'.$icon.'">'.$this->Html->div('icon', '').'</td>
				<td>'.$this->Js->link($title, $url, $this->ajaxLinkOptions()).'</td>
			</tr>';
	}
	
	
	/**
	 * Creates a form with the basic fields for an item.
	 * The basic fields are id, name and description.
	 *
	 * @param string $model - the model name.
	 * @param bool $editing - the type of action being realized.
	 * @param array $hidden - the list of fields to set as hidden.
	 * @param bool $hideAll - determines if all fields must be hidden.
	 * @return string
	 */
	public function baseItemForm($model, $editing=false, $hidden=array(), $hideAll=false) {
		
		// Set Name and Description Fields
		if($hideAll) {
			$html = $this->Form->hidden($model.'.name') . $this->Form->hidden($model.'.description');
		}
		else {
			$html = $this->Form->input($model.'.name', array('autofocus' => 'autofocus')) . $this->Form->input($model.'.description');
		}
		
		// Set ID Field
		if($editing) $html.= $this->Form->hidden($model.'.id');
		
		// Set Other Fields
		foreach($hidden as $field) {
			$html.= $this->Form->hidden($model.'.'.$field);
		}
		
		return $html;
	}
	
	
	/**
	 * Creates a form with the basic fields to connect with a LMS.
	 * The basic fields are LMS.id, LMS.url, User.username and User.password.
	 *
	 * @param string $list - the list of supported LMS.
	 * @param bool $lms - the LMS data.
	 * @param array $choose - determines if the user must be able to choose the type of LMS or not.
	 * @param bool $hideAll - determines if all fields must be hidden (in this case the password field isn't set).
	 * @return string
	 */
	public function lmsConnectionForm($list, $lms, $choose=true, $hideAll=false) {
		$html = '';
		$choose = $hideAll? false : $choose;
		
		// LMS Fields
		if($choose) {
			$selected = isset($lms['id'])? $lms['id'] : null;
			$classes = 'input text required';
			$html.= 
				$this->Form->input('LMS.id', array('label' => __('LMS Name'), 'options' => $list, 'selected' => $selected, 'div' => array('class' => $classes))).
				$this->Form->input('LMS.url', array('label' => __('LMS URL'), 'div' => array('class' => $classes)));
		}
		else {
			if(!$hideAll) {
				$lms_url = $this->Html->link($lms['url'], $lms['url'], array('target' => '_blank'));
				$html.= $this->Html->para('', '<b>'.$lms['name'].': </b>'.$lms_url);
			}
			$html.= $this->Form->hidden('LMS.id') . $this->Form->hidden('LMS.url');
		}
		
		// User Fields
		if($hideAll) {
			$html.= $this->Form->hidden('User.username');
		}
		else {
			$html.= $this->Form->input('User.username', array('autofocus' => 'autofocus')) . $this->Form->input('User.password');
		}
		
		return $html;
	}
	
	
	/**
	 * Creates a success message to display on a Complete Section.
	 *
	 * @param string $item - the type of icon being managed.
	 * @param string $actionPerformed - the action performed, such as create, update, upload, or import.
	 * @param string $actionToPerform - the action to perform, such as view or open.
	 * @param string $list - the dashboard section.
	 * @param bool $plural - determines if sentence must be singular or plural.
	 * @return string
	 */
	public function successMessage($item, $actionPerformed, $actionToPerform, $list, $plural=false) {
		if($plural) {
			$message = __('success-list-message-plural', $item, $actionPerformed, $actionToPerform, $list);
		}
		else {
			$message = __('success-list-message-singular', $item, $actionPerformed, $actionToPerform, $list);
		}
		return $this->Html->para('', $message);
	}
	
	
	/**
	 * Set previous and current sections fields.
	 *
	 * @param string $previous - the previous section (-1: redirect to other action, null: no previous action).
	 * @param string $current - the current section.
	 * @return string
	 */
	public function setSections($previous, $current) {
		$html = '';
		
		$this->Form->unlockField('Section.previous');
		$html.= $this->Form->hidden('Section.previous', array('id' => 'form_previous', 'value' => $previous));
		
		$this->Form->unlockField('Section.current');
		$html.= $this->Form->hidden('Section.current', array('id' => 'form_current', 'value' => $current));
			
		return $html;
	}
	
	
	/**
	 * Creates the page buttons.
	 *
	 * @param array $buttons - the buttons properties:
	 *				- ajax: ajax button or normal button;
	 *				- color: the button color;
	 *				- display: the button name to be displayed;
	 *				- name: the button name HTML attribute.
	 *				- type: the button type (link, button, or submit);
	 *				- url: the button url.
	 * @return string
	 */
	public function buttons($buttons) {
		$html = '';
		foreach($buttons as $button) {
			$color = isset($button['color'])? $button['color'] : 'black';
			switch($button['type']) {
				case 'link':
					if($button['ajax']) {
						$html.= $this->Js->link($button['display'], $button['url'], $this->ajaxLinkOptions("button big $color"));
					}
					else {
						$html.= $this->Html->link($button['display'], $button['url'], array('class' => 'button big '.$color));
					}
					break;
					
				case 'button':
					$html.= $this->Form->button($button['display'], array('name' => $button['name'], 'type' => 'button', 'class' => $color));
					break;
					
				case 'submit':
					if($button['ajax']) {
						$html.= $this->Js->submit($button['display'], $this->ajaxLinkOptions($color));
					}
					else {
						$html.= $this->Form->submit($button['display'], array('class' => $color));
					}
					break;
			}
		}
		
		if(!isset($buttons['div'])) {
			$html = $this->Html->div('buttons', $html);
		}
		
		return $html;
	}
	
	
	/**
	 * Creates a Previous Button
	 *
	 * Usually it is the button on the Modal Window bottom left corner.
	 * @param array $buttons - the buttons array.
	 * @param bool $previous - determines if is a cancel or previous button.
	 */
	public function previousButton(&$buttons, $previous=false) {
		$name = $previous? 'previous' : 'cancel';
		$display = $previous? __('Back') : __('Cancel');
		array_push($buttons, array('type' => 'button', 'name' => $name, 'display' => $display));
	}
	
	
	/**
	 * Creates a Next Button
	 *
	 * Usually it is the button on the Modal Window bottom right corner.
	 * @param array $buttons - the buttons array.
	 * @param bool $finish - determines if is a continue or finish button.
	 * @param bool $ajax - determines if the button uses the ajax helper.
	 */
	public function nextButton(&$buttons, $finish=true, $ajax=true) {
		$display = $finish? __('Finish') : __('Continue');
		array_push($buttons, array('type' => 'submit', 'ajax' => $ajax, 'display' => $display));
	}
	
	
	/**
	 * Creates a Insert Button
	 *
	 * Used along side with chooser to insert a activity or resource into a project.
	 * @param array $buttons - the buttons array.
	 */
	public function insertButton(&$buttons) {
		array_push($buttons, array('type' => 'button', 'name' => 'insert', 'display' => __('Insert')));
	}
	
	
	/**
	 * Creates a Redirect Button
	 *
	 * Used along side with chooser to redirect the page to the seleted destination.
	 * @param array $buttons - the buttons array.
	 */
	public function redirectButton(&$buttons) {
		array_push($buttons, array('type' => 'button', 'name' => 'redirect', 'display' => __('Open')));
	}
	
	
	/**
	 * Creates a link button.
	 * 
	 * @param array $buttons - the buttons array.
	 * @param string $display - the button display text.
	 * @param array $url - the button url.
	 * @param bool $ajax - determines if the button uses the ajax helper.
	 * @param string $color - the button color.
	 */
	public function linkButton(&$buttons, $display, $url, $ajax=true, $color='blue') {
		array_push($buttons, array('type' => 'link', 'color' => $color, 'ajax' => $ajax, 'display' => $display, 'url' => $url));
	}
	
	
	/**
	 * Creates the buttons for the Activities actions Complete Pages.
	 *
	 * @param array $buttons - the buttons array.
	 * @param int $id - the activity id;
	 * @param string $controller - the controller for this type of activity.
	 * @param bool $allows_hints - determines if activity allows hints.
	 * @param bool $allows_resources - determines if activity allows resources.
	 */
	public function activityButtons(&$buttons, $id, $controller, $allows_hints, $allows_resources) {
		if($allows_hints) {
			$this->linkButton($buttons, __('Associate Hints'), array('controller' => 'activities', 'action' => 'hints', $id), true, 'yellow');
		}
		
		$this->linkButton($buttons, __('View Activity'), array('controller' => $controller, 'action' => 'view', $id));
		
		if($allows_resources) {
			$this->linkButton($buttons, __('Associate Resources'), array('controller' => 'activities', 'action' => 'resources', $id), true, 'yellow');
		}
	}
	
	
	/**
	 * Creates a question answers list.
	 *
	 * @param array $answer - the answer data;
	 * @param bool $small - icon size.
	 * @return string
	 */
	public function questionAnswers($answer, $small=false) {
		$icon = $small? 'icon-small' : 'icon';
		$classes = 'item answer '.($answer['is_correct']? 'correct' : 'incorrect');
		return $this->Html->div($classes, $this->Html->div($icon.' left', '').$this->Html->div('name', $answer['content']));
	}
	
	
	/**
	 * Options for a ajax link.
	 *
	 * @param string $classes - the link classes.
	 * @return array of options.
	 */
	private function ajaxLinkOptions($classes='') {
		return array(
			'class' => $classes,
			'before' => "E.ajax.start('$this->update')",
			'error' => "E.ajax.error(errorThrown)",
			'update' => $this->update,
			'complete' => "E.ajax.finish('$this->update')"
		);
	}
}