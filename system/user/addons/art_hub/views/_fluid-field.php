<?php 
$field_meta = [
    'name' => $field_name,
    'children' => [],
    'settings' => [
        'child_fields' => $child_fields,
    ]
];
$fieldset_class = (isset($field_required) && $field_required) ? 'fieldset-required' : 'fieldset';
// field_id_24[fields][field_1][field_id_1]
?>
<fieldset class="<?=$fieldset_class?>" ng-controller="fluidFieldController" ng-init="_field='<?php echo base64_encode(json_encode($field_meta)); ?>'">
    <div class="field-instruct">
        <label><?=$field_label?></label>
        <em><?=$field_instructions?></em>
    </div>
    <div class="card">
    	<div class="card-body bg-light">
    		<div class="d-flex flex-column" data-ng-show="field.children.length > 0">
            	<!-- fluid field children -->
            	<div class="d-flex flex-column p-1">
            		<div ng-repeat="ft in field.children track by ft.row_name" class="card mb-2">
            			<fieldset ng-class="{'fieldset-required' : ft.required, 'fieldset' : !ft.required}" class="list-group list-group-flush">
            				<div class="list-group-item d-flex flex-row justify-content-between">
            					<div class="field-instruct">
                    				<label ng-bind="ft.label"></label>
                    				<em ng-bind="ft.instructions"></em>        				
                				</div>
                				<div class="btn-group align-self-center" role="group">
									<button type="button" class="btn btn-sm btn-outline-secondary" ng-click="removeChildField(ft)"><i class="fas fa-sm fa-trash-alt"></i></button>
								</div>
            				</div>
            				<div ng-show="ft.type == 'file'" class="list-group-item p-3">
            					<div class="file-input-field">
            						<span ng-bind="ft.file.title" class="file-title"></span>
                					<div ng-class="{'d-none' : ft.file.src != null, 'd-flex justify-content-center': ft.file.src == null}">
                    					<p class="py-2">No File Selected</p>
                					</div>
                					<div class="file-preview" ng-class="{'d-none' : ft.file.src == null}">
                						<img data-ng-src="{{ft.file.src}}" ng-show="ft.file.src != null">
                					</div>
                					<div class="file-button-con">
                                    	<button type="button" ng-click="chooseExisting(ft)">Choose Existing</button>
                                    	<button type="button" ng-click="uploadNew(ft)">Upload New</button>
                                	</div>
            					</div>
            				</div>
            			</fieldset>
            			<input type="text" name="{{field.name}}[fields][{{ft.row_name}}][{{ft.name}}]" ng-model="ft.value" class="d-none">
            		</div>
            	</div>
            </div>
            <!-- fluid field children -->
        	<div class="d-flex flex-row">
        		<button type="button" class="btn btn-outline-secondary" ng-repeat="ft in field.settings.child_fields track by ft.id" ng-click="addField(ft)">Add {{ft.label}}</button>
        	</div>
    	</div>
    </div>
</fieldset>