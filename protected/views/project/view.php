<?php
/* @var $this ProjectController */
/* @var $model Project */
/* @var $tasks Task[] */   
/* @var $showParent boolean */  
 



$this->breadcrumbs=array(
	Yii::t('main','Projects')=>array('index'),
	$model->name,
);
  

KModule::fireEvents($model, KModule::BEFORE_PROJECT_MENU_MAIN, array(
	'menu'=>$this->menu,
	'controller'=>$this,
));  
$this->menu=array(
	//array('label'=>Yii::t('main','List Project'), 'url'=>array('index')),
	//array('label'=>Yii::t('main','Create Project'), 'url'=>array('create')),
	array(
		'label'=>Yii::t('main','Update Project'), 
		'url'=>array('update', 'id'=>$model->id),
		'visible'=>ProjectHelper::currentUserCreater($model),
	),
	array(
		'label'=>Yii::t('main','Delete Project'), 
		'url'=>'#', 
		'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?'),
		'visible'=>ProjectHelper::currentUserCreater($model),
		),
	array('label'=>Yii::t('main','Create Task'), 'url'=>array('/task/create', 'projectId'=>$model->id)),
	
	array('_'),
	array(
		'label'=>Yii::t('main','Add category task'), 
		'url'=>array('/taskCategory/create', 'projectId'=>$model->getPrimaryKey()),
		'visible'=>ProjectHelper::currentUserCreater($model),
		),
	array('label'=>Yii::t('main','Completed tasks'), 'url'=>array('/task/completed', 'id'=>$model->getPrimaryKey())),
	array('label'=>Yii::t('main','Tasks without category'), 'url'=>array('/task/withoutCategory', 'id'=>$model->getPrimaryKey())),
	
	array(
		'label'=>Yii::t('main','Statistics'), 
		'url'=>array('statistics', 'id'=>$model->id), 
	),
	array(
		'label'=>Yii::t('main','View tree'), 
		'url'=>array('viewTree', 'id'=>$model->id), 
	),
);
KModule::fireEvents($model, KModule::AFTER_PROJECT_MENU_MAIN, array(
	'menu'=>$this->menu,
	'controller'=>$this,
)); 
 
?>

<h1><?php echo Yii::t('main', 'View Project');?> #<?php echo $model->name; ?></h1>
 
 <div class="in-page">
 <div class="span-5 right">
		<div class="sidebar">
<?php
KModule::fireEvents($model, KModule::BEFORE_PROJECT_SIDEBAR, array(	 
	'controller'=>$this,
)); 

$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>Yii::t('main','Operations'),
));
$this->widget('zii.widgets.CMenu', array(
			'items'=>$this->menu,
			'htmlOptions'=>array('class'=>'operations'),
));
$this->endWidget();

KModule::fireEvents($model, KModule::AFTER_PROJECT_SIDEBAR, array(	 
	'controller'=>$this,
)); 
?>
			</div>
 </div>
 
 <?php if($model->getSizeWichOutViewTable() > 0) {?>
<div class="span-5 right">	
		<div class="sidebar">
		<?php
$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>Yii::t('main','Categories'),
));
$menu = array();
foreach($model->taskCategories as $taskCategory) { 
	if(!$taskCategory->view_in_table) {
		$menu[] = array(
			'label'=>$taskCategory->name, 
			'url'=>array('/taskCategory/view', 'id'=>$taskCategory->id), 
		); 
	}
}	
$this->widget('zii.widgets.CMenu', array(
			'items'=>$menu ,
			'htmlOptions'=>array('class'=>'operations'),
));
$this->endWidget();
	?>
		</div>
 </div>
 <?php } ?>
 <div class="<?php if($model->getSizeWichOutViewTable() > 0) :?>m-span-10 <?php else:?> m-span-5 <?php endif;?>">
	 <div class="content">
	 
 

<?php
KModule::fireEvents($model, KModule::BEFORE_PROJECT_CONTENT, array(	 
	'controller'=>$this,
));

array_unshift($this->tabs, array(
    'title'=>Yii::t('main', 'Files task'),
    'view'=>'_files',
    'data'=>array('model'=>$model, ),
)); 
array_unshift($this->tabs, array(
    'title'=>Yii::t('main', 'Overall'),
    'view'=>'_overall',
    'data'=>array('model'=>$model, 'showParent'=>$showParent,),
));


