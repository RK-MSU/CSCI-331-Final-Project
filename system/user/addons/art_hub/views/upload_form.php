<?php

// $base_url = ee('CP/URL')->make('cp/addons/settings/art_hub/do_upload')->compile();
$base_url = ee()->config->item('site_url') . ee()->config->item('site_index');
$attrs = array();
$hidden = array();


$title = null;
$desc = null;
$credit = null;
$location = null;

?>
<?php if(isset($error) && !empty($error)){echo $error;}?>
<?php echo form_open_multipart($base_url, $attrs, $hidden);?>
<div class="mb-3">
    <label if="userFile">File</label>
    <div id="userFileHelp" class="form-text">Choose a file to upload.</div>
    <div class="input-group">
        <input type="file" id="userFile" name="userFile" class="form-control" aria-describedby="userFileHelp" required>
    </div>
</div>
<div class="mb-3">
    <label for="fileTitle">Title</label>
    <input type="text" id="fileTitle" name="file_title" value="<?=$title?>" class="form-control" />
</div>
<div class="mb-3">
    <label for="fileDescription">Description</label>
    <textarea type="text" id="fileDescription" name="file_desc" class="form-control"><?=$desc?></textarea>
</div>
<div class="mb-3">
    <label for="fileCredit">Credit</label>
    <input type="text" id="fileCredit" name="file_credit" value="<?=$credit?>" class="form-control" />
</div>
<div class="mb-3">
    <label for="fileLocation">Location</label>
    <input type="text" id="fileLocation" name="file_location" value="<?=$location?>" class="form-control" />
</div>
<button type="submit" class="btn btn-primary">Upload File</button>
</form>