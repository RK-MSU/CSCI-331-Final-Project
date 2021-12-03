<?php 
$id = null;
$title = (isset($title) && !empty($title)) ? $title : 'DEFAULT Title';
$body = (isset($body)) ? $body : 'DEFAULT Body - {{ee}}';
$footer = (isset($footer)) ? $footer : 'DEFAULT footer';

$modal_dialog = "modal-dialog";
$modal_body_class = "modal-body";

// centered
if(isset($centered) && !empty($centered) && $centered == true) {
    $modal_dialog .= "  modal-dialog-centered";
}
// scrollable
if(isset($scrollable) && !empty($scrollable) && $scrollable == true) {
    $modal_dialog .= " modal-dialog-scrollable";
}
// fullscreen
if(isset($fullscreen) && !empty($fullscreen)) {
    if($fullscreen == true) {
        $modal_dialog .= " modal-fullscreen";
    } else if (in_array($fullscreen, ['modal-fullscreen-sm-down', 'modal-fullscreen-md-down', 'modal-fullscreen-lg-down', 'modal-fullscreen-xl-down', 'modal-fullscreen-xxl-down'])) {
        $modal_dialog .= " " . $fullscreen;
    }
}
// size
if(isset($size) && !empty($size) && in_array($size, ['sm', 'lg', 'xl'])) {
    $modal_dialog .= " modal-${size}";
}

?>
<div class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="<?=$modal_dialog?>">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?=$title?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="<?=$modal_body_class?>">
                <?=$body?>
            </div>
            <?php if(isset($footer) && !empty($footer)): ?>
            <div class="modal-footer">
                <?=$footer?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>