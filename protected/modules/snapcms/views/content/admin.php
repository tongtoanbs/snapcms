<?php
/* @var $this ContentController */
/* @var $model Content */

$this->breadcrumbs=array(
	'Contents'=>array('index'),
	'Manage',
);

$this->operations=array(
	array('label'=>'Create Page', 'url'=>array('/snapcms/contentType/index')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#content-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<div class="page-header">
	<h1 class="text-muted">Manage Content</h1>
</div>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<p><?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?></p>

<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
<?php $this->widget('SnapGridView', array(
	'id'=>'content-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'name'=>'title',
			'type'=>'raw',
			//'header'=>'Name',
			'value'=>'CHtml::link($data->title, array("/snapcms/content/update","id"=>$data->id))',
		),
		array(
			'name'=>'type',
			//'header'=>'Content Type',
			'filter'=>ContentType::getList(),
		),
		array(            // display 'create_time' using an expression
            'name'=>'updated',
            'value'=>'SnapFormat::date($data->updated)',
        ),
		array(
			'class'=>'SnapButtonColumn',
			'viewButtonUrl'=>'array("/content/view/","id"=>$data->id)',
		),
	),
)); ?>
