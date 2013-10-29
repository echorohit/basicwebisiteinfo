<?php 
include_once 'header.php';
require 'metaInfo.class.php';
//$_POST['url']='';
$url=$_POST['url'];

$process=true;
if($process){
    if(!metaInfo::validateUrl($url)){
        $msg='Url is not Valid';//We can make a config file where we can define the errors
        $process=false;
    }
}
if($process){
    if(!metaInfo::iscurl($url)){
        $msg='Curl is not enabled';//We can make a config file where we can define the errors
        $process=false;
    }
}


if($process){   
    $metaObj=new metaInfo($url);
    if(!$metaObj->curlResponse){
        $process=false;
        $msg='Curl error'.$metaObj->curlError;//We can make a config file where we can define the errors
    }
    
}

?>




<div class="cont">
<div class="container-fluid">
    
    <div class="row-fluid">
    <div class="span2">
    </div>
    <div class="span10">
        <div class="columnbox">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean ut nibh eget sapien tincidunt ullamcorper. Vivamus consectetur, nisi facilisis ornare dictum, elit neque viverra nisl, ut pellentesque leo libero a velit. Duis adipiscing nisi ac vestibulum scelerisque. Suspendisse luctus, dui sit amet lobortis pulvinar, dolor sapien condimentum sem, vel euismod felis lorem non leo. Sed egestas adipiscing sem a iaculis. Curabitur auctor et est id rhoncus. Donec porta purus augue, at cursus neque ullamcorper quis. Quisque varius ornare rhoncus. Phasellus sollicitudin in lorem sed facilisis. Vivamus leo dolor, interdum vitae accumsan at, vestibulum vitae est. Curabitur ornare et ipsum quis condimentum. Sed scelerisque dui id odio dictum sagittis. Donec sagittis malesuada augue, ut viverra magna consequat eu. Vestibulum id malesuada elit, non porta odio. Morbi euismod suscipit libero vitae venenatis. Nunc eu velit nec ante condimentum facilisis in et lorem.</p>
        </div>
        <div class="span10"> <h3>Complete Details of Site:<em class="text-success"><?php echo $url;?> </em></h3></div>
        <?php 
        if(!$process){
        ?>
        <div class="span4"><p class="text-error"><?php echo $msg;?></p></div>
        <?php
        }else{
        ?>
        <div class="span4">
            <label><strong>Meta Title:</strong></label><p class="text-success"><?php echo $metaObj->getMetaTitle();?></p>
        </div>
        <div class="span4">
            <label><strong>Meta Keywords:</strong></label><p class="text-success"><?php echo $metaObj->getMetaKeyWords();?></p>
        </div>
        <div class="span4">
            <label><strong>Meta Description:</strong></label><p class="text-success"><?php echo $metaObj->getMetaDescription();?></p>
        </div>
        
        <div class="span4">
            <label><strong>Load Time:</strong></label><p class="text-success"><?php echo $metaObj->getLoadTime();?></p>
        </div>
        <div class="span4">
            <label><strong>HTTP Response Code:</strong></label><p class="text-success"><?php echo $metaObj->getHttpStatusCode();?></p>
        </div>
        <div class="span4">
            <label><strong>IP:</strong></label><p class="text-success"><?php echo $metaObj->getRemoteIp();?></p>
        </div>
        <div class="span6">
            <label><strong>External Links(<?php echo count($metaObj->getExternalLinks());?>):</strong></label>
            <ul class="unstyled">
            <?php 
            foreach($metaObj->getExternalLinks() as $links){
                echo '<li><a href="'.$links.'">'.$links.'</a></li>';
            }
            ?>
            </ul>    
        </div>
        <div class="span6">
            <label><strong>Internal Links(<?php echo count($metaObj->getInternalLinks());?>):</strong></label>
            <ul class="unstyled">
            <?php 
            foreach($metaObj->getInternalLinks() as $lin){
                echo '<li><a href="'.$lin.'">'.$lin.'</a></li>';
            }
            ?>
            </ul>
        </div>
        <?php }?>
    </div>
  </div>
</div>
</div>    
<?php include_once 'footer.php';?>
