<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>
/* @var $this <?php echo $this->getControllerClass(); ?> */
/* @var $model <?php echo $this->getModelClass(); ?> */

<?php
$nameColumn=$this->guessNameColumn($this->tableSchema->columns);
$label=$this->pluralize($this->class2name($this->modelClass));
echo "\$this->breadcrumbs=array(
	'$label'=>array('admin'),
	\$model->{$nameColumn}=>array('update','id'=>\$model->{$this->tableSchema->primaryKey}),
	'Обновить',
);\n";
?>

$this->menu=array(
	array('label'=>'Создать запись', 'url'=>array('create')),
	array('label'=>'Просмотр записи', 'url'=>array('view', 'id'=>$model-><?php echo $this->tableSchema->primaryKey; ?>)),
	array('label'=>'Управление записями', 'url'=>array('admin')),
);
?>

<h1>Update <?php echo $this->modelClass." <?php echo \$model->{$this->tableSchema->primaryKey}; ?>"; ?></h1>

<?php echo "<?php \$this->renderPartial('_form', array('model'=>\$model)); ?>"; ?>