$this->widget('CTabView', array(
     'tabs'=>$this->tabs,
));
KModule::fireEvents($model, KModule::AFTER_PROJECT_CONTENT, array(	 
	'controller'=>$this,
)); 
?> 


</div></div>
 
</div>

<table class="task-table">
    <thead>
    <tr>
<?php 
$size = $model->getSizeViewTable() ? (int)(100 / $model->getSizeViewTable()) : 100;
foreach($model->taskCategories as $taskCategory) { 
	if(!$taskCategory->view_in_table) {
		continue;
	}
	?>     
    <th style="width:<?php echo $size?>%;">
       <h4> <?php echo CHTml::link($taskCategory->name, array('/taskCategory/view', 'id'=>$taskCategory->id), array('title'=>Yii::t('main', 'View category')));?> </h4>
	   
	   <p>
	   	<?php $this->widget('application.widgets.BoxButton', array(
			'updateButtonUrl'=>array(
				'/taskCategory/update', 'id'=>$taskCategory->getPrimaryKey(),
			),
			'updateButtonLabel'=>Yii::t('main', 'Edit caegory'),
			
			'createButtonUrl'=>array(
				'/task/create', 'categoryId'=>$taskCategory->getPrimaryKey(),
			),
			'createButtonLabel'=>Yii::t('main', 'Add task'),
			
			'deleteButtonUrl'=>array(
				'/taskCategory/delete', 'id'=>$taskCategory->getPrimaryKey(),
			),
			'deleteButtonLabel'=>Yii::t('main', 'Remove caegory'),
			'viewButtonVisible'=>false,
                        'deleteButtonVisible'=>ProjectHelper::currentUserCreater($model),
                        'updateButtonVisible'=>ProjectHelper::currentUserCreater($model),
		));?>  
        
        <?php if($taskCategory->limit_task > 0)  {?> 
			<?php echo Yii::t('main', 'Limit task {num}', array(
				'{num}'=>$taskCategory->limit_task,
			));?>    
        <?php }?>
		</p>
    </th>
<?php }?>
    </tr>
    </thead>
    <tbody>
           <tr>
<?php foreach($model->taskCategories as $taskCategory) { 
		if(!$taskCategory->view_in_table) {
			continue;
		}
		?>     
        <td class="droppable" data-pk="<?php echo $taskCategory->getPrimaryKey(); ?>"> 
           <?php foreach($tasks as $task) {  
			  if($task->task_category_id == $taskCategory->getPrimaryKey()) {
				$this->renderPartial('_view_task', array(
                    'model'=>$task,
                ));
			  }	 
            }  ?> 
    </td>
<?php }?>
    </tr> 
    </tbody>
</table>


<?php 
Yii::app()->getClientScript()->registerCoreScript( 'jquery.ui' );
Yii::app()->getClientScript()->registerCssFile(
    Yii::app()->clientScript->getCoreScriptUrl().
    '/jui/css/base/jquery-ui.css'
);
?>        

<script type="text/javascript">
$(function(){    
    var  
    fnUpdateOrder = function() {
        var list = $(this).find('>div.task-box.drag'),
            stack = {};
        list.each(function(index, item){
            stack[list.length - index] = $(item).data('pk');
        });               
        $.post('<?php echo $this->createUrl('/task/ajaxUpdateOrder', array('id'=>$model->id))?>', {tasks:stack}, function(data){});
    };    
    
    $('td.droppable').sortable({
       revert: true,
       connectWith: "td", 
        update:function(event, ui) {   
            if( ui.sender === null) {
                fnUpdateOrder.apply(ui.item.parent(), [ui.item]);
            } else {
                $.post('<?php echo $this->createUrl('/task/ajaxUpdate')?>', 
                 {id:ui.item.data('pk'), 'Task[task_category_id]': $(this).data('pk')}, function(data){
                if(!data.success){  
                    ui.sender.append( ui.item);
                } else {
                    fnUpdateOrder.apply(ui.item.parent(), [ui.item]);
                }
            }, 'json');
            }     
        },
        stop: function(event, ui) { }       
    });
});</script>
<?php 
