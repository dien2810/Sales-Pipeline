{*
Name: AddActionSettingModal.tpl
Author: Dien Nguyen
Date: 2025-01-13
Purpose: Modal add Action
*}
{strip}
<div id="addActionSettingModal" class="modal-dialog modal-content hide">
	{assign var=HEADER_TITLE value={vtranslate('LBL_ADD_ACTION_SETTING_MODAL_TITLE', 'Settings:Vtiger')}}
	{include file='ModalHeader.tpl'|@vtemplate_path:'Vtiger' TITLE=$HEADER_TITLE}
	{assign var="isActionWithCondition" value=($ACTION.frequency == "actionWithCondition")}
	{assign var="isScheduled" value=($ACTION.action_time_type == "scheduled")}
	<form class="form-horizontal addActionSettingForm" method="POST">
		<div class="form-group mt-3">
			<label class="control-label fieldLabel col-sm-5" for="frequency">
				<span>{vtranslate('LBL_CHOOSE_ACTION_TYPE', 'Settings:Vtiger')}</span>
			</label>
			<div class="col-sm-6 padding0">
				<div class="form-check my-3">
					<label>
						<input class="form-check-input" type="radio" name="frequency" id="actionType1"
							value="onceAction" {if !$isActionWithCondition}checked{/if} />
						&nbsp;
						<span>{vtranslate('LBL_ONCE_ACTION', 'Settings:Vtiger')}</span>
					</label>
				</div>
				<div class="form-check my-3">
					<label>
						<input class="form-check-input" type="radio" name="frequency" id="actionType2"
							value="actionWithCondition" {if $isActionWithCondition}checked{/if} />
						&nbsp;
						<span>{vtranslate('LBL_ACTION_WITH_CONDITION', 'Settings:Vtiger')}</span>
					</label>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label fieldLabel col-sm-5">
				<span>{vtranslate('LBL_TIME_OF_EXECUTION', 'Settings:Vtiger')}</span>
				&nbsp;
				<span class="redColor">*</span>
			</label>
			<div class="col-sm-6 padding0">
				<select name="action_time_type" id="action_time_type" class="inputElement select2"
					data-fieldtype="picklist" style="display: none" data-rule-required="true">
					<option value="immediate" {if !$isScheduled}selected{/if}>{vtranslate('LBL_IMMEDIATE',
						'Settings:Vtiger')}</option>
					<option value="scheduled" {if $isScheduled}selected{/if}>{vtranslate('LBL_SCHEDULED',
						'Settings:Vtiger')}</option>
				</select>
			</div>
			<div class="col-sm-5"></div>
			<div id="scheduled_fields" class="col-sm-6 {if !$isScheduled}hide{/if}" style="margin-top: 10px;">
				<div class="row">
					<div class="lblAfterMoving col-sm-5 mr-1">
						<p>{vtranslate('LBL_AFTER_MOVING', 'Settings:Vtiger')}</p>
					</div>
					<div class="col-sm-2 padding0 mr-1">
						<input id="time" type="number" name="time" value="{$ACTION.time}" class="form-control"
							style="margin-bottom: 10px;" />
					</div>
					<div class="form-select form-select-lg col-sm-4 padding0 mr-1">
						<select name="time_unit" class="inputElement select2" data-fieldtype="picklist"
							style="display: none" data-rule-required="true">
							<option value="minutes">{vtranslate('LBL_MINUTE', 'Settings:Vtiger')}</option>
							<option value="hours" {if $ACTION.time_unit eq "hours" }selected{/if}>
								{vtranslate('LBL_HOUR', 'Settings:Vtiger')}</option>
							<option value="days" {if $ACTION.time_unit eq "days" }selected{/if}>{vtranslate('LBL_DAY',
								'Settings:Vtiger')}</option>
						</select>
					</div>
				</div>
			</div>
		</div>
		{include file='ModalFooter.tpl'|@vtemplate_path:'Vtiger'}
	</form>
</div>
{/strip}