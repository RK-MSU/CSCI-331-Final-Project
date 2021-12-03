<?php
$status = (isset($status)) ? $status : 'success';
?>
<div class="container page-alert">
    <div class="alert alert-<?=$status?>" role="alert">
    <?php if(isset($title) && !empty($title)): ?><h5><?=$title?></h5><?php endif; ?>
    <?=$body?>
    </div>
</div>