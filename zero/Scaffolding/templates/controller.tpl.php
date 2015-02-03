namespace <?=trim($_DATA['app'].$_DATA['namespace'], '\\')?>;

class <?=$_DATA['name']?> {

	private $app;
<?foreach($_DATA['services'] as $service):?>
	private $<?=$service?>;
<?endforeach;?>
<?foreach($_DATA['models'] as $var => $model):?>
	private $<?=$var?>;
<?endforeach;?>
<?$constructorParams = []?>
<?foreach($_DATA['models'] as $var => $model):?>
	<?$constructorParams[] = $model.' $'.$var?>
<?endforeach;?>

	public function __construct($app) {
		$this->app = $app;
<?foreach($_DATA['services'] as $service):?>
		$this-><?=$service?> = $app->getService('<?=$service?>');
<?endforeach;?>
<?foreach($_DATA['models'] as $var => $model):?>
		$this-><?=$var?> = $this->app->getService('<?=$model?>');
<?endforeach;?>
	}

<?foreach($_DATA['actions'] as $action => $params):?>
<?
$paramsString='';
foreach($params as $param => $value) {
	if (is_null($value)) {
		$value = '';
	} else {
	 	$value = ' = '.$value.'';
	}
	$paramsString .= '$'.$param.$value.', ';
}
$paramsString=trim($paramsString, ', ');
?>
	public function <?=$action?>Action(<?=$paramsString?>) {
		$data = [];
		return $data;
	}

<?endforeach;?>

}