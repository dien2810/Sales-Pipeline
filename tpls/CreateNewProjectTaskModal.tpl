{* Added by Minh Hoang on 2021-02-03 *}

{strip}
<div class="modal-dialog modal-content modal-width-900">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_CREATE_NEW_PROJECT_TASK', $MODULE_NAME)}" }
    <form id="form-create-new-project-task" class="form-horizontal createNewProjectTaskModal form-modal" method="POST">
        <div class="form-content">
            <input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_TASK_NAME', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <input name="action_name" type="text" class="inputElement w50" data-rule-required="true">
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_TITLE', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <input name="projecttaskname" type="text" class="inputElement w50" data-rule-required="true">
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_TYPE', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <select name="projecttasktype" class="inputElement text-left select2 max-width-295" data-rule-required="true">
                        {assign var=TYPE_PICKLIST_VALUES value=$PROJECT_TASK_TYPE_VALUES}
                        {foreach  from=$TYPE_PICKLIST_VALUES item=TYPE_PICKLIST_VALUE key=TYPE_PICKLIST_KEY}
                            <option value="{$TYPE_PICKLIST_KEY}">{$TYPE_PICKLIST_VALUE}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_PROJECT', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls fieldValue col-sm-8">
                    <div class="referencefield-wrapper">
						<input name="popupReferenceModule" type="hidden" value="Project" data-rule-required="true"/>
						<div class="input-group">
							<input name="projectid" type="hidden" class="sourceField" value="" data-displayvalue="" />
							<input id="projectid_display" name="projectid_display" type="text" 
								class="marginLeftZero autoComplete inputElement ui-autocomplete-input" 
								value="" placeholder="{vtranslate('LBL_ENTER_TO_SEARCH', $MODULE_NAME)}" autocomplete="off"
								data-fieldname="projectid" data-fieldtype="reference" />
							<a href="#" class="clearReferenceSelection hide">&nbsp;x&nbsp;</a>
							<span class="input-group-addon relatedPopup cursorPointer" title="{vtranslate('LBL_ENTER_TO_SEARCH', $MODULE_NAME)}">
								<i id="projectid_select" class="fa fa-search"></i>
							</span>
						</div>
					</div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_ENDING_DATE_OF_PLANNING', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <div class="d-flex align-item-center justify-content-center">
                        <input name="endDays" type="text" class="inputElement w20" data-rule-required="true">
                        <span class="ml-3 mr-3">{vtranslate('LBL_DATE', $MODULE_NAME)}</span>
                        <select name="endDateDirection" class="inputElement select2 mr-3 w50" tabindex="-1" data-rule-required="true">
                            <option value="after">{vtranslate('LBL_AFTER', $MODULE_NAME)}</option>
                            <option value="before">{vtranslate('LBL_BEFORE', $MODULE_NAME)}</option>
                        </select>
                        <select name="endDatefield" class="inputElement select2" tabindex="-1" data-rule-required="true">
                            {foreach from=$DATETIME_FIELDS item=DATETIME_FIELD}
                                <option value="{$DATETIME_FIELD->get('name')}">{vtranslate($DATETIME_FIELD->get('label'), $DATETIME_FIELD->getModuleName())}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <div id="extraInfo" style="display: none;">
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_START_DATE', $MODULE_NAME)}
                    </div>
                    <div class="controls col-sm-6">
                        <div class="input-group inputElement" style="margin-bottom: 3px">
                            <input type="text" name="startdate" class="form-control datePicker" 
                                data-fieldtype="date"/>
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                        <span data-toggle="tooltip" style="margin-top:7px; margin-left: 20px;" data-tippy-content="{vtranslate('LBL_START_DATE_NOTE', $MODULE_NAME)}">
                            <i class="far fa-info-circle"></i>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_END_DATE', $MODULE_NAME)}
                    </div>
                    <div class="controls col-sm-6">
                        <div class="input-group inputElement" style="margin-bottom: 3px">
                            <input type="text" name="enddate" class="form-control datePicker" 
                                data-fieldtype="date"/>
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                        <span data-toggle="tooltip" style="margin-top:7px; margin-left: 20px;" data-tippy-content="{vtranslate('LBL_END_DATE_NOTE', $MODULE_NAME)}">
                            <i class="far fa-info-circle"></i>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_STATE', $MODULE_NAME)}
                    </div>
                    <div class="controls col-sm-8">
                        <select name="projecttaskstatus" class="inputElement text-left select2 max-width-295" data-rule-required="true">
                            {assign var=STATUS_PICKLIST_VALUES value=$PROJECT_TASK_STATUS_VALUES}
                            {foreach  from=$STATUS_PICKLIST_VALUES item=STATUS_PICKLIST_VALUE key=STATUS_PICKLIST_KEY}
                                <option value="{$STATUS_PICKLIST_KEY}">{$STATUS_PICKLIST_VALUE}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_DESCRIBE', $MODULE_NAME)}
                    </div>
                    <div class="controls col-sm-8">
                        <textarea rows="3" class="w50 resize-vertical" name="description"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_DELIVER', $MODULE_NAME)}
                        <span class="redColor">*</span>
                    </div>
                    <div class="controls col-sm-8">
                        {include file="layouts/v7/modules/Settings/Workflows/Tasks/CustomOwnerField.tpl" FIELD_VALUE=$TASK_OBJECT->assigned_user_id}
                        <div class="checkbox-label mt-2">
                            <label><input name="assign_parent_record_owners" type="checkbox"> 
                            {vtranslate('LBL_THE_PERSON_IN_CHARGE_OF_THE_FATHER_RECORD', $MODULE_NAME)}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <center>
                {if $BUTTON_NAME neq null}
                    {assign var=BUTTON_LABEL value=$BUTTON_NAME}
                {else}
                    {assign var=BUTTON_LABEL value={vtranslate('LBL_SAVE', $MODULE)}}
                {/if}
                <button href="#" class="btn cancelLink" type="reset" data-dismiss="modal">Hủy</button>
                <button class="btn btn-default" id="fullInfo" type="button"><strong>Toàn bộ thông tin</strong></button>
                <button {if $BUTTON_ID neq null} id="{$BUTTON_ID}" {/if} class="btn btn-primary" type="submit" name="saveButton"><strong>{$BUTTON_LABEL}</strong></button>
            </center>
        </div>
    </form>
</div>
{/strip}