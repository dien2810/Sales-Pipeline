{* Added by Minh Hoang on 2021-01-23 *}

{strip}
<div class="modal-dialog modal-content modal-width-900">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_SEND_SMS', $QUALIFIED_MODULE)}" }
    <form id="form-send-sms" class="form-horizontal sendSMSModal form-modal" method="POST">
        <!-- <input type="hidden" name="module" value="{$MODULE}" /> -->
        <div class="taskTypeUi">
            <div id="VtSMSTaskContainer" class="form-content" style="padding-left: 20px; padding-right: 20px;">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="row form-group">
                            <div class="col-sm-8 col-xs-8">
                                <div class="row">
                                    <div class="col-sm-3 col-xs-3">{vtranslate('LBL_TASK_NAME',$QUALIFIED_MODULE)}<span class="redColor">*</span></div>
                                    <div class="col-sm-9 col-xs-9"><input name="titleSMS" class="inputElement" data-rule-required="true" type="text" /></div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-2">{vtranslate('LBL_RECEIVER',$QUALIFIED_MODULE)}<span class="redColor">*</span></div>
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <input type="text" class="inputElement fields" data-rule-required="true" name="sms_recepient" value="{$TASK_OBJECT->sms_recepient}" />
                                    </div>
                                    <div class="col-lg-6">
                                        <select class="select2 task-fields" style="min-width: 170px;" data-placeholder="{vtranslate('LBL_SELECT_FIELDS', $QUALIFIED_MODULE)}">
                                            <option></option>
                                            {* Modified by Hieu Nguyen on 2020-07-01 to display field label clearly and make code more readble *}
                                            {foreach key=META_KEY item=FIELD_MODEL from=$RECORD_STRUCTURE_MODEL->getFieldsByType('phone')}
                                                <option value=",${$META_KEY}">{$FIELD_MODEL->get('workflow_columnlabel')}</option>
                                            {/foreach}
                                            {* End Hieu Nguyen *}
                                        </select>	
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-2">{vtranslate('LBL_INSERT_VARIABLE',$QUALIFIED_MODULE)}</div>
                            <div class="col-lg-10">
                                <select class="select2" id="task-fieldnames" style="min-width: 150px;" data-placeholder="{vtranslate('LBL_SELECT_FIELDS', $QUALIFIED_MODULE)}">
                                    <option></option>
                                    {$ALL_FIELD_OPTIONS}
                                </select>	
                            </div>
                            <div class="col-lg-2"> &nbsp; </div>
                            <div class="col-lg-10"> &nbsp; </div>
                            <div class="col-lg-2">{vtranslate('LBL_CONTENT_SMS',$QUALIFIED_MODULE)}</div>
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