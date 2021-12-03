<?php

$meta = [
    'site_id'               => $site_id,
    'member_id'             => $member_id,
    'upload_directory'      => $upload_directory,
    'upload_directory_name' => $upload_directory_name,
    'default_modal_view'    => $default_modal_view,
    'field_content_type'    => $field_content_type,
    'num_existing'          => $num_existing
];

$meta = ee('Encrypt')->encode(json_encode($meta));

$file = [
    'title' => (isset($file_title) && !empty($file_title)) ? $file_title : null,
    'src'   => (isset($file_src) && !empty($file_src)) ? $file_src : null,
];
$field = [
    'value' => (isset($field_value) && !empty($field_value)) ? $field_value : null,
    'meta'  => $meta,
];

$file_meta = base64_encode(json_encode($file));
$field_meta = base64_encode(json_encode($field));

$ng_init = "ng-init=\"_file='${file_meta}'; _field='${field_meta}'\"";

// title: {{file.title}}<br>
// src: {{file.src}}<br>
?>
<div ng-controller="fileInputField" <?=$ng_init?> class="file-input-field">
    <span ng-bind="file.title" class="file-title"></span>
    <div ng-class="{'d-none' : file.src != null, 'd-flex justify-content-center': file.src == null}">
        <p class="py-2">No File Selected</p>
    </div>
    <div class="file-preview" ng-class="{'d-none' : file.src == null}">
        <?php if($file_is_image == true): ?>
        <img data-ng-src="{{file.src}}" ng-show="file.src != null">
        <?php else: ?>
        NEED TO SETUP file_is_image=false: {{file.src}}
        <?php endif; ?>
    </div>
    <div class="file-button-con">
        <button type="button" ng-click="chooseExisting()">Choose Existing</button>
        <button type="button" ng-click="uploadNew()">Upload New</button>
    </div>
    <input type="text" ng-model="field.value" name="<?=$field_name?>">
</div>
<?php 













?>