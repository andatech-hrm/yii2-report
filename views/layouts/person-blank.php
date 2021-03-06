<?php
use yii\bootstrap\Html;
//use yii\widgets\Menu;
use yii\bootstrap\Nav;
use dmstr\widgets\Menu;
use mdm\admin\components\Helper;

 $this->beginContent('@andahrm/report/views/layouts/person-menu-left.php'); 
 $module = $this->context->module->id;
?>
<div class="row hidden-print">
    <div class="col-md-3"> 
      
      

      <?php
                    $menuItems = [];
      
                    $menuItems[] =  [
                           'label' => Yii::t('andahrm/report', 'Person'),
                            'url' => ["/{$module}/person/index"],
                            'icon'=>'fa fa-sitemap'
                     ];    
                    
      
                    $menuItems[] =  [
                           'label' =>  Yii::t('andahrm/report', 'Person Type'),
                            'url' => ["/{$module}/person/type"],
                            'icon'=>'fa fa-sitemap'
                     ];
      
      
                    $menuItems[] =  [
                            'label' =>  Yii::t('andahrm/report', 'Gender'),
                            'url' => ["/{$module}/person/gender"],
                            'icon'=>'fa fa-sitemap'
                     ]; 
      
      
                    $menuItems[] =  [
                            'label' =>  Yii::t('andahrm/report', 'Level'),
                            'url' => ["/{$module}/person/level"],
                            'icon'=>'fa fa-sitemap'
                     ];     
                     
                   $menuItems[] =  [
                            'label' =>  Yii::t('andahrm/report', 'Leave'),
                            'url' => ["/{$module}/person/leave"],
                            'icon'=>'fa fa-sitemap'
                     ];      
                  
      
                    $menuItems = Helper::filter($menuItems);
                    ?>
                    
        <div class="x_panel tile">
            <div class="x_title">
                <h2><?= Yii::t('andahrm/report', 'Report') ?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">            
                    <?php
                    //$nav = new Navigate();
                    echo Menu::widget([
                        'options' => ['class' => 'nav nav-pills nav-stacked'],
                        'encodeLabels' => false,
                        //'activateParents' => true,
                        //'linkTemplate' =>'<a href="{url}">{icon} {label} {badge}</a>',
                        'items' => $menuItems,
                    ]);
                    ?>
         <div class="clearfix"></div>
            </div>
        </div>
      
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
