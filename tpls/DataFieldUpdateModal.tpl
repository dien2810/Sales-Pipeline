{strip}
    <div class="modal-dialog modal-content modal-width-900">
        {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_DATA_FIELD_UPDATE', $MODULE_NAME)}" }
        <form id="form-update-data-field" class="form-horizontal updateDataFieldModal form-modal" method="POST">
            <div class="form-content">
                <input type="hidden" name="date_filters" data-value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATE_FILTERS))}' />
                <div class="form-group">
                    <div class="fieldLabel col-sm-3 text-left ml-3">
                        {vtranslate('LBL_TASK_NAME', $MODULE_NAME)}
                        <span class="redColor">*</span>
                    </div>
                    <div class="controls col-sm-8">
                        <input type="text" name="action_name" class="inputElement">
                    </div>
                </div>
    
                <label class="text-left ml-3 def-variable-text">
                    {vtranslate('LBL_PUT_THE_VALUE_FOR_THE_FIELD', $MODULE_NAME)}
                </label>
    
                <div>
                    <button type="button" class="btn btn-default ml-3 mb-3 mt-2" id="addDataField">
                        {vtranslate('LBL_ADD_DATA_FIELD', $MODULE_NAME)}
                    </button>   
                </div>
    
                <div class="newDataField">
                </div>
                <div class="hide basic">
                    <div class="form-group d-flex align-item-center fieldRow">
                        <div class="controls col-sm-4" style="display: flex;align-items: center;justify-content: center;">
                            <select class="col-lg-12 inputElement mr-2 column_name" name="column_name" data-fieldtype="picklist" style="width: 200px;" data-rule-required="true">
                                <option value="none">{vtranslate('LBL_SELECT_FIELD', $MODULE)}</option>
                                {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
                                    {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                                        {assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
                                        {assign var=MODULE_MODEL value=$FIELD_MODEL->getModule()}
                                        {if !empty($COLUMNNAME_API)}
                                            {assign var=columnNameApi value=$COLUMNNAME_API}
                                        {else}
                                            {assign var=columnNameApi value=getCustomViewColumnName}
                                        {/if}
                                        <h1>{$COLUMNNAME_API}</h1>
                                        <option value="{$FIELD_MODEL->$columnNameApi()}" data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_NAME}"
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
                                            data-fieldinfo='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($FIELD_INFO))}'>
                                            {vtranslate($FIELD_MODEL->get('label'), $MODULE_MODEL->get('name'))}
                                        </option>
                                    {/foreach}
                                {/foreach}
                            </select>
                        </div>
                        <span class="col-lg-4 col-md-4 col-sm-4 fieldUiHolder">
                            <input name="value" data-value="value" class="col-lg-12 col-md-12 col-sm-12" type="text" value="" />
                        </span>
                        <div class="col-lg-1 col-md-1 col-sm-1">
                            <i class="far fa-trash-alt icon ml-3 removeField"></i>
                        </div>
                        
                    </div>
                </div>
            </div>
            {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
        </form>
    </div>
    {/strip}