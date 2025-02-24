{strip}
<div class="modal-dialog modal-content modal-width-900">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_SEND_SMS', $MODULE_NAME)}" }
    <form id="form-send-sms" class="form-horizontal sendSMSModal form-modal" method="POST">
        <!-- <input type="hidden" name="module" value="{$MODULE}" /> -->
        <div class="taskTypeUi">
            <div id="VtSMSTaskContainer" class="form-content" style="padding-left: 20px; padding-right: 20px;">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="row form-group">
                            <div class="col-sm-8 col-xs-8">
                                <div class="row">
                                    <div class="col-sm-3 col-xs-3">{vtranslate('LBL_TASK_NAME',$MODULE_NAME)}<span class="redColor">*</span></div>
                                    <div class="col-sm-9 col-xs-9"><input name="titleSMS" class="inputElement" data-rule-required="true" type="text" /></div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-2">{vtranslate('LBL_RECEIVER',$MODULE_NAME)}<span class="redColor">*</span></div>
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <input type="text" class="inputElement fields" data-rule-required="true" name="sms_recepient" value="{$TASK_OBJECT->sms_recepient}" />
                                    </div>
                                    <div class="col-lg-6">
                                        <select class="select2 task-fields" style="min-width: 170px;" data-placeholder="{vtranslate('LBL_SELECT_FIELDS', $MODULE_NAME)}">
                                            <option></option>
                                            {$PHONE_FIELD_OPTIONS}
                                        </select>	
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-2">{vtranslate('LBL_INSERT_VARIABLE',$MODULE_NAME)}</div>
                            <div class="col-lg-10">
                                <select class="select2" id="task-fieldnames" style="min-width: 150px;" data-placeholder="{vtranslate('LBL_SELECT_FIELDS', $MODULE_NAME)}">
                                    <option></option>
                                    {$ALL_FIELD_OPTIONS}
                                </select>	
                            </div>
                            <div class="col-lg-2"> &nbsp; </div>
                            <div class="col-lg-10"> &nbsp; </div>
                            <div class="col-lg-2">{vtranslate('LBL_CONTENT_SMS',$MODULE_NAME)}</div>
                            <div class="col-lg-6">
                                <textarea name="content" class="inputElement fields" style="height: inherit;">{$TASK_OBJECT->content}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}