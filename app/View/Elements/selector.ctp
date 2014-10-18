<table>
<?php
	if(isset($types) && isset($title) && isset($url)) {
		$base_url = $url;
		foreach($types as $type) {
			
			if(isset($pass)) {
				$params = array();
				foreach($pass as $key => $val) {
					$params[] = $type[$key][$val];
				}
				$url = array_merge($base_url, $params);
			}
			
			if(isset($controller)) {
				$key = key($controller);
				$url['controller'] = $type[$key][$controller[$key]];
			}
			
			echo $this->Modal->selectorItem(__($title, $type['Type']['name']), $type['Type']['icon'], $url);
		}
	}
	
	if(isset($after)) {
		foreach($after as $item) {
			echo $this->Modal->selectorItem(__($item['title']), $item['icon'], $item['url']);
		}
	}
?>
</table>