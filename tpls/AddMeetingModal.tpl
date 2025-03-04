{* Added by Minh Hoang on 2025-02-03 *}

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
                    <input name="action_name" type="text" class="inputElement w40" data-rule-required="true">
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_ACTIVITY_NAME', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <input name="eventName" type="text" class="inputElement w40" data-rule-required="true">
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    {vtranslate('LBL_START_TIME', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-6">
                    <div class="input-group inputElement time w60">
                        <input type="text" name="startTime" class="timepicker-default form-control" data-format="24" data-rule-required="true"/>
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
                        <input name="startDays" type="text" class="inputElement w10" data-rule-required="true">
                        <span class="ml-3 mr-3">{vtranslate('LBL_DATE', $MODULE_NAME)}</span>
                        <select name="startDirection" class="inputElement select2 mr-3 w20" tabindex="-1" data-rule-required="true">
                            <option value="after">{vtranslate('LBL_AFTER', $MODULE_NAME)}</option>
                            <option value="before">{vtranslate('LBL_BEFORE', $MODULE_NAME)}</option>
                        </select>
                        <select name="startDatefield" class="inputElement select2 w40" tabindex="-1" data-rule-required="true">
                            {foreach from=$DATETIME_FIELDS item=DATETIME_FIELD}
                                <option {if $TASK_OBJECT->startDatefield eq $DATETIME_FIELD->get('name')}selected{/if}  value="{$DATETIME_FIELD->get('name')}">{vtranslate($DATETIME_FIELD->get('label'), $DATETIME_FIELD->getModuleName())}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    Thời lượng cuộc họp
                </div>
                <div class="controls col-sm-8">
                    <div class="d-flex align-item-center justify-content-center">
                        <input name="duration" type="number" min="0" class="inputElement w10">
                        <select name="durationUnit" class="inputElement select2 ml-3 w30" tabindex="-1" value="minutes">
                            <option value="minutes">{vtranslate('LBL_MINUTES', $MODULE_NAME)}</option>
                            <option value="hours">{vtranslate('LBL_HOURS', $MODULE_NAME)}</option>
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
                        <input type="text" name="endTime" readonly class="timepicker-default form-control" data-format="24" data-rule-required="true"/>
                        <span class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="fieldLabel col-sm-3 text-left ml-3">
                    Địa điểm
                </div>
                <div class="controls col-sm-6">
                    <div class="input-group inputElement w60">
                        <input type="text" name="location" class="form-control" data-rule-required="true"/>
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
                            <option value="Planned">{vtranslate('LBL_PLAN', $MODULE_NAME)}</option>
                            <option value="Held">{vtranslate('LBL_ENDED', $MODULE_NAME)}</option>
                            <option value="Not Held">{vtranslate('LBL_CANCEL', $MODULE_NAME)}</option>
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
                            <option value="Private">{vtranslate('LBL_PRIVATE', $MODULE_NAME)}</option>
                            <option value="Public">{vtranslate('LBL_PUBLIC', $MODULE_NAME)}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_DELIVER', $MODULE_NAME)}
                        <span class="redColor">*</span>
                    </div>
                    <div class="controls col-sm-8">
                        {* <input name="assigned_user_id" type="text" class="inputElement w40" data-rule-required="true"> *}
                        <input type="text" autocomplete="off" class="inputElement select2" style="width: 100%" data-rule-required="true" data-rule-main-owner="true"
                            data-fieldtype="owner" data-fieldname="assigned_user_id" data-name="assigned_user_id" name="assigned_user_id"
                            {if $FOR_EVENT}
                                data-assignable-users-only="true" data-user-only="true" data-single-selection="true"
                            {/if}
                            {if $FIELD_VALUE}
                                data-selected-tags='{ZEND_JSON::encode(Vtiger_Owner_UIType::getCurrentOwners($FIELD_VALUE))}'
                            {/if}
                        />
                        <div class="checkbox-label mt-2">
                            <input name="assign_parent_record_owners" type="checkbox"> 
                            {vtranslate('LBL_THE_PERSON_IN_CHARGE_OF_THE_FATHER_RECORD', $MODULE_NAME)}
                        </div>
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
                        {vtranslate('LBL_ACTIVATE_REPEAT_MODE', $MODULE_NAME)}
                    </div>
                    <div class="controls col-sm-8">
                        <input id="toggleCheckbox" name="recurringcheck" class="inputElement mt-2" type="checkbox">
                        <div id="toggleContent" class="mt-2 hide">
                            <div class="d-flex align-item-center justify-content-center">
                                <span class="mr-3">{vtranslate('LBL_WHENEVER', $MODULE_NAME)}</span>
                                <select name="repeat_frequency" class="inputElement select2 mr-3 w10" tabindex="-1">
                                    {for $i=1 to 14}
                                        <option value="{$i}">{$i}</option>
                                    {/for}
                                </select>                        
                                <select name="recurringtype" class="inputElement select2 w20" tabindex="-1">
                                    <option value="Daily">{vtranslate('LBL_DAY', $MODULE_NAME)}</option>
                                    <option value="Weekly">{vtranslate('LBL_WEEK', $MODULE_NAME)}</option>
                                    <option value="Monthly">{vtranslate('LBL_MONTH', $MODULE_NAME)}</option>
                                    <option value="Yearly">{vtranslate('LBL_YEAR', $MODULE_NAME)}</option>
                                </select>
                                <span class="ml-3 mr-3">{vtranslate('LBL_UNTIL', $MODULE_NAME)}</span>
                                <div class="input-group inputElement w30" style="margin-bottom: 3px">
                                    <input id="calendar_repeat_limit_date" type="text" name="calendar_repeat_limit_date" class="form-control datePicker" 
                                        data-fieldtype="date" data-rule-required="true" />
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {* {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'} *}
        <div class="modal-footer ">
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