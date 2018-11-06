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
    <div class="col-md-12"> 
      
      

      <?php
                    $menuItems = [];
      
                    $menuItems[] =  [
                           'label' => Yii::t('andahrm/report', 'Position'),
                            'url' => ["/{$module}/position/index"],
                            'icon'=>'fa fa-sitemap'
                     ]; 
                     
                     $menuItems[] =  [
                           'label' => Yii::t('andahrm/report', 'Capacity'),
                            'url' => ["/{$module}/position/capacity"],
                            'icon'=>'fa fa-sitemap'
                     ];    
                    
                    $menuItems[] =  [
                           'label' => Yii::t('andahrm/report', 'Pay'),
                            'url' => ["/{$module}/position/pay"],
                            'icon'=>'fa fa-sitemap'
                     ];   
                     
                     $menuItems[] =  [
                           'label' => Yii::t('andahrm/report', 'Account'),
                            'url' => ["/{$module}/position/account"],
                            'icon'=>'fa fa-sitemap'
                     ];    
                    
                  
      
                    $menuItems = Helper::filter($menuItems);
                    ?>
                    
        <div class="x_panel tile">
            <div class="x_title">
                <h2><?= Yii::t('andahrm/report', 'Report') ?> <?=$this->title;?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">            
                    <?php
                    //$nav = new Navigate();
                    echo Menu::widget([
                        'options' => ['class' => 'nav nav-tabs'],
                        'encodeLabels' => false,
                        //'activateParents' => true,
                        //'linkTemplate' =>'<a href="{url}">{icon} {label} {badge}</a>',
                        'items' => $menuItems,
                    ]);
                    ?>   
            <!--<h2><?= $this->title; ?></h2>-->
                <br/>
                <?php echo $content; ?>
                <div class="clearfix"></div>
            </div>
        </div>
        
    </div>
</div>

<?php $this->endContent(); ?>
