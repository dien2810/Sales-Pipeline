{strip}
<div class="modal-dialog modal-content modal-width-900">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_CREATE_NEW_TASK', $MODULE_NAME)}" }
    <form id="form-create-new-task" class="form-horizontal createNewTaskModal form-modal" method="POST">
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
                    {vtranslate('LBL_STATE', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-8">
                    <select class="inputElement text-left select2 max-width-295" data-rule-required="true">
                        <option value="plan">{vtranslate('LBL_PLAN', $MODULE_NAME)}</option>
                        <option value="ended">{vtranslate('LBL_ENDED', $MODULE_NAME)}</option>
                        <option value="cancel">{vtranslate('LBL_CANCEL', $MODULE_NAME)}</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_PRIORITY', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-8">
                    <select class="inputElement text-left select2 max-width-295" data-rule-required="true">
                        <option value="high">{vtranslate('LBL_HIGH', $MODULE_NAME)}</option>
                        <option value="medium">{vtranslate('LBL_MEDIUM', $MODULE_NAME)}</option>
                        <option value="low">{vtranslate('LBL_LOW', $MODULE_NAME)}</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_DELIVER', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <input type="text" class="inputElement w50">
                    <div class="checkbox-label mt-2">
                        <input type="checkbox"> 
                        {vtranslate('LBL_THE_PERSON_IN_CHARGE_OF_THE_FATHER_RECORD', $MODULE_NAME)}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_TIME', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-6">
                    <div class="input-group inputElement time" >
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
                        <input type="text" class="inputElement w20">
                        <span class="ml-3 mr-3">{vtranslate('LBL_DATE', $MODULE_NAME)}</span>
                        <select class="inputElement select2 mr-3 w50" tabindex="-1">
                            <option value="after">{vtranslate('LBL_AFTER', $MODULE_NAME)}</option>
                            <option value="before">{vtranslate('LBL_BEFORE', $MODULE_NAME)}</option>
                        </select>
                        <select class="inputElement select2" tabindex="-1">
                            <option value="create">{vtranslate('LBL_DAY_CREATION', $MODULE_NAME)}</option>
                            <option value="update">{vtranslate('LBL_REPAIR_DATE', $MODULE_NAME)}</option>
                            <option value="update">Các field datetime có trong module được áp dụng pipeline</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_SEND_NOTIFICATION', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-8">
                    <input class="inputElement" type="checkbox">
                </div>
            </div>
        </div>
        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}