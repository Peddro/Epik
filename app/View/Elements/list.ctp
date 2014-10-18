<?php
if(!isset($list)) {
	$data = $this->requestAction(array('controller' => $controller, 'action' => 'listing'));
	$list = $data['list'];
	$keyword = $data['keyword'];
	$use = $data['use'];
	$this->Paginator->params['paging'] = $data['paging'];
	
	$options = isset($data['options'])? $data['options'] : array();
}

$isSearch = isset($keyword) && strlen($keyword) > 0;

$current_page = $this->Paginator->params['paging'][$model]['page'];
$total_pages = $this->Paginator->params['paging'][$model]['pageCount'];
?>

<?php if(count($list) > 0) { ?>
	<?php foreach($list as $item) { ?>
		<div class="item">
			<?php
				if($use['icon'] && isset($item['Type']['icon'])) {
					echo $this->Html->div($item['Type']['icon'], $this->Html->div('icon-small', ''));
				}
			?>
		
			<div class="name <?php if(isset($nameClass)) echo $nameClass; ?>">
				<?php 
					$properties = array('class' => '');
					if($use['modal']) {
						$properties['class'] = 'modal';
					}
					echo $this->Html->link($item[$model]['name'], array('controller' => $controller, 'action' => 'view', $item[$model]['id']), $properties);
				?>
			</div>
			<div class="date">
				<?php echo $this->Time->nice(h($item[$model]['modified'])); ?>
			</div>
			<div class="options">
				<?php $this->Elements->options($item, $model, $controller, $options, 'small'); ?>
			</div>
		</div>
	<?php } ?>

	<?php if($current_page < $total_pages) { ?>
		<div class="paging">
			<?php
				$options = array('controller' => $controller, 'action' => 'listing', 'page' => $this->Paginator->current()+1);
				if($isSearch) {
					array_push($options, $keyword);
				}
				echo $this->Html->link(__('More'), $this->Paginator->url($options, true, $model));
			?>
		</div>
	<?php } ?>

<?php } else { ?>
	<p class="empty">
		<?php
			if($isSearch) {
				echo __('list-empty-search', $controller);
			}
			else {
				$special_msg = '';
				if($model == 'Game') {
					$special_msg = __('list-empty-export-message', __('game'));
				}
				else if($model == 'Template') {
					$special_msg = __('list-empty-export-message', __('template'));
				}
				else {
					$special_msg = __('list-empty-create-message');
				}
				echo __('list-empty', $controller).' '.$special_msg;
			}
		?>
	</p>
<?php } ?>