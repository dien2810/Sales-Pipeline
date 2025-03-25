{* Added by Minh Hoang on 2021-02-03 *}

{strip}
{assign var=ACTION_INFO value=$ACTION_DATA['callInfo']}
<div class="modal-dialog modal-content modal-width-1100">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_ADD_CALL', $MODULE_NAME)}" }
    <form id="form-add-call" class="form-horizontal addCallModal form-modal" method="POST">
        <div class="form-content">
            <input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_TASK_NAME', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <input name="action_name" value="{if isset($ACTION_DATA['action_name'])}{$ACTION_DATA['action_name']}{/if}" type="text" class="inputElement w40" data-rule-required="true">
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_ACTIVITY_NAME', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <input name="eventName" value="{if isset($ACTION_DATA['callInfo']['eventName'])}{$ACTION_DATA['callInfo']['eventName']}{/if}" type="text" class="inputElement w40" data-rule-required="true">
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_START_TIME', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-6">
                    <div class="input-group inputElement time w60">
                        <input value="{if isset($ACTION_DATA['callInfo']['startTime'])}{$ACTION_DATA['callInfo']['startTime']}{else}00:00{/if}" type="text" name="startTime" class="timepicker-default form-control" data-format="24" value="00:00" data-rule-required="true"/>
                        <span class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_START_DATE', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <div class="d-flex align-item-center justify-content-center">
                        <input name="startDate" type="text" class="inputElement w10" data-rule-required="true" value="{if isset($ACTION_DATA['callInfo']['startDays'])}{$ACTION_DATA['callInfo']['startDays']}{/if}">
                        <span class="ml-3 mr-3">{vtranslate('LBL_DATE', $MODULE_NAME)}</span>
                        <select name="startDirection" class="inputElement select2 mr-3 w20" tabindex="-1">
                            <option value="after" {if isset($ACTION_DATA['callInfo']['startDirection']) && $ACTION_DATA['callInfo']['startDirection'] eq 'after'}selected{/if}>{vtranslate('LBL_AFTER', $MODULE_NAME)}</option>
                            <option value="before" {if isset($ACTION_DATA['callInfo']['startDirection']) && $ACTION_DATA['callInfo']['startDirection'] eq 'before'}selected{/if}>{vtranslate('LBL_BEFORE', $MODULE_NAME)}</option>
                        </select>
                        <select name="startDateField" class="inputElement select2 w40" tabindex="-1">
                            {foreach from=$DATETIME_FIELDS item=DATETIME_FIELD}
                            <option {if isset($ACTION_DATA['callInfo']['startDatefield']) && $ACTION_DATA['callInfo']['startDatefield'] eq $DATETIME_FIELD->get('name')}selected{/if}
                                value="{$DATETIME_FIELD->get('name')}">{vtranslate($DATETIME_FIELD->get('label'), $DATETIME_FIELD->getModuleName())}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_CALL_DURATION', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <div class="d-flex align-item-center justify-content-center">
                        <input name="duration" type="number" min="0" class="inputElement w10" data-rule-required="true" value="{if isset($ACTION_DATA['callInfo']['duration'])}{$ACTION_DATA['callInfo']['duration']}{/if}">
                        <select name="durationUnit" class="inputElement select2 ml-3 w30" tabindex="-1">
                            <option value="minutes" {if isset($ACTION_DATA['callInfo']['durationUnit']) && $ACTION_DATA['callInfo']['durationUnit'] eq 'minutes'}selected{/if}>{vtranslate('LBL_MINUTES', $MODULE_NAME)}</option>
                            <option value="hours" {if isset($ACTION_DATA['callInfo']['durationUnit']) && $ACTION_DATA['callInfo']['durationUnit'] eq 'hours'}selected{/if}>{vtranslate('LBL_HOURS', $MODULE_NAME)}</option>
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
                        <input type="text" name="endTime" class="timepicker-default form-control" readonly data-format="12" value="{if isset($ACTION_DATA['callInfo']['endTime'])}{$ACTION_DATA['callInfo']['endTime']}{/if}" data-rule-required="true"/>
                        <span class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div id="extraInfo" style="display: none;">
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_STATE', $MODULE_NAME)}
                    </div>
                    <div class="controls col-sm-8 w3">
                    <select name="status" class="inputElement text-left select2 w40" data-rule-required="true">
                        <option value="Planned" {if isset($ACTION_DATA['callInfo']['status']) && $ACTION_DATA['callInfo']['status'] eq 'Planned'}selected{/if}>{vtranslate('LBL_PLAN', $MODULE_NAME)}</option>
                        <option value="Held" {if isset($ACTION_DATA['callInfo']['status']) && $ACTION_DATA['callInfo']['status'] eq 'Held'}selected{/if}>{vtranslate('LBL_ENDED', $MODULE_NAME)}</option>
                        <option value="Not Held" {if isset($ACTION_DATA['callInfo']['status']) && $ACTION_DATA['callInfo']['status'] eq 'Not Held'}selected{/if}>{vtranslate('LBL_CANCEL', $MODULE_NAME)}</option>
                    </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_SHARE_MODE', $MODULE_NAME)}
                        <span class="redColor">*</span>
                    </div>
                    <div class="controls col-sm-8">
                        <select name="visibility" class="inputElement text-left select2 w40" data-rule-required="true">
                            <option value="private" {if isset($ACTION_DATA['callInfo']['visibility']) && $ACTION_DATA['callInfo']['visibility'] eq 'private'}selected{/if}>{vtranslate('LBL_PRIVATE', $MODULE_NAME)}</option>
                            <option value="public" {if isset($ACTION_DATA['callInfo']['visibility']) && $ACTION_DATA['callInfo']['visibility'] eq 'public'}selected{/if}>{vtranslate('LBL_PUBLIC', $MODULE_NAME)}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_CALL_TYPE', $MODULE_NAME)}
                        <span class="redColor">*</span>
                    </div>
                    <div class="controls col-sm-8">
                        <select name="events_call_direction" class="inputElement text-left select2 w40" data-rule-required="true">
                            <option value="" {if !isset($ACTION_DATA['callInfo']['events_call_direction']) || $ACTION_DATA['callInfo']['events_call_direction'] eq ''}selected{/if}>{vtranslate('LBL_CHOOSE_A_VALUE', $MODULE_NAME)}</option>
                            <option value="incomingCall" {if isset($ACTION_DATA['callInfo']['events_call_direction']) && $ACTION_DATA['callInfo']['events_call_direction'] eq 'incomingCall'}selected{/if}>{vtranslate('LBL_INCOMING_CALL', $MODULE_NAME)}</option>
                            <option value="outgoingCall" {if isset($ACTION_DATA['callInfo']['events_call_direction']) && $ACTION_DATA['callInfo']['events_call_direction'] eq 'outgoingCall'}selected{/if}>{vtranslate('LBL_OUTGOING_CALL', $MODULE_NAME)}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_DELIVER', $MODULE_NAME)}
                        <span class="redColor">*</span>
                    </div>
                    <div class="controls col-sm-8">
                        <input type="text" autocomplete="off" class="inputElement select2" style="width: 100%"
                            data-rule-required="true" data-rule-main-owner="true" data-fieldtype="owner"
                            data-fieldname="assigned_user_id" data-name="assigned_user_id" name="assigned_user_id"
                            {if $FOR_EVENT} data-assignable-users-only="true" data-user-only="true" data-single-selection="true" {/if}
                            {if isset($ACTION_DATA['callInfo']['assigned_user_id'])}data-selected-tags='{ZEND_JSON::encode(Vtiger_Owner_UIType::getCurrentOwners($ACTION_DATA['callInfo']['assigned_user_id']))}'{/if} />
                        <div class="checkbox-label mt-2">
                            <input name="assign_parent_record_owners" type="checkbox" {if isset($ACTION_DATA['callInfo']['assign_parent_record_owners']) && $ACTION_DATA['callInfo']['assign_parent_record_owners'] eq 1}checked{/if}>
                            {vtranslate('LBL_THE_PERSON_IN_CHARGE_OF_THE_FATHER_RECORD', $MODULE_NAME)}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_DESCRIBE', $MODULE_NAME)}
                    </div>
                    <div class="controls col-sm-8">
                        <textarea rows="3" class="w40 resize-vertical" name="description">{if isset($ACTION_DATA['callInfo']['description'])}{$ACTION_DATA['callInfo']['description']}{/if}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_ACTIVATE_REPEAT_MODE', $MODULE_NAME)}
                    </div>
                    <div class="controls col-sm-8">
                        <input id="toggleCheckbox" name="recurringcheck" class="inputElement mt-2" type="checkbox" {if isset($ACTION_DATA['callInfo']['recurringcheck']) && $ACTION_DATA['callInfo']['recurringcheck'] eq 'on'}checked{/if}>
                        <div id="toggleContent" class="mt-2 {if !isset($ACTION_DATA['callInfo']['recurringcheck']) || $ACTION_DATA['callInfo']['recurringcheck'] neq 'on'}hide{/if}">
                            <div class="d-flex align-item-center justify-content-center">
                                <span class="mr-3">{vtranslate('LBL_WHENEVER', $MODULE_NAME)}</span>
                                <select name="repeat_frequency" class="inputElement select2 mr-3 w10" tabindex="-1">
                                    {for $i=1 to 14}
                                    <option value="{$i}" {if isset($ACTION_DATA['callInfo']['repeat_frequency']) && $ACTION_DATA['callInfo']['repeat_frequency'] eq $i}selected{/if}>{$i}</option>
                                    {/for}
                                </select>
                                <select name="recurringtype" class="inputElement select2 w20" tabindex="-1">
                                    <option value="Daily" {if isset($ACTION_DATA['callInfo']['recurringtype']) && $ACTION_DATA['callInfo']['recurringtype'] eq 'Daily'}selected{/if}>{vtranslate('LBL_DAY', $MODULE_NAME)}</option>
                                    <option value="Weekly" {if isset($ACTION_DATA['callInfo']['recurringtype']) && $ACTION_DATA['callInfo']['recurringtype'] eq 'Weekly'}selected{/if}>{vtranslate('LBL_WEEK', $MODULE_NAME)}</option>
                                    <option value="Monthly" {if isset($ACTION_DATA['callInfo']['recurringtype']) && $ACTION_DATA['callInfo']['recurringtype'] eq 'Monthly'}selected{/if}>{vtranslate('LBL_MONTH', $MODULE_NAME)}</option>
                                    <option value="Yearly" {if isset($ACTION_DATA['callInfo']['recurringtype']) && $ACTION_DATA['callInfo']['recurringtype'] eq 'Yearly'}selected{/if}>{vtranslate('LBL_YEAR', $MODULE_NAME)}</option>
                                </select>
                                <span class="ml-3 mr-3">{vtranslate('LBL_UNTIL', $MODULE_NAME)}</span>
                                <div class="input-group inputElement w30" style="margin-bottom: 3px">
                                    <input id="calendar_repeat_limit_date" type="text" name="calendar_repeat_limit_date" class="form-control datePicker" 
                                        data-fieldtype="date" data-rule-required="true" value="{if isset($ACTION_DATA['callInfo']['calendar_repeat_limit_date'])}{$ACTION_DATA['callInfo']['calendar_repeat_limit_date']}{/if}" />
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal-footer ">
            <center>
                {if $BUTTON_NAME neq null}
                {assign var=BUTTON_LABEL value=$BUTTON_NAME}
                {else}
                {assign var=BUTTON_LABEL value={vtranslate('LBL_SAVE', $MODULE)}}
                {/if}
                <button href="#" class="btn cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE_NAME)}</button>
                <button class="btn btn-default" id="fullInfo" type="button"><strong>{vtranslate('LBL_FULL_INFO', $MODULE_NAME)}</strong></button>
                <button {if $BUTTON_ID neq null} id="{$BUTTON_ID}" {/if} class="btn btn-primary" type="submit"
                    name="saveButton"><strong>{$BUTTON_LABEL}</strong></button>
            </center>
        </div>
    </form>
</div>
{/strip}