{strip}
<div class="modal-dialog modal-content modal-width-1100">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_ADD_MEETING', $MODULE_NAME)}" }
    <form id="form-add-meeting" class="form-horizontal addCallModal form-modal" method="POST">
        <div class="form-content">
            <input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_TASK_NAME', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <input type="text" class="inputElement w40">
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_ACTIVITY_NAME', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <input type="text" class="inputElement w40">
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_DESCRIBE', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-8">
                    <textarea rows="3" class="w40 resize-vertical" name="description"></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_STATE', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-8 w3">
                    <select class="inputElement text-left select2 w40" data-rule-required="true">
                        <option value="plan">{vtranslate('LBL_PLAN', $MODULE_NAME)}</option>
                        <option value="ended">{vtranslate('LBL_ENDED', $MODULE_NAME)}</option>
                        <option value="cancel">{vtranslate('LBL_CANCEL', $MODULE_NAME)}</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_KIND', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-8">
                    <select class="inputElement text-left select2 w40" data-rule-required="true">
                        <option value="call">{vtranslate('LBL_CALL', $MODULE_NAME)}</option>
                        <option value="meeting">{vtranslate('LBL_MEETING', $MODULE_NAME)}</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_DELIVER', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <input type="text" class="inputElement w40">
                    <div class="checkbox-label mt-2">
                        <input type="checkbox"> 
                        {vtranslate('LBL_THE_PERSON_IN_CHARGE_OF_THE_FATHER_RECORD', $MODULE_NAME)}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_START_TIME', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-6">
                    <div class="input-group inputElement time w60">
                        <input type="text" name="typeTime" class="timepicker-default form-control" data-format="12" data-rule-required="true"/>
                        <span class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_START_DATE', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-8">
                    <div class="d-flex align-item-center justify-content-center">
                        <input type="text" class="inputElement w10">
                        <span class="ml-3 mr-3">{vtranslate('LBL_DATE', $MODULE_NAME)}</span>
                        <select class="inputElement select2 mr-3 w20" tabindex="-1">
                            <option value="after">{vtranslate('LBL_AFTER', $MODULE_NAME)}</option>
                            <option value="before">{vtranslate('LBL_BEFORE', $MODULE_NAME)}</option>
                        </select>
                        <select class="inputElement select2 w40" tabindex="-1">
                            <option value="create">{vtranslate('LBL_DAY_CREATION', $MODULE_NAME)}</option>
                            <option value="update">{vtranslate('LBL_REPAIR_DATE', $MODULE_NAME)}</option>
                            <option value="update">Các field datetime có trong module được áp dụng pipeline</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_END_TIME', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-6">
                    <div class="input-group inputElement time w60">
                        <input type="text" name="typeTime" class="timepicker-default form-control" data-format="12" data-rule-required="true"/>
                        <span class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_END_DATE', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-8">
                    <div class="d-flex align-item-center justify-content-center">
                        <input type="text" class="inputElement w10">
                        <span class="ml-3 mr-3">{vtranslate('LBL_DATE', $MODULE_NAME)}</span>
                        <select class="inputElement select2 mr-3 w20" tabindex="-1">
                            <option value="after">{vtranslate('LBL_AFTER', $MODULE_NAME)}</option>
                            <option value="before">{vtranslate('LBL_BEFORE', $MODULE_NAME)}</option>
                        </select>
                        <select class="inputElement select2 w40" tabindex="-1">
                            <option value="create">{vtranslate('LBL_DAY_CREATION', $MODULE_NAME)}</option>
                            <option value="update">{vtranslate('LBL_REPAIR_DATE', $MODULE_NAME)}</option>
                            <option value="update">Các field datetime có trong module được áp dụng pipeline</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_ACTIVATE_REPEAT_MODE', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-8">
                    <input id="toggleCheckbox" class="inputElement mt-2" type="checkbox">
                    <div id="toggleContent" class="mt-2 hide">
                        <div class="d-flex align-item-center justify-content-center">
                            <span class="mr-3">{vtranslate('LBL_WHENEVER', $MODULE_NAME)}</span>
                            <select class="inputElement select2 mr-3 w10" tabindex="-1">
                                {for $i=1 to 14}
                                    <option value="{$i}">{$i}</option>
                                {/for}
                            </select>                        
                            <select class="inputElement select2 w20" tabindex="-1">
                                <option value="day">{vtranslate('LBL_DAY', $MODULE_NAME)}</option>
                                <option value="week">{vtranslate('LBL_WEEK', $MODULE_NAME)}</option>
                                <option value="month">{vtranslate('LBL_MONTH', $MODULE_NAME)}</option>
                                <option value="update">{vtranslate('LBL_YEAR', $MODULE_NAME)}</option>
                            </select>
                            <span class="ml-3 mr-3">{vtranslate('LBL_UNTIL', $MODULE_NAME)}</span>
                            <div class="input-group inputElement w30" style="margin-bottom: 3px">
                                <input type="text" name="inputDate" class="form-control datePicker" 
                                    data-fieldtype="date" data-rule-required="true" />
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_SHARE_MODE', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <select class="inputElement text-left select2 w40" data-rule-required="true">
                        <option value="private">{vtranslate('LBL_PRIVATE', $MODULE_NAME)}</option>
                        <option value="public">{vtranslate('LBL_PUBLIC', $MODULE_NAME)}</option>
                    </select>
                </div>
            </div>
        </div>
        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}