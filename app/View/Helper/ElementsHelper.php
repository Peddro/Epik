<?php
/**
 * Imports
 */
App::uses('AppHelper', 'View/Helper');

/**
 * Elements Helper
 *
 * @package app.View.Helper
 * @author Bruno Sampaio
 */
class ElementsHelper extends AppHelper {
	
	/**
	 * @var array Helpers used by this Helper
	 */
	var $helpers = array('Html', 'Form');
	
	
	/**
	 * Constructor
	 * @param View $view
	 * @param array $settings
	 */
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
	}
	
	
	/**
	 * Creates a Featured Item.
	 * 
	 * @param string $title - element title.
	 * @param string $highlight - element title highlight.
	 * @param string $description - element descriptions.
	 * @param bool $picture - contains or not picture element.
	 * @param array $classes - element classes (can contain the keys: before, color and after).
	 * @return string 
	 */
	public function featured($title, $highlight=null, $description, $picture=false, $classes=array()) {
		$html = "<h1>$title".($highlight? "<span class=\"highlight\">$highlight</span>" : '').'</h1>'.$description;
		
		$html.= $picture? '<div class="picture"></div>' : '';
		
		$class = 'featured';
		if(isset($classes['before'])) {
			$class = $classes['before'] . ' ' . $class;
		}
		if(isset($classes['color'])) {
			$class.= ' '.$classes['color'];
		}
		if(isset($classes['after'])) {
			$class = $class . ' ' . $classes['after'];
		}
		
		return "<div class=\"$class\">$html</div>";
	}
	
	
	/**
	 * Creates Options Links
	 *
	 * @param array $item - the item data.
	 * @param string $model - the model name.
	 * @param string $controller - the controller name.
	 * @param array $data - the options data.
	 * @param string $size - the icons size (default: normal size).
	 */
	public function options($item, $model, $controller, $data, $size=false) {
		foreach($data as $key => $value) {
			
			// Verify Condition
			$condition = true;
			if(isset($value['fields'])) {
				foreach($value['fields'] as $field) {
					$condition = $condition && $item[$value['model']][$field];
				}
			}
			
			if($condition) {
				
				// Set Link Properties
				$url = array('controller' => isset($value['controller'])? $value['controller'] : $controller, $item[$model]['id']);
				$attrs = array('class' => 'icon' . ($size? '-'.$size : '') . ((isset($value['modal']) && $value['modal'])? ' modal' : ''));
				
				switch($key) {
					case 'edit':
						$url['action'] = $key;
						$attrs['title'] = __('Edit %s', $model);
						break;
						
					case 'delete':
						$url['action'] = $key;
						$attrs['title'] = __('Delete %s', $model);
						$message = __('alert-delete', $model);
						break;
						
					default:
						$attrs['title'] = $value['title'];
						$url['action'] = $value['action'];
						break;
				}
				
				// Create Link
				if(isset($url['action'])) {
					if(!isset($value['post'])) {
						$link = $this->Html->link('', $url, $attrs);
					}
					else {
						$link = $this->Form->postLink('', $url, $attrs, $message);
					}

					// Create Icon
					echo $this->Html->div($key, $link);
				}
			}
		}
	}
	
	
	/**
	 * Creates a file preview element for file display and removal on edit forms.
	 * 
	 * @param string $file - file path.
	 * @param string $type - file type.
	 * @param string $input - hidden input element which stores the file path.
	 * @param bool $filesFolder - determines if the file is inside the default files folder or not.
	 * @param bool $external - determines if the file is external or not.
	 * @return string 
	 */
	public function filePreview($file, $type, $input=null, $filesFolder=false, $external=false) {
		
		$file = $external? $file : ($filesFolder? $this->base.'/files/'.$file : $file);
		$class = 'preview';
		
		$preview = '';
		switch($type) {
			case 'audio':
				$preview = 
					"<audio class=\"$class\" controls=\"controls\">
						<source src=\"$file\">".
						__('error-browser-support', 'audio').
					"</audio>";
				break;
				
			case 'application':
				$preview = "<embed class=\"$class\" src=\"$file\" />";
				break;
				
			case 'image':
				if($filesFolder) {
					$preview = "<img class=\"$class\" src=\"$file\" />";
				}
				else {
					$preview = $this->Html->image($file, array('class' => 'preview'));
				}
				break;
			
			case 'video':
				if($external) {
					$preview = "<iframe class=\"$class\" src=\"$file\" frameborder=\"0\" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>";
				}
				else {
					$preview = 
						"<video class=\"$class\" controls=\"controls\">
							<source src=\"$file\">".
							__('error-browser-support', 'video').
						"</video>";
				}
				break;
		}
		
		if($input) {
			$html = 
				'<div class="file-info">'.
					$preview.
					'<div class="remove"><div title="'.__('Remove').'" class="icon-small"></div></div>'.
					$input.
				'</div>';
		}
		else {
			$html = "<div class=\"file-info\">$preview</div>";
		}
		
		return $html;
	}
	
	
	/**
	 * Parses a text field as a list of HTML paragraphs.
	 * 
	 * @param string $text - the text to be parsed.
	 * @param string $class - the class(es) to be used on each paragraph element.
	 * @return string
	 */
	public function paragraphs($text, $class='') {
		$html = '';
		$text = explode("\n", $text);
		foreach($text as $paragraph) {
			if(strlen($paragraph) > 0 && ord($paragraph[0]) != '13') {
				$html.= $this->Html->para($class, $paragraph);
			}
		}
		return $html;
	}
	
	
	/**
	 * Creates the IMS LTI information table.
	 * 
	 * @param string $title - the section title.
	 * @param string $desc - the section description.
	 * @param string $url - the url to send the request.
	 * @param string $icon - the tool icon url.
	 * @param string $key - the tool key.
	 * @param string $secret - the tool secret.
	 * @param string $help - the howtos section from pages controller.
	 * @return string 
	 */
	public function imsLtiTable($title, $desc, $url, $icon, $key, $secret, $help) {
		$main_url = 'http://'. $_SERVER['SERVER_NAME'] . $this->base;
		
		$html =
			"<h1>$title</h1>
			<p>$desc</p>
			<table class=\"ims-info\">
				<tr>
					<td>".__('Launch URL').":</td>
					<td class=\"url\">$main_url$url</td>
				</tr>
				<tr>
					<td>".__('Icon URL').":</td>
					<td class=\"url\">$main_url$icon</td>
				</tr>
				<tr>
					<td>".__('Consumer Key').":</td>
					<td>$key</td>
				</tr>
				<tr>
					<td>".__('Shared Secret').":</td>
					<td>$secret</td>
				</tr>
			</table>
			<p>".__('help-lti-use', $this->Html->link(__('page'), array('controller' => 'pages', 'action' => 'display', 'howtos', $help)))."</p>";
		
		return $html;
	}
	
	
	/**
	 * Creates a Tool Item.
	 * 
	 * @param string $name - tool name.
	 * @param string $title - tool title.
	 * @param string $type - tool type (button or expand).
	 * @param string $url - link url.
	 * @return string 
	 */
	public function tool($name, $title, $type, $url=array()) {
		$classes = 'tool '.$type.' '.$name.' disabled';
		
		if(count($url) > 0) {
			$classes.= ' modal';
		}
		
		return
			"<li class=\"$classes\" title=\"$title\" >".
				$this->Html->link('', $url, array('class' => 'icon')).
			"</li>";
	}
	
	
	/**
	 * Creates a Menu Item.
	 * 
	 * @param string $name - menu item name.
	 * @param bool $modal - determines if must use a modal window when clicked.
	 * @param string $title - the menu item title.
	 * @param string $code - tool keyboard code.
	 * @param string $url - link url.
	 * @param string $desc - menu description.
	 * @return string 
	 */
	public function menu($name, $modal, $title, $code=false, $url=array(), $desc=null) {
		$modal = $modal? 'modal' : ' ';
		
		$classes = "item $modal $name";
		$url = $this->Html->link('', $url, array('class' => 'icon'));
		$code = $code? "<div class=\"code\">$code</div>" : '';
		$desc = $desc? "title=\"$desc\"" : '';
		
		return
			"<div class=\"$classes\" $desc>
				$url
				<div class=\"name\">$title</div>
				$code
			</div>";
	}
	
	
	/**
	 * Creates the contents for a TipTip box.
	 *
	 * @param string $title - the title.
	 * @param string $descs - the descriptions.
	 * @return string
	 */
	public function tip($title=null, $descs=array()) {
		$html = '';
		
		if($title) {
			$html.= '<b>'.$title.'</b>';
		}
		
		foreach($descs as $desc) {
			$html.= $this->Html->para(false, $desc);
		}
		
		return $html;
	}
	
	
	/**
	 * Creates a Separator Item.
	 *
	 * @param string $tag - item tag.
	 * @return string
	 */
	public function separator($tag='li') {
		return "<$tag class=\"separator\"></$tag>";
	}
	
	
	/**
	 * Creates a Explorer Item
	 *
	 * @param string $name - the name to display.
	 * @param string $type - the type class (first class).
	 * @param string $icon - the icon name (third class).
	 * @param string $tag - the item tag.
	 * @param bool $arrow - determines if must display a arrow.
	 */
	public function explorerItem($name, $type, $icon=false, $tag='li', $arrow=false) {
		$html = '';
		
		if($arrow) {
			$html.= $this->Html->div('arrow', false);
		}
		
		$classes = "$type item" . ($icon? ' '.$icon : '');
		$html.= "<$tag class=\"$classes\">" . $this->Html->div('icon-small', false) . $this->Html->div('name', $name) . "</$tag>";
		
		return $html;
	}
	
}