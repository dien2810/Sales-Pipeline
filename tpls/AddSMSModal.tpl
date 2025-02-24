{strip}
<div class="modal-dialog modal-content modal-width-900">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_SEND_SMS', $MODULE_NAME)}" }
    <form id="form-add-sms" class="form-horizontal addSMSModal form-modal" method="POST">
        <div>
            <input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_TASK_NAME', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <input type="text" class="inputElement">
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_RECEIVER', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <div class="d-flex align-item-center">
                        <input type="text" class="inputElement">
                        <select class="inputElement select2 ml-3" tabindex="-1">
                            <option value="">{vtranslate('LBL_CHOOSE_FIELD', $MODULE_NAME)}</option>
                            <option value="value1">Option 1</option>
                            <option value="value2">Option 2</option>
                            <option value="value3">Option 3</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_INSERT_VARIABLE', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-8">
                    <select class="inputElement text-left select2" data-rule-required="true">
                        <option value="">{vtranslate('LBL_CHOOSE_FIELD', $MODULE_NAME)}</option>
                        <option value="value1">Option 1</option>
                        <option value="value2">Option 2</option>
                        <option value="value3">Option 3</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_CONTENT_SMS', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-8">
                    <textarea rows="3" cols="12" class="inputElement textAreaElement col-lg-12" name="description"></textarea>
                </div>
            </div>
        </div>
        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}