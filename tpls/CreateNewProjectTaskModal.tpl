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
                    <input type="text" class="inputElement w50" data-rule-required="true">
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_TITLE', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <input type="text" class="inputElement w50" data-rule-required="true">
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_TYPE', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <select class="inputElement text-left select2 max-width-295" data-rule-required="true">
                        <option value="1">Admin(HCNS, KT,...)</option>
                        <option value="2">Vận hành</option>
                        <option value="3">Khác</option>
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
							<input name="project_id" type="hidden" class="sourceField" value="" data-displayvalue="" />
							<input id="project_id_display" name="project_id_display" type="text" 
								class="marginLeftZero autoComplete inputElement ui-autocomplete-input" 
								value="" placeholder="{vtranslate('LBL_ENTER_TO_SEARCH', $MODULE_NAME)}" autocomplete="off"
								data-fieldname="project_id" data-fieldtype="reference" />
							<a href="#" class="clearReferenceSelection hide">&nbsp;x&nbsp;</a>
							<span class="input-group-addon relatedPopup cursorPointer" title="{vtranslate('LBL_ENTER_TO_SEARCH', $MODULE_NAME)}">
								<i id="project_id_select" class="fa fa-search"></i>
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
                        <input name="days" type="text" class="inputElement w20" data-rule-required="true">
                        <span class="ml-3 mr-3">{vtranslate('LBL_DATE', $MODULE_NAME)}</span>
                        <select name="direction" class="inputElement select2 mr-3 w50" tabindex="-1" data-rule-required="true">
                            <option value="after">{vtranslate('LBL_AFTER', $MODULE_NAME)}</option>
                            <option value="before">{vtranslate('LBL_BEFORE', $MODULE_NAME)}</option>
                        </select>
                        <select name="datefield" class="inputElement select2" tabindex="-1" data-rule-required="true">
                            {foreach from=$DATETIME_FIELDS item=DATETIME_FIELD}
                                <option {if $TASK_OBJECT->datefield eq $DATETIME_FIELD->get('name')}selected{/if} value="{$DATETIME_FIELD->get('name')}">{vtranslate($DATETIME_FIELD->get('label'), $DATETIME_FIELD->getModuleName())}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_START_DATE', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-6">
                    <div class="input-group inputElement" style="margin-bottom: 3px">
                        <input type="text" name="inputDate" class="form-control datePicker" 
                            data-fieldtype="date" data-rule-required="true" />
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
                        <input type="text" name="inputDate" class="form-control datePicker" 
                            data-fieldtype="date" data-rule-required="true" />
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
                    <select class="inputElement text-left select2 max-width-295" data-rule-required="true">
                        <option value="notStart">{vtranslate('LBL_NOT_STARTED', $MODULE_NAME)}</option>
                        <option value="proceeding">{vtranslate('LBL_PROCEEDING', $MODULE_NAME)}</option>
                        <option value="complete">{vtranslate('LBL_COMPLETE', $MODULE_NAME)}</option>
                        <option value="postpone">{vtranslate('LBL_POSTPONE', $MODULE_NAME)}</option>
                        <option value="canceled">{vtranslate('LBL_CANCELED', $MODULE_NAME)}</option>
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
                    <input type="text" class="inputElement max-width-295" data-rule-required="true">
                    <div class="checkbox-label mt-2">
                        <input type="checkbox"> 
                        {vtranslate('LBL_THE_PERSON_IN_CHARGE_OF_THE_FATHER_RECORD', $MODULE_NAME)}
                    </div>
                </div>
            </div>
        </div>
        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}