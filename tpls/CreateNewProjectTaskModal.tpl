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
                    <input type="text" class="inputElement w50">
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_TITLE', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <input type="text" class="inputElement w50">
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
                    {vtranslate('LBL_PROJECT', $MODULE_NAME)}
                </div>
                <div class="controls fieldValue col-sm-8">
                    <div class="referencefield-wrapper">
						<input name="popupReferenceModule" type="hidden" value="Project" />
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
                    {vtranslate('LBL_DELIVER', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <input type="text" class="inputElement max-width-295">
                    <div class="checkbox-label mt-2">
                        <input type="checkbox"> 
                        {vtranslate('LBL_THE_PERSON_IN_CHARGE_OF_THE_FATHER_RECORD', $MODULE_NAME)}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_LEAD_TIME', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-6">
                    <div class="input-group inputElement time" >
                        <input type="text" name="typeTime" class="timepicker-default form-control" data-format="12" data-rule-required="true"/>
                        <span class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </span>
                    </div>
                    <span data-toggle="tooltip" style="margin-top:7px; margin-left: 20px;" data-tippy-content="{vtranslate('LBL_LEAD_TIME_NOTE', $MODULE_NAME)}">
                        <i class="far fa-info-circle"></i>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_STARTING_DATE_OF_PLANNING', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-6">
                    <div class="input-group inputElement" style="margin-bottom: 3px">
                        <input type="text" name="inputDate" class="form-control datePicker" 
                            data-fieldtype="date" data-rule-required="true" />
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_ENDING_DATE_OF_PLANNING', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-6">
                    <div class="input-group inputElement" style="margin-bottom: 3px">
                        <input type="text" name="inputDate" class="form-control datePicker" 
                            data-fieldtype="date" data-rule-required="true" />
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
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
        </div>
        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}