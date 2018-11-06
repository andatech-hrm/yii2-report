<?php
use yii\bootstrap\Html;
//use yii\widgets\Menu;
use yii\bootstrap\Nav;
use dmstr\widgets\Menu;
use mdm\admin\components\Helper;

 $this->beginContent('@andahrm/report/views/layouts/main.php'); 
 $module = $this->context->module->id;
?>
<div class="row hidden-print">
    <div class="col-md-3"> 
      
      
      
<section class="panel">
  <div class="x_title">
    <?=Html::tag('h2','สำหรับเจ้าตัว')?>
    <div class="clearfix"></div>
  </div>
  <div class="panel-body">
      <?php
                    $menuItems = [];
      
                    $menuItems[] =  [
                           'label' => Yii::t('andahrm/leave', 'History'),
                            'url' => ["/{$module}/person/index"],
                            'icon'=>'fa fa-sitemap'
                     ];    
                    
      
                    $menuItems[] =  [
                           'label' =>  Yii::t('andahrm/leave', 'Offer'),
                            'url' => ["/{$module}/person/index"],
                            'icon'=>'fa fa-sitemap'
                     ];
      
      
                    $menuItems[] =  [
                            'label' =>  Yii::t('andahrm/leave', 'Result'),
                            'url' => ["/{$module}/person/index"],
                            'icon'=>'fa fa-sitemap'
                     ];      
                  
      
                    $menuItems = Helper::filter($menuItems);
                    
                    //$nav = new Navigate();
                    echo Menu::widget([
                        'options' => ['class' => 'nav nav-pills nav-stacked'],
                        'encodeLabels' => false,
                        //'activateParents' => true,
                        //'linkTemplate' =>'<a href="{url}">{icon} {label} {badge}</a>',
                        'items' => $menuItems,
                    ]);
                    ?>
        </div>
</section>
      
</div>
      

    <div class="col-md-9">
      
        <div class="x_panel tile">
            <div class="x_title">
                <h2><?= $this->title; ?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo $content; ?>
                <div class="clearfix"></div>
            </div>
        </div>
      
    </div>
</div>

<?php $this->endContent(); ?>
