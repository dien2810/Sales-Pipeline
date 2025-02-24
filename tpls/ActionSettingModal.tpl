{strip}
	{* Modal Action Setting *}
	<div id="actionSettingModal" class="modal-dialog modal-content hide"> 
		{assign var=HEADER_TITLE value={vtranslate('LBL_ACTION_SETTING_MODAL_TITLE', 'Settings:Vtiger')}} 
		{include file='ModalHeader.tpl'|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
		{* <h1>{$ACTION.actionType}</h1>
		<h1>{$ACTION.notification_type}</h1>
		<h1>{$ACTION.stageId}</h1>
		<h1>{$ACTION.time_unit}</h1>
		<h1>{$ACTION.time_value}</h1> *}
		<div class="actionTypeContainer">
			<div class="actionTypeRow mt-3">
				<div id="addCall" class="actionTypeItem">
					<i class="fal fa-phone ml-2 text-primary"></i>
					&nbsp;
					&nbsp;
					<p class="text-primary pt-3">{vtranslate('LBL_ADD_CALL', 'Settings:Vtiger')}</p>								
				</div>
				<div id="addMeeting" class="actionTypeItem">
					<i class="fal fa-users ml-2 text-primary"></i>
					&nbsp;
					&nbsp;
					<p class="text-primary pt-3">{vtranslate('LBL_ADD_MEETING', 'Settings:Vtiger')}</p>
				</div>
			</div>
			<div class="actionTypeRow">
				<div id="createNewTask" class="actionTypeItem">
					<i class="fal fa-list ml-2 text-primary text-primary"></i>
					&nbsp;
					&nbsp;
					<p class="text-primary pt-3">{vtranslate('LBL_CREATE_NEW_TASK', 'Settings:Vtiger')}</p>
				</div>
				<div id="createNewProjectTask" class="actionTypeItem">
					<i class="fal fa-clipboard-list ml-2 text-primary"></i>
					&nbsp;
					&nbsp;
					<p class="text-primary pt-3">{vtranslate('LBL_CREATE_NEW_PROJECT_TASK', 'Settings:Vtiger')}</p>
				</div>
			</div>
			<div class="actionTypeRow">
				<div id="createNewRecord" class="actionTypeItem">
					<i class="fal fa-plus-circle ml-2 text-primary"></i>
						&nbsp;
						&nbsp;
					<p class="text-primary pt-3">{vtranslate('LBL_CREATE_NEW_RECORD', 'Settings:Vtiger')}</p>
				</div>
				<div id="updateDataField" class="actionTypeItem">
					<i class="fal fa-pencil-alt ml-2 text-primary"></i>
					&nbsp;
					&nbsp;
					<p class="text-primary pt-3">{vtranslate('LBL_UPDATE_DATA_FIELD', 'Settings:Vtiger')}</p>
				</div>
			</div>
			<div class="actionTypeRow">
				<div id="sendZNSMessage" class="actionTypeItem">
					<i class="fal fa-comments-alt ml-2 text-primary"></i>
					&nbsp;
					&nbsp;
					<p class="text-primary pt-3">{vtranslate('LBL_SEND_ZNS_MESSAGE', 'Settings:Vtiger')}</p>
				</div>
				<div id="sendSMSMessage" class="actionTypeItem">
					<i class="fal fa-sms ml-2 text-primary"></i>
					&nbsp;
					&nbsp;
					<p class="text-primary pt-3">{vtranslate('LBL_SEND_SMS_MESSAGE', 'Settings:Vtiger')}</p>
				</div>
			</div>
			<div class="actionTypeRow">
				<div id="sendEmail" class="actionTypeItem">
					<i class="fal fa-envelope ml-2 text-primary"></i>
					&nbsp;
					&nbsp;
					<p class="text-primary pt-3">Gửi Email</p>
				</div>
				<div id="addNotification" class="actionTypeItem">
					<i class="fal fa-bell ml-2 text-primary"></i>
					&nbsp;
					&nbsp;
					<p class="text-primary pt-3">{vtranslate('LBL_NOTIFICATION', 'Settings:Vtiger')}</p>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<center>
				{assign var=BUTTON_LABEL value={vtranslate('LBL_BACK', $MODULE)}}
				<button id="back" class="btn text-primary" type="button" name="back"><strong>{$BUTTON_LABEL}</strong></button>
				<a href="#" class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</center>
		</div>
		{* {include file='ModalFooter.tpl'|@vtemplate_path:'Vtiger'}   *}
	</div>
	{/strip}