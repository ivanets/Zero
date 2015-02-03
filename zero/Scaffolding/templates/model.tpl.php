namespace <?=trim($_DATA['app'].$_DATA['namespace'], '\\')?>;

class <?=$_DATA['name']?> {

	private $app;
<?foreach($_DATA['params'] as $var => $model):?>
	private $<?=$var?>;
<?endforeach;?>
<?$constructorParams = []?>
<?foreach($_DATA['params'] as $var => $model):?>
	<?$constructorParams[] = $model.' $'.$var?>
<?endforeach;?>

	public function __construct($app) {
		$this->app = $app;
<?foreach($_DATA['params'] as $var => $model):?>
		$this-><?=$var?> = $this->app->getService('<?=$model?>');
<?endforeach;?>
	}

}