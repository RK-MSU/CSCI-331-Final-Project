<?php
$file_name = 'FILE NAME';
?>
<div class="d-flex flex-column p-2 border border-2 border-grey bg-light rounded shadow">
    <div class="flex-fill">
        <div class="d-flex justify-content-between ">
            <div class="align-self-center">
                <span class="text-muted"><?= $file_name ?></span>
            </div>
            <div class="align-self-center">
                <button class="btn">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn">
                    <i class="fas fa-minus-circle"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="d-flex bg-white rounded p-2">
        <img src="<?=$file_url?>" class="align-self-center m-auto" style="max-width: 200px; max-height: 200px;">
    </div>
</div>


<?php

$_meta = [
    'entry_id' => $entry_id,
    'field_id' => $field_id,
    'field_settings' => $field_settings,
];

$_meta = ee('Encrypt')->encode(json_encode($_meta));

$file = [
    'title' => (isset($file_title) && !empty($file_title)) ? $file_title : null,
    'src' => (isset($file_url) && !empty($file_url)) ? $file_url : null,
];

$field = [
//     'id' => (isset($file_id) && !empty($file_id)) ? $file_id : null,
    'value' => (isset($field_value) && !empty($field_value)) ? $field_value : null,
//     'name' => (isset($field_name) && !empty($field_name)) ? $field_name : null,
    'meta' => $_meta,
];



?>
<div data-ng-controller="fileInputField" data-ng-init="_file='<?php echo base64_encode(json_encode($file, TRUE)); ?>'; _field='<?php echo base64_encode(json_encode($field, TRUE)); ?>'">
	<div class="bg-light rounded border border-2 p-1">
		<div class="d-flex flex-column px-1">
			<div class="d-flex flex-row py-1">
				<span class="text-muted">{{file.title}}</span>
			</div>
			<div class="d-flex justify-content-center bg-white p-3 rounded">
				<img data-ng-src="{{file.src}}" class="rounded shadow" width="200" height="200">
			</div>
			<div class="d-flex flex-row">
				<button type="button" class="btn btn-outline-secondary" ng-click="chooseExisting()">Choose Existing</button>
				<button type="button" class="btn btn-outline-secondary" ng-click="uploadNew()">Upload New</button>
			</div>
		</div>
    	<input type="text" ng-model="field.value" name="<?=$field_name?>" class="d-none">
	</div>
</div>


