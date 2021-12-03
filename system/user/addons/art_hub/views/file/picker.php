<?php 

$files_meta = base64_encode(json_encode($files));
$filters_meta = base64_encode(json_encode($filters));
$view = $default_modal_view;

$ng_init = "ng-init=\"_files='${files_meta}'; " .
    "_filters='${filters_meta}'; " . 
    "view='${view}'\";";

?>
<div ng-controller="filePicker" <?=$ng_init?> class="file-picker">
	<div class="d-flex flex-column justify-content-center">
		<div class="flex-fill d-flex flex-row">
			<div class="m-3">
				<input ng-model="filters.search" type="text" class="form-control" placeholder="Search">
			</div>
			<div class="m-3">
				<button class="btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="List View" ng-hide="view=='list'" ng-click="view='list'">
    				<i class="fas fa-list"></i>
    			</button>
    			<button class="btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Grid View" ng-hide="view=='thumb'" ng-click="view='thumb'">
    				<i class="fas fa-grip-horizontal"></i>
    			</button>
			</div>
<!-- 			<div class="m-3"> -->
<!-- 				<div class="dropdown"> -->
<!--               		<button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"> -->
<!--                     	Show (25) -->
<!--                   	</button> -->
<!--                   	<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"> -->
<!--                         <li><a class="dropdown-item" href="#">Show (25)</a></li> -->
<!--                         <li><a class="dropdown-item" href="#">Show (50)</a></li> -->
<!--                         <li><a class="dropdown-item" href="#">Show (75)</a></li> -->
<!--                         <li><a class="dropdown-item" href="#">Show (100)</a></li> -->
<!--                         <li><a class="dropdown-item" href="#">All (#)</a></li> -->
<!--                   	</ul> -->
<!--                 </div> -->
<!-- 			</div> -->
		</div>	
		<div class="d-flex flex-row flex-wrap justify-content-center" ng-show="view=='thumb'">
			<img ng-repeat="img in files track by img.id" ng-click="selectFile(img)" ng-src="{{img.src}}" class="select-file-img" width="150" height="150">
        </div>
        
        <table class="table table-hover" ng-show="view=='list'">
        	<thead>
        		<tr class="text-nowrap">
        			<td>Preview</td>
        			<td>Title/Name</td>
        			<td>Date Added</td>
        		</tr>
        	</thead>
        	<tbody>
        		<tr ng-repeat="img in files" ng-click="selectFile(img)">
        			<td><img ng-src="{{img.src}}" height="50" width="50"></td>
        			<td>
        				<span ng-bind="img.title" class="d-block"></span>
        				<em class="text-muted small" ng-bind="img.name"></em>
        			</td>
        			<td ng-bind="img.upload_date" class="text-nowrap"></td>
        		</tr>
        	</tbody>
        </table>
	</div>
</div>