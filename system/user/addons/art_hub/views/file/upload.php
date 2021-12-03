<?php

$meta = [
    'site_id'           => $site_id,
    'member_id'         => $member_id,
    'upload_directory'  => $upload_directory,
    'field_content_type' => $field_content_type
];
$meta = ee('Encrypt')->encode(json_encode($meta));

$base_url = ee()->config->item('site_url') . ee()->config->item('site_index');
$attrs = (!isset($attrs) || !is_array($attrs)) ? [] : $attrs;
$hidden = (!isset($hidden) || !is_array($hidden)) ? [] : $hidden;

$attrs += array(
    'method' => 'post',
    'enctype' => 'multipart/form-data'
);
$hidden += array(
    'ACT' => ee()->art_hub->fetch_action_id('Art_hub', 'upload_file'),
    'meta' => $meta
);


$title = (!isset($title) || empty($title)) ? null : $title;
$desc = (!isset($desc) || empty($desc)) ? null : $desc;
$credit = (!isset($credit) || empty($credit)) ? null : $credit;
$location = (!isset($location) || empty($location)) ? null : $location;

?>

<?php if(isset($error) && !empty($error)){echo $error;}?>

<?php echo form_open_multipart($base_url, $attrs, $hidden);?>
<fieldset class="fieldset-required">
    <div class="field-instruct">
        <label for="upload_file">File</label>
        <em id="uploadFileHelp">Choose a file to upload.</em>
    </div>
    <div class="fieldset-control">
    	<div class="input-group">
    		<input type="file" name="userFile" id="upload_file" value="" class="form-control" aria-describedby="uploadFileHelp" required>
    	</div>
    </div>
</fieldset>
<fieldset class="fieldset">
    <div class="field-instruct">
        <label for="upload_file_title">Title</label>
    </div>
    <div class="fieldset-control">
		<input type="text" name="title" id="upload_file_title" value="<?=$title?>">
    </div>
</fieldset>

<fieldset class="fieldset">
    <div class="field-instruct">
        <label for="upload_file_description">Description</label>
    </div>
    <div class="fieldset-control">
		<textarea name="desc" id="upload_file_description"><?=$desc?></textarea>
    </div>
</fieldset>

<fieldset class="fieldset">
    <div class="field-instruct">
        <label for="upload_file_credit">Credit</label>
    </div>
    <div class="fieldset-control">
		<input type="text" name="credit" id="upload_file_credit" value="<?=$credit?>">
    </div>
</fieldset>

<fieldset class="fieldset">
    <div class="field-instruct">
        <label for="upload_file_location">Location</label>
    </div>
    <div class="fieldset-control">
		<input type="text" name="location" id="upload_file_location" value="<?=$location?>">
    </div>
</fieldset>