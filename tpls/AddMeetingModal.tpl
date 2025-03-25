{* Added by Minh Hoang on 2025-02-03 *}
{strip}
{assign var=ACTION_INFO value=$ACTION_DATA['meetingInfo']}
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
                    <input name="action_name" type="text" class="inputElement w40" data-rule-required="true" value="{if isset($ACTION_DATA['action_name'])}{$ACTION_DATA['action_name']}{/if}">
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_ACTIVITY_NAME', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <input name="eventName" type="text" class="inputElement w40" data-rule-required="true" value="{if isset($ACTION_INFO['eventName'])}{$ACTION_INFO['eventName']}{/if}">
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_START_TIME', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-6">
                    <div class="input-group inputElement time w60">
                        <input type="text" name="startTime" class="timepicker-default form-control" data-format="24" data-rule-required="true" value="{if isset($ACTION_INFO['startTime'])}{$ACTION_INFO['startTime']}{else}00:00{/if}"/>
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
                        <input name="startDays" type="text" class="inputElement w10" data-rule-required="true" value="{if isset($ACTION_INFO['startDays'])}{$ACTION_INFO['startDays']}{/if}">
                        <span class="ml-3 mr-3">{vtranslate('LBL_DATE', $MODULE_NAME)}</span>
                        <select name="startDirection" class="inputElement select2 mr-3 w20" tabindex="-1" data-rule-required="true">
                            <option value="after" {if isset($ACTION_INFO['startDirection']) && $ACTION_INFO['startDirection'] eq 'after'}selected{/if}>{vtranslate('LBL_AFTER', $MODULE_NAME)}</option>
                            <option value="before" {if isset($ACTION_INFO['startDirection']) && $ACTION_INFO['startDirection'] eq 'before'}selected{/if}>{vtranslate('LBL_BEFORE', $MODULE_NAME)}</option>
                        </select>
                        <select name="startDatefield" class="inputElement select2 w40" tabindex="-1" data-rule-required="true">
                            {foreach from=$DATETIME_FIELDS item=DATETIME_FIELD}
                                <option {if isset($ACTION_INFO['startDatefield']) && $ACTION_INFO['startDatefield'] eq $DATETIME_FIELD->get('name')}selected{/if} value="{$DATETIME_FIELD->get('name')}">{vtranslate($DATETIME_FIELD->get('label'), $DATETIME_FIELD->getModuleName())}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_MEETING_DURATION', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-8">
                    <div class="d-flex align-item-center justify-content-center">
                        <input name="duration" type="number" min="0" class="inputElement w10" value="{if isset($ACTION_INFO['duration'])}{$ACTION_INFO['duration']}{/if}">
                        <select name="durationUnit" class="inputElement select2 ml-3 w30" tabindex="-1">
                            <option value="minutes" {if isset($ACTION_INFO['durationUnit']) && $ACTION_INFO['durationUnit'] eq 'minutes'}selected{/if}>{vtranslate('LBL_MINUTES', $MODULE_NAME)}</option>
                            <option value="hours" {if isset($ACTION_INFO['durationUnit']) && $ACTION_INFO['durationUnit'] eq 'hours'}selected{/if}>{vtranslate('LBL_HOURS', $MODULE_NAME)}</option>
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
                        <input type="text" name="endTime" readonly class="timepicker-default form-control" data-format="24" data-rule-required="true" value="{if isset($ACTION_INFO['endTime'])}{$ACTION_INFO['endTime']}{/if}"/>
                        <span class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_LOCATION', $MODULE_NAME)}
                </div>
                <div class="controls col-sm-6">
                    <div class="input-group inputElement w60">
                        <input type="text" name="location" class="form-control" data-rule-required="true" value="{if isset($ACTION_INFO['location'])}{$ACTION_INFO['location']}{/if}"/>
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
                            <option value="Planned" {if isset($ACTION_INFO['status']) && $ACTION_INFO['status'] eq 'Planned'}selected{/if}>{vtranslate('LBL_PLAN', $MODULE_NAME)}</option>
                            <option value="Held" {if isset($ACTION_INFO['status']) && $ACTION_INFO['status'] eq 'Held'}selected{/if}>{vtranslate('LBL_ENDED', $MODULE_NAME)}</option>
                            <option value="Not Held" {if isset($ACTION_INFO['status']) && $ACTION_INFO['status'] eq 'Not Held'}selected{/if}>{vtranslate('LBL_CANCEL', $MODULE_NAME)}</option>
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
                            <option value="Private" {if isset($ACTION_INFO['visibility']) && $ACTION_INFO['visibility'] eq 'Private'}selected{/if}>{vtranslate('LBL_PRIVATE', $MODULE_NAME)}</option>
                            <option value="Public" {if isset($ACTION_INFO['visibility']) && $ACTION_INFO['visibility'] eq 'Public'}selected{/if}>{vtranslate('LBL_PUBLIC', $MODULE_NAME)}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_DELIVER', $MODULE_NAME)}
                        <span class="redColor">*</span>
                    </div>
                    <div class="controls col-sm-8">
                        <input type="text" autocomplete="off" class="inputElement select2" style="width: 100%" data-rule-required="true" data-rule-main-owner="true"
                            data-fieldtype="owner" data-fieldname="assigned_user_id" data-name="assigned_user_id" name="assigned_user_id"
                            {if $FOR_EVENT}
                                data-assignable-users-only="true" data-user-only="true" data-single-selection="true"
                            {/if}
                            {if isset($ACTION_INFO['assigned_user_id'])}
                                data-selected-tags='{ZEND_JSON::encode(Vtiger_Owner_UIType::getCurrentOwners($ACTION_INFO['assigned_user_id']))}'
                            {/if}
                        />
                        <div class="checkbox-label mt-2">
                            <input name="assign_parent_record_owners" type="checkbox" {if isset($ACTION_INFO['assign_parent_record_owners']) && $ACTION_INFO['assign_parent_record_owners'] eq 1}checked{/if}>
                            {vtranslate('LBL_THE_PERSON_IN_CHARGE_OF_THE_FATHER_RECORD', $MODULE_NAME)}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_DESCRIBE', $MODULE_NAME)}
                    </div>
                    <div class="controls col-sm-8">
                        <textarea rows="3" class="w40 resize-vertical" name="description">{if isset($ACTION_INFO['description'])}{$ACTION_INFO['description']}{/if}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_ACTIVATE_REPEAT_MODE', $MODULE_NAME)}
                    </div>
                    <div class="controls col-sm-8">
                        <input id="toggleCheckbox" name="recurringcheck" class="inputElement mt-2" type="checkbox" {if isset($ACTION_INFO['recurringcheck']) && $ACTION_INFO['recurringcheck'] eq 'on'}checked{/if}>
                        <div id="toggleContent" class="mt-2 hide">
                            <div class="d-flex align-item-center justify-content-center">
                                <span class="mr-3">{vtranslate('LBL_WHENEVER', $MODULE_NAME)}</span>
                                <select name="repeat_frequency" class="inputElement select2 mr-3 w10" tabindex="-1">
                                    {for $i=1 to 14}
                                        <option value="{$i}" {if isset($ACTION_INFO['repeat_frequency']) && $ACTION_INFO['repeat_frequency'] eq $i}selected{/if}>{$i}</option>
                                    {/for}
                                </select>                      
                                <select name="recurringtype" class="inputElement select2 w20" tabindex="-1">
                                    <option value="Daily" {if isset($ACTION_INFO['recurringtype']) && $ACTION_INFO['recurringtype'] eq 'Daily'}selected{/if}>{vtranslate('LBL_DAY', $MODULE_NAME)}</option>
                                    <option value="Weekly" {if isset($ACTION_INFO['recurringtype']) && $ACTION_INFO['recurringtype'] eq 'Weekly'}selected{/if}>{vtranslate('LBL_WEEK', $MODULE_NAME)}</option>
                                    <option value="Monthly" {if isset($ACTION_INFO['recurringtype']) && $ACTION_INFO['recurringtype'] eq 'Monthly'}selected{/if}>{vtranslate('LBL_MONTH', $MODULE_NAME)}</option>
                                    <option value="Yearly" {if isset($ACTION_INFO['recurringtype']) && $ACTION_INFO['recurringtype'] eq 'Yearly'}selected{/if}>{vtranslate('LBL_YEAR', $MODULE_NAME)}</option>
                                </select>
                                <span class="ml-3 mr-3">{vtranslate('LBL_UNTIL', $MODULE_NAME)}</span>
                                <div class="input-group inputElement w30" style="margin-bottom: 3px">
                                    <input id="calendar_repeat_limit_date" type="text" name="calendar_repeat_limit_date" class="form-control datePicker" 
                                        data-fieldtype="date" data-rule-required="true" value="{if isset($ACTION_INFO['calendar_repeat_limit_date'])}{$ACTION_INFO['calendar_repeat_limit_date']}{/if}"/>
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
                    {assign var=BUTTON_LABEL value={vtranslate('LBL_SAVE', $MODULE_NAME)}}
                {/if}
                <button href="#" class="btn cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE_NAME)}</button>
                <button class="btn btn-default" id="fullInfo" type="button"><strong>{vtranslate('LBL_FULL_INFO', $MODULE_NAME)}</strong></button>
                <button {if $BUTTON_ID neq null} id="{$BUTTON_ID}" {/if} class="btn btn-primary" type="submit" name="saveButton"><strong>{$BUTTON_LABEL}</strong></button>
            </center>
        </div>
    </form>
</div>
{/strip}