{*
	Name: AddConditionModal.tpl
	Author: Dien Nguyen
	Date: 2025-01-13
	Purpose: Modal add Condition
*}
{strip}
	{assign var=ALL_CONDITION_CRITERIA value=$ADVANCE_CRITERIA[1] }
	{assign var=ANY_CONDITION_CRITERIA value=$ADVANCE_CRITERIA[2] }

	{if empty($ALL_CONDITION_CRITERIA) }
		{assign var=ALL_CONDITION_CRITERIA value=array()}
	{/if}

	{if empty($ANY_CONDITION_CRITERIA) }
		{assign var=ANY_CONDITION_CRITERIA value=array()}
	{/if}
	<div id="transitionConditionModal" class="modal-dialog modal-content"> 
		{assign var=HEADER_TITLE value={vtranslate('LBL_TRANSITION_CONDITION', 'Settings:Vtiger')}} 
		<div class="modal-header">
			<div class="clearfix">
				<div class="pull-right " >
					<button type="button" class="close" aria-label="Close" data-dismiss="modal">
						<span aria-hidden="true" class='far fa-close'></span>
					</button>
				</div>
				<h4 class="pull-left">
					{$HEADER_TITLE}
					<span cladata-toggle="tooltip" style="margin-top:30px; margin-left: 10px;"
					data-tippy-content="Điều kiện chuyển bước giúp ràng buộc tiêu chuẩn chuyển bước, 
						đưa ra tiêu chuẩn chung cho mỗi bản ghi bên trong module">
					<i class="far fa-info-circle"></i>
				</span>
				</h4>
			</div>
		</div>
		
		<form class="form-horizontal transitionConditionForm" method="POST">
			<div class="form-content">
				<input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />
				<input type="hidden" name="date_filters" data-value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATE_FILTERS))}' />
				<input type=hidden name="advanceFilterOpsByFieldType" data-value='{ZEND_JSON::encode($ADVANCED_FILTER_OPTIONS_BY_TYPE)}' />
				<input type="hidden" id="advfilterlist" name="advfilterlist" value=""/>
				{foreach key=ADVANCE_FILTER_OPTION_KEY item=ADVANCE_FILTER_OPTION from=$ADVANCED_FILTER_OPTIONS}
					{$ADVANCED_FILTER_OPTIONS[$ADVANCE_FILTER_OPTION_KEY] = vtranslate($ADVANCE_FILTER_OPTION, $MODULE)}
				{/foreach}
				<input type=hidden name="advanceFilterOptions" data-value='{ZEND_JSON::encode($ADVANCED_FILTER_OPTIONS)}' />
				<div id="andConditionContainer" class="form-group mt-3 conditionGroup">
					<label class="control-label fieldLabel" for="andCondition"> 
						<span>{vtranslate('LBL_AND_CONDITION', 'Settings:Vtiger')}</span>                   
					</label>
					{if count($ALL_CONDITION_CRITERIA['columns']) > 0}
						{foreach item=CONDITION_INFO from=$ALL_CONDITION_CRITERIA['columns']}
							<div class="padding0 mt-2 andConditionRow conditionRow">
								{assign var=source_module value=$SOURCE_MODULE}
								<select class="{if empty($NOCHOSEN)}select2{/if} col-lg-12 inputElement mr-2 columnname" name="columnname" data-fieldtype="picklist" style="display: none; width: 200px;" data-rule-required="true">
									<option value="none">{vtranslate('LBL_SELECT_FIELD',$MODULE)}</option>
									{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
										<optgroup label='{vtranslate($BLOCK_LABEL, $SOURCE_MODULE)}'>
											{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
												{assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
												{assign var=MODULE_MODEL value=$FIELD_MODEL->getModule()}
												{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
												{if !empty($COLUMNNAME_API)}
													{assign var=columnNameApi value=$COLUMNNAME_API}
												{else}
													{assign var=columnNameApi value=getCustomViewColumnName}
												{/if}
												<option value="{$FIELD_MODEL->$columnNameApi()}" data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_NAME}"
												{if decode_html($FIELD_MODEL->$columnNameApi()) eq decode_html($CONDITION_INFO['columnname'])}
													{assign var=FIELD_TYPE value=$FIELD_MODEL->getFieldType()}
													{assign var=SELECTED_FIELD_MODEL value=$FIELD_MODEL}
													{if $FIELD_MODEL->getFieldDataType() == 'reference'  ||  $FIELD_MODEL->getFieldDataType() == 'multireference'}
														{$FIELD_TYPE='V'}
													{/if}
													{$FIELD_INFO['value'] = decode_html($CONDITION_INFO['value'])}
							
													{* Added by Hieu Nguyen on 2019-06-18 to return selected tags for custom owner field *}
													{if $FIELD_MODEL->getFieldDataType() eq 'owner' && $FIELD_INFO['value']}
														{$FIELD_INFO['selected_tags'] = Vtiger_Owner_UIType::getSelectedOwnersFromOwnersString($FIELD_INFO['value'])}
													{/if}
													{* End Hieu Nguyen *}
							
													{* Added by Hieu Nguyen on 2019-10-22 to return selected tags for custom user reference field *}
													{if $FIELD_MODEL->getFieldDataType() eq 'reference' && in_array($FIELD_MODEL->get('uitype'), ['52', '77']) && $FIELD_INFO['value']}
														{$FIELD_INFO['selected_tags'] = Vtiger_Owner_UIType::getSelectedOwnersFromOwnersString($FIELD_INFO['value'])}
													{/if}
													{* End Hieu Nguyen *}
							
													selected="selected"
												{/if}
												{if ($MODULE_MODEL->get('name') eq 'Calendar' || $MODULE_MODEL->get('name') eq 'Events') && ($FIELD_NAME eq 'recurringtype')}
													{assign var=PICKLIST_VALUES value = Calendar_Field_Model::getReccurencePicklistValues()}
													{$FIELD_INFO['picklistvalues'] = $PICKLIST_VALUES}
												{/if}
												{if ($MODULE_MODEL->get('name') eq 'Calendar') && ($FIELD_NAME eq 'activitytype')}
													{$FIELD_INFO['picklistvalues']['Task'] = vtranslate('Task', 'Calendar')}
												{/if}
							
												{* Added by Hieu Nguyen on 2021-01-26 to support tags field in saved search (NOTE: Currently for tags field in MAIN MODULE ONLY!) *}
												{if $FIELD_NAME eq 'tags'}
													{$FIELD_INFO['tag_list'] = Vtiger_Tag_Model::getAllUserAccessibleTags()}
												{/if}
												{* End Hieu Nguyen *}
							
												{if $FIELD_MODEL->getFieldDataType() eq 'reference'}
													{assign var=referenceList value=$FIELD_MODEL->getWebserviceFieldObject()->getReferenceList()}
													{if is_array($referenceList) && in_array('Users', $referenceList)}
															{assign var=USERSLIST value=array()}
															{assign var=CURRENT_USER_MODEL value = Users_Record_Model::getCurrentUserModel()}
															
															{* Comment out by Hieu Nguyen on 2019-05-14 to boost performance *}
															{*{assign var=ACCESSIBLE_USERS value = $CURRENT_USER_MODEL->getAccessibleUsers()}
															{foreach item=USER_NAME from=$ACCESSIBLE_USERS}
																	{$USERSLIST[$USER_NAME] = $USER_NAME}
															{/foreach}*}
															{* End Hieu Nguyen *}
							
															{$FIELD_INFO['picklistvalues'] = $USERSLIST}
															{$FIELD_INFO['type'] = 'picklist'}
													{/if}
												{/if}
												data-fieldinfo='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($FIELD_INFO))}' 
												{if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}>
												({vtranslate($MODULE_MODEL->get('name'), $MODULE_MODEL->get('name'))}) {vtranslate($FIELD_MODEL->get('label'), $MODULE_MODEL->get('name'))}
											</option>
											{/foreach}
										</optgroup>
									{/foreach}
								</select>
								<div class="conditionComparator">
									<select class="{if empty($NOCHOSEN)}select2{/if} inputElement mr-2 compareType" name="compareType" data-fieldtype="picklist" style="display: none; width: 150px;" data-rule-required="true">
										<option value="none">{vtranslate('LBL_NONE',$MODULE)}</option>
										{assign var=ADVANCE_FILTER_OPTIONS value=$ADVANCED_FILTER_OPTIONS_BY_TYPE[$FIELD_TYPE]}
										{if $FIELD_TYPE eq 'D' || $FIELD_TYPE eq 'DT'}
											{assign var=DATE_FILTER_CONDITIONS value=array_keys($DATE_FILTERS)}
											{assign var=ADVANCE_FILTER_OPTIONS value=array_merge($ADVANCE_FILTER_OPTIONS,$DATE_FILTER_CONDITIONS)}
										{/if}
										{foreach item=ADVANCE_FILTER_OPTION from=$ADVANCE_FILTER_OPTIONS}
											<option value="{$ADVANCE_FILTER_OPTION}"
											{if $ADVANCE_FILTER_OPTION eq $CONDITION_INFO['compareType']}
													selected
											{/if}
											>{vtranslate($ADVANCED_FILTER_OPTIONS[$ADVANCE_FILTER_OPTION])}</option>
										{/foreach}
									</select>
								</div>
								<span class="col-lg-4 col-md-4 col-sm-4 fieldUiHolder">
									<input name="{if $SELECTED_FIELD_MODEL}{$SELECTED_FIELD_MODEL->get('name')}{/if}" data-value="value" class="col-lg-12 col-md-12 col-sm-12" type="text" value="{$CONDITION_INFO['value']|escape}" />
								</span>
								<span class="hide">
									<input type="hidden" name="column_condition" value="and" />
								</span>
								<div class="col-lg-1 col-md-1 col-sm-1">
									<i class="far fa-trash-alt"></i>
								</div>
							</div>
						{/foreach}
					{else}
						<div class="padding0 mt-2 andConditionRow conditionRow">
							{assign var=source_module value=$SOURCE_MODULE}
							<select class="{if empty($NOCHOSEN)}select2{/if} col-lg-12 inputElement mr-2 columnname" name="columnname" data-fieldtype="picklist" style="display: none; width: 200px;" data-rule-required="true">
								<option value="none">{vtranslate('LBL_SELECT_FIELD',$MODULE)}</option>
								{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
									<optgroup label='{vtranslate($BLOCK_LABEL, $SOURCE_MODULE)}'>
										{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
											{assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
											{assign var=MODULE_MODEL value=$FIELD_MODEL->getModule()}
											{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
											{if !empty($COLUMNNAME_API)}
												{assign var=columnNameApi value=$COLUMNNAME_API}
											{else}
												{assign var=columnNameApi value=getCustomViewColumnName}
											{/if}
											<option value="{$FIELD_MODEL->$columnNameApi()}" data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_NAME}"
											{if ($MODULE_MODEL->get('name') eq 'Calendar' || $MODULE_MODEL->get('name') eq 'Events') && ($FIELD_NAME eq 'recurringtype')}
												{assign var=PICKLIST_VALUES value = Calendar_Field_Model::getReccurencePicklistValues()}
												{$FIELD_INFO['picklistvalues'] = $PICKLIST_VALUES}
											{/if}
											{if ($MODULE_MODEL->get('name') eq 'Calendar') && ($FIELD_NAME eq 'activitytype')}
												{$FIELD_INFO['picklistvalues']['Task'] = vtranslate('Task', 'Calendar')}
											{/if}
						
											{* Added by Hieu Nguyen on 2021-01-26 to support tags field in saved search (NOTE: Currently for tags field in MAIN MODULE ONLY!) *}
											{if $FIELD_NAME eq 'tags'}
												{$FIELD_INFO['tag_list'] = Vtiger_Tag_Model::getAllUserAccessibleTags()}
											{/if}
											{* End Hieu Nguyen *}
						
											{if $FIELD_MODEL->getFieldDataType() eq 'reference'}
												{assign var=referenceList value=$FIELD_MODEL->getWebserviceFieldObject()->getReferenceList()}
												{if is_array($referenceList) && in_array('Users', $referenceList)}
														{assign var=USERSLIST value=array()}
														{assign var=CURRENT_USER_MODEL value = Users_Record_Model::getCurrentUserModel()}
														
														{* Comment out by Hieu Nguyen on 2019-05-14 to boost performance *}
														{*{assign var=ACCESSIBLE_USERS value = $CURRENT_USER_MODEL->getAccessibleUsers()}
														{foreach item=USER_NAME from=$ACCESSIBLE_USERS}
																{$USERSLIST[$USER_NAME] = $USER_NAME}
														{/foreach}*}
														{* End Hieu Nguyen *}
						
														{$FIELD_INFO['picklistvalues'] = $USERSLIST}
														{$FIELD_INFO['type'] = 'picklist'}
												{/if}
											{/if}
											data-fieldinfo='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($FIELD_INFO))}' 
											{if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}>
											({vtranslate($MODULE_MODEL->get('name'), $MODULE_MODEL->get('name'))}) {vtranslate($FIELD_MODEL->get('label'), $MODULE_MODEL->get('name'))}
										</option>
										{/foreach}
									</optgroup>
								{/foreach}
							</select>
							<div class="conditionComparator">
								<select class="{if empty($NOCHOSEN)}select2{/if} inputElement mr-2 compareType" name="compareType" data-fieldtype="picklist" style="display: none; width: 150px;" data-rule-required="true">
									<option value="none">{vtranslate('LBL_NONE',$MODULE)}</option>
									{assign var=ADVANCE_FILTER_OPTIONS value=$ADVANCED_FILTER_OPTIONS_BY_TYPE[$FIELD_TYPE]}
									{if $FIELD_TYPE eq 'D' || $FIELD_TYPE eq 'DT'}
										{assign var=DATE_FILTER_CONDITIONS value=array_keys($DATE_FILTERS)}
										{assign var=ADVANCE_FILTER_OPTIONS value=array_merge($ADVANCE_FILTER_OPTIONS,$DATE_FILTER_CONDITIONS)}
									{/if}
									{foreach item=ADVANCE_FILTER_OPTION from=$ADVANCE_FILTER_OPTIONS}
										<option value="{$ADVANCE_FILTER_OPTION}"
										>{vtranslate($ADVANCED_FILTER_OPTIONS[$ADVANCE_FILTER_OPTION])}</option>
									{/foreach}
								</select>
							</div>
							<span class="col-lg-4 col-md-4 col-sm-4 fieldUiHolder">
								<input name="{if $SELECTED_FIELD_MODEL}{$SELECTED_FIELD_MODEL->get('name')}{/if}" data-value="value" class="col-lg-12 col-md-12 col-sm-12" type="text" value="" />
							</span>
							<span class="hide">
								<input type="hidden" name="column_condition" value="and" />
							</span>
							<div class="col-lg-1 col-md-1 col-sm-1">
								<i class="far fa-trash-alt"></i>
							</div>
						</div>
					{/if}
					<div id="newAndRow" class="col-lg-12 col-md-12 col-sm-12 padding0"></div>
					<div class="mt-3 addAndConditionBtn">
						<button class="btn text-primary">+ {vtranslate('LBL_ADD_CONDITION', $MODULE_NAME)}</button>
					</div>
					<div class="groupCondition">
						{assign var=GROUP_CONDITION value=$ALL_CONDITION_CRITERIA['condition']}
						{if empty($GROUP_CONDITION)}
							{assign var=GROUP_CONDITION value="and"}
						{/if}
						<input type="hidden" name="condition" value="{$GROUP_CONDITION}" />
					</div>
				</div>
				<div id="orConditionContainer" class="form-group mt-3 conditionGroup">
					<label class="control-label fieldLabel" for="orCondition"> 
						<span>{vtranslate('LBL_OR_CONDITION', 'Settings:Vtiger')}</span>                   
					</label>
					{if count($ANY_CONDITION_CRITERIA['columns']) > 0}
						{foreach item=CONDITION_INFO from=$ANY_CONDITION_CRITERIA['columns']}
							<div class="padding0 mt-2 orConditionRow conditionRow">
								{assign var=source_module value=$SOURCE_MODULE}
								<select class="{if empty($NOCHOSEN)}select2{/if} col-lg-12 inputElement mr-2 columnname" name="columnname" data-fieldtype="picklist" style="display: none; width: 200px;" data-rule-required="true">
									<option value="none">{vtranslate('LBL_SELECT_FIELD',$MODULE)}</option>
									{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
										<optgroup label='{vtranslate($BLOCK_LABEL, $SOURCE_MODULE)}'>
											{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
												{assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
												{assign var=MODULE_MODEL value=$FIELD_MODEL->getModule()}
												{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
												{if !empty($COLUMNNAME_API)}
													{assign var=columnNameApi value=$COLUMNNAME_API}
												{else}
													{assign var=columnNameApi value=getCustomViewColumnName}
												{/if}
												<option value="{$FIELD_MODEL->$columnNameApi()}" data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_NAME}"
												{if decode_html($FIELD_MODEL->$columnNameApi()) eq decode_html($CONDITION_INFO['columnname'])}
													{assign var=FIELD_TYPE value=$FIELD_MODEL->getFieldType()}
													{assign var=SELECTED_FIELD_MODEL value=$FIELD_MODEL}
													{if $FIELD_MODEL->getFieldDataType() == 'reference'  ||  $FIELD_MODEL->getFieldDataType() == 'multireference'}
														{$FIELD_TYPE='V'}
													{/if}
													{$FIELD_INFO['value'] = decode_html($CONDITION_INFO['value'])}
							
													{* Added by Hieu Nguyen on 2019-06-18 to return selected tags for custom owner field *}
													{if $FIELD_MODEL->getFieldDataType() eq 'owner' && $FIELD_INFO['value']}
														{$FIELD_INFO['selected_tags'] = Vtiger_Owner_UIType::getSelectedOwnersFromOwnersString($FIELD_INFO['value'])}
													{/if}
													{* End Hieu Nguyen *}
							
													{* Added by Hieu Nguyen on 2019-10-22 to return selected tags for custom user reference field *}
													{if $FIELD_MODEL->getFieldDataType() eq 'reference' && in_array($FIELD_MODEL->get('uitype'), ['52', '77']) && $FIELD_INFO['value']}
														{$FIELD_INFO['selected_tags'] = Vtiger_Owner_UIType::getSelectedOwnersFromOwnersString($FIELD_INFO['value'])}
													{/if}
													{* End Hieu Nguyen *}
							
													selected="selected"
												{/if}
												{if ($MODULE_MODEL->get('name') eq 'Calendar' || $MODULE_MODEL->get('name') eq 'Events') && ($FIELD_NAME eq 'recurringtype')}
													{assign var=PICKLIST_VALUES value = Calendar_Field_Model::getReccurencePicklistValues()}
													{$FIELD_INFO['picklistvalues'] = $PICKLIST_VALUES}
												{/if}
												{if ($MODULE_MODEL->get('name') eq 'Calendar') && ($FIELD_NAME eq 'activitytype')}
													{$FIELD_INFO['picklistvalues']['Task'] = vtranslate('Task', 'Calendar')}
												{/if}
							
												{* Added by Hieu Nguyen on 2021-01-26 to support tags field in saved search (NOTE: Currently for tags field in MAIN MODULE ONLY!) *}
												{if $FIELD_NAME eq 'tags'}
													{$FIELD_INFO['tag_list'] = Vtiger_Tag_Model::getAllUserAccessibleTags()}
												{/if}
												{* End Hieu Nguyen *}
							
												{if $FIELD_MODEL->getFieldDataType() eq 'reference'}
													{assign var=referenceList value=$FIELD_MODEL->getWebserviceFieldObject()->getReferenceList()}
													{if is_array($referenceList) && in_array('Users', $referenceList)}
															{assign var=USERSLIST value=array()}
															{assign var=CURRENT_USER_MODEL value = Users_Record_Model::getCurrentUserModel()}
															
															{* Comment out by Hieu Nguyen on 2019-05-14 to boost performance *}
															{*{assign var=ACCESSIBLE_USERS value = $CURRENT_USER_MODEL->getAccessibleUsers()}
															{foreach item=USER_NAME from=$ACCESSIBLE_USERS}
																	{$USERSLIST[$USER_NAME] = $USER_NAME}
															{/foreach}*}
															{* End Hieu Nguyen *}
							
															{$FIELD_INFO['picklistvalues'] = $USERSLIST}
															{$FIELD_INFO['type'] = 'picklist'}
													{/if}
												{/if}
												data-fieldinfo='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($FIELD_INFO))}' 
												{if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}>
												({vtranslate($MODULE_MODEL->get('name'), $MODULE_MODEL->get('name'))}) {vtranslate($FIELD_MODEL->get('label'), $MODULE_MODEL->get('name'))}
											</option>
											{/foreach}
										</optgroup>
									{/foreach}
								</select>
								<div class="conditionComparator">
									<select class="{if empty($NOCHOSEN)}select2{/if} inputElement mr-2 compareType" name="compareType" data-fieldtype="picklist" style="display: none; width: 150px;" data-rule-required="true">
										<option value="none">{vtranslate('LBL_NONE',$MODULE)}</option>
										{assign var=ADVANCE_FILTER_OPTIONS value=$ADVANCED_FILTER_OPTIONS_BY_TYPE[$FIELD_TYPE]}
										{if $FIELD_TYPE eq 'D' || $FIELD_TYPE eq 'DT'}
											{assign var=DATE_FILTER_CONDITIONS value=array_keys($DATE_FILTERS)}
											{assign var=ADVANCE_FILTER_OPTIONS value=array_merge($ADVANCE_FILTER_OPTIONS,$DATE_FILTER_CONDITIONS)}
										{/if}
										{foreach item=ADVANCE_FILTER_OPTION from=$ADVANCE_FILTER_OPTIONS}
											<option value="{$ADVANCE_FILTER_OPTION}"
											{if $ADVANCE_FILTER_OPTION eq $CONDITION_INFO['compareType']}
													selected
											{/if}
											>{vtranslate($ADVANCED_FILTER_OPTIONS[$ADVANCE_FILTER_OPTION])}</option>
										{/foreach}
									</select>
								</div>
								<span class="col-lg-4 col-md-4 col-sm-4 fieldUiHolder">
									<input name="{if $SELECTED_FIELD_MODEL}{$SELECTED_FIELD_MODEL->get('name')}{/if}" data-value="value" class="col-lg-12 col-md-12 col-sm-12" type="text" value="{$CONDITION_INFO['value']|escape}" />
								</span>
								<span class="hide">
									<input type="hidden" name="column_condition" value="or" />
								</span>
								<div class="col-lg-1 col-md-1 col-sm-1">
									<i class="far fa-trash-alt"></i>
								</div>
							</div>
						{/foreach}
					{else}
						<div class="padding0 mt-2 orConditionRow conditionRow">
							{assign var=source_module value=$SOURCE_MODULE}
							<select class="{if empty($NOCHOSEN)}select2{/if} col-lg-12 inputElement mr-2 columnname" name="columnname" data-fieldtype="picklist" style="display: none; width: 200px;" data-rule-required="true">
								<option value="none">{vtranslate('LBL_SELECT_FIELD',$MODULE)}</option>
								{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
									<optgroup label='{vtranslate($BLOCK_LABEL, $SOURCE_MODULE)}'>
										{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
											{assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
											{assign var=MODULE_MODEL value=$FIELD_MODEL->getModule()}
											{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
											{if !empty($COLUMNNAME_API)}
												{assign var=columnNameApi value=$COLUMNNAME_API}
											{else}
												{assign var=columnNameApi value=getCustomViewColumnName}
											{/if}
											<option value="{$FIELD_MODEL->$columnNameApi()}" data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_NAME}"											
											{if ($MODULE_MODEL->get('name') eq 'Calendar' || $MODULE_MODEL->get('name') eq 'Events') && ($FIELD_NAME eq 'recurringtype')}
												{assign var=PICKLIST_VALUES value = Calendar_Field_Model::getReccurencePicklistValues()}
												{$FIELD_INFO['picklistvalues'] = $PICKLIST_VALUES}
											{/if}
											{if ($MODULE_MODEL->get('name') eq 'Calendar') && ($FIELD_NAME eq 'activitytype')}
												{$FIELD_INFO['picklistvalues']['Task'] = vtranslate('Task', 'Calendar')}
											{/if}
						
											{* Added by Hieu Nguyen on 2021-01-26 to support tags field in saved search (NOTE: Currently for tags field in MAIN MODULE ONLY!) *}
											{if $FIELD_NAME eq 'tags'}
												{$FIELD_INFO['tag_list'] = Vtiger_Tag_Model::getAllUserAccessibleTags()}
											{/if}
											{* End Hieu Nguyen *}
						
											{if $FIELD_MODEL->getFieldDataType() eq 'reference'}
												{assign var=referenceList value=$FIELD_MODEL->getWebserviceFieldObject()->getReferenceList()}
												{if is_array($referenceList) && in_array('Users', $referenceList)}
														{assign var=USERSLIST value=array()}
														{assign var=CURRENT_USER_MODEL value = Users_Record_Model::getCurrentUserModel()}
														
														{* Comment out by Hieu Nguyen on 2019-05-14 to boost performance *}
														{*{assign var=ACCESSIBLE_USERS value = $CURRENT_USER_MODEL->getAccessibleUsers()}
														{foreach item=USER_NAME from=$ACCESSIBLE_USERS}
																{$USERSLIST[$USER_NAME] = $USER_NAME}
														{/foreach}*}
														{* End Hieu Nguyen *}
						
														{$FIELD_INFO['picklistvalues'] = $USERSLIST}
														{$FIELD_INFO['type'] = 'picklist'}
												{/if}
											{/if}
											data-fieldinfo='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($FIELD_INFO))}' 
											{if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}>
											({vtranslate($MODULE_MODEL->get('name'), $MODULE_MODEL->get('name'))}) {vtranslate($FIELD_MODEL->get('label'), $MODULE_MODEL->get('name'))}
										</option>
										{/foreach}
									</optgroup>
								{/foreach}
							</select>
							<div class="conditionComparator">
								<select class="{if empty($NOCHOSEN)}select2{/if} inputElement mr-2 compareType" name="compareType" data-fieldtype="picklist" style="display: none; width: 150px;" data-rule-required="true">
									<option value="none">{vtranslate('LBL_NONE',$MODULE)}</option>
									{assign var=ADVANCE_FILTER_OPTIONS value=$ADVANCED_FILTER_OPTIONS_BY_TYPE[$FIELD_TYPE]}
									{if $FIELD_TYPE eq 'D' || $FIELD_TYPE eq 'DT'}
										{assign var=DATE_FILTER_CONDITIONS value=array_keys($DATE_FILTERS)}
										{assign var=ADVANCE_FILTER_OPTIONS value=array_merge($ADVANCE_FILTER_OPTIONS,$DATE_FILTER_CONDITIONS)}
									{/if}
									{foreach item=ADVANCE_FILTER_OPTION from=$ADVANCE_FILTER_OPTIONS}
										<option value="{$ADVANCE_FILTER_OPTION}"
										>{vtranslate($ADVANCED_FILTER_OPTIONS[$ADVANCE_FILTER_OPTION])}</option>
									{/foreach}
								</select>
							</div>
							<span class="col-lg-4 col-md-4 col-sm-4 fieldUiHolder">
								<input name="{if $SELECTED_FIELD_MODEL}{$SELECTED_FIELD_MODEL->get('name')}{/if}" data-value="value" class="col-lg-12 col-md-12 col-sm-12" type="text" value="" />
							</span>
							<span class="hide">
								<input type="hidden" name="column_condition" value="or" />
							</span>
							<div class="col-lg-1 col-md-1 col-sm-1">
								<i class="far fa-trash-alt"></i>
							</div>
						</div>
					{/if}
					<div id="newOrRow" class="col-lg-12 col-md-12 col-sm-12 padding0"></div>
					<div class="mt-3 addOrConditionBtn">
						<button class="btn text-primary">+ {vtranslate('LBL_ADD_CONDITION', $MODULE_NAME)}</button>
					</div>
				</div>
			</div>
			{include file='ModalFooter.tpl'|@vtemplate_path:'Vtiger'} 
		</form> 
	</div>
	{/strip}