{* Added by Minh Hoang on 2021-02-03 *}

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
                    <input name="action_name" type="text" class="inputElement w50">
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_TITLE', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <input name="todo" type="text" class="inputElement w50" data-rule-required="true">
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_END_DATE', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <div class="d-flex align-item-center justify-content-center">
                        <input name="days" type="text" class="inputElement w20" data-rule-required="true">
                        <span class="ml-3 mr-3">{vtranslate('LBL_DATE', $MODULE_NAME)}</span>
                        <select name="direction" class="inputElement select2 mr-3 w50" tabindex="-1">
                            <option value="after">{vtranslate('LBL_AFTER', $MODULE_NAME)}</option>
                            <option value="before">{vtranslate('LBL_BEFORE', $MODULE_NAME)}</option>
                        </select>
                        <select name="datefield" class="inputElement select2" tabindex="-1">
                            {foreach from=$DATETIME_FIELDS item=DATETIME_FIELD}
                                <option {if $TASK_OBJECT->datefield eq $DATETIME_FIELD->get('name')}selected{/if} value="{$DATETIME_FIELD->get('name')}">{vtranslate($DATETIME_FIELD->get('label'), $DATETIME_FIELD->getModuleName())}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <div id="extraInfo" style="display: none;">
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_STATE', $MODULE_NAME)}
                    </div>
                    <div class="controls col-sm-8">
                        <select name="status" class="inputElement text-left select2 max-width-295" data-rule-required="true">
                            {assign var=STATUS_PICKLIST_VALUES value=$TASK_TYPE_MODEL->getTaskBaseModule()->getField('taskstatus')->getPickListValues()}
                            {foreach  from=$STATUS_PICKLIST_VALUES item=STATUS_PICKLIST_VALUE key=STATUS_PICKLIST_KEY}
                                <option value="{$STATUS_PICKLIST_KEY}" {if $STATUS_PICKLIST_KEY eq $TASK_OBJECT->status} selected="" {/if}>{$STATUS_PICKLIST_VALUE}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_PRIORITY', $MODULE_NAME)}
                    </div>
                    <div class="controls col-sm-8">
                        <select name="priority" class="inputElement text-left select2 max-width-295" data-rule-required="true">
                            {assign var=PRIORITY_PICKLIST_VALUES value=$TASK_TYPE_MODEL->getTaskBaseModule()->getField('taskpriority')->getPickListValues()}
                            {foreach  from=$PRIORITY_PICKLIST_VALUES item=PRIORITY_PICKLIST_VALUE key=PRIORITY_PICKLIST_KEY}
                                <option value="{$PRIORITY_PICKLIST_KEY}" {if $PRIORITY_PICKLIST_KEY eq $TASK_OBJECT->priority} selected="" {/if}>{$PRIORITY_PICKLIST_VALUE}</option>
                            {/foreach}
                        </select>
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
                        {vtranslate('LBL_SEND_NOTIFICATION', $MODULE_NAME)}
                    </div>
                    <div class="controls col-sm-8">
                        <input name="sendNotification" class="inputElement" type="checkbox">
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