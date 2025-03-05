{strip}
<input type="hidden" id="fieldValueMapping" name="field_value_mapping" value="{if $TASK_OBJECT->field_value_mapping}{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($TASK_OBJECT->field_value_mapping))}{/if}" />
    <input type="hidden" value="{if $TASK_ID}{$TASK_OBJECT->reference_field}{else}{$REFERENCE_FIELD_NAME}{/if}" name='reference_field' id='reference_field' />
    <div class="conditionsContainer" id="save_fieldvaluemapping">
        {if $RELATED_MODULE_MODEL}
            <div>
                <button type="button" class="btn btn-default" id="addFieldBtn">{vtranslate('LBL_ADD_DATA_FIELD',$QUALIFIED_MODULE)}</button>
            </div><br>
            {assign var=MANDATORY_FIELD_MODELS value=$RELATED_MODULE_MODEL->getMandatoryFieldModels()}
            {foreach from=$MANDATORY_FIELD_MODELS item=MANDATORY_FIELD_MODEL}
                {if in_array($SOURCE_MODULE, $MANDATORY_FIELD_MODEL->getReferenceList())}
                    {continue}
                {/if}
                <div class="row conditionRow form-group">
                    <span style="margin-right: 20px; margin-left: 15px;">
                        <select name="fieldname" class="select2" disabled="" style="min-width: 250px">
                            <option value="none"></option>
                            {foreach from=$RELATED_MODULE_MODEL->getFields() item=FIELD_MODEL}
                                {assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
                                <option value="{$FIELD_MODEL->get('name')}" data-fieldtype="{$FIELD_MODEL->getFieldType()}" {if $FIELD_MODEL->get('name') eq $MANDATORY_FIELD_MODEL->get('name')} 
                                    {assign var=FIELD_TYPE value=$FIELD_MODEL->getFieldDataType()} selected=""{/if} 
                                    data-field-name="{$FIELD_MODEL->get('name')}" data-fieldinfo='{ZEND_JSON::encode($FIELD_INFO)}' >
                                    {vtranslate($FIELD_MODEL->get('label'), $FIELD_MODEL->getModuleName())}<span class="redColor">*</span>
                                </option>	
                            {/foreach}
                        </select>
                    </span>
                    <span>
                        {if ($FIELD_TYPE eq 'picklist' || $FIELD_TYPE eq 'multipicklist')}
                            <input type="hidden" name="modulename" value="{$RELATED_MODULE_MODEL->get('name')}" />
                        {else}
                            <input type="hidden" name="modulename" value="{$SOURCE_MODULE}" />
                        {/if}
                    </span>

                    {* Modified by Hieu Nguyen on 2020-10-26 to support assign new record to parent record owners *}
                    <span class="fieldUiHolder"
                        {if $MANDATORY_FIELD_MODEL->get('name') == 'assigned_user_id'}
                            data-assign-parent-record-owners=""
                            data-assign-parent-record-owners-label="{vtranslate('LBL_ASSIGN_TO_PARENT_RECORED_OWNERS', $QUALIFIED_MODULE)}"
                        {/if}
                    >
                        <input type="text" class="getPopupUi inputElement" name="fieldValue" value="" />
                        <input type="hidden" name="valuetype" value="rawtext" />
                    </span>
                    {* End Hieu Nguyen *}
                </div>
            {/foreach}
            {include file="modules/Settings/PipelineConfig/tpls/FieldExpressions.tpl" RELATED_MODULE_MODEL=$RELATED_MODULE_MODEL MODULE_MODEL=$MODULE_MODEL FIELD_EXPRESSIONS=$FIELD_EXPRESSIONS}
        {/if}
    </div><br>
    {if $RELATED_MODULE_MODEL}
        <div class="row form-group basicAddFieldContainer hide">
            <span style="margin-right: 20px; margin-left: 15px;">
                <select name="fieldname" style="min-width: 250px">
                    <option value="none">{vtranslate('LBL_NONE',$QUALIFIED_MODULE)}</option>
                    {foreach from=$RELATED_MODULE_MODEL->getFields() item=FIELD_MODEL}
                        {assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
                        {if !$FIELD_MODEL->isMandatory() && $FIELD_MODEL->isEditable()} {* Modified by Hieu Nguyen on 2020-07-01 to hide readonly fields *}
                        <option value="{$FIELD_MODEL->get('name')}" data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_MODEL->get('name')}" data-fieldinfo='{ZEND_JSON::encode($FIELD_INFO)}' >
                            {vtranslate($FIELD_MODEL->get('label'), $FIELD_MODEL->getModuleName())}
                        </option>
                        {/if}
                    {/foreach}
                </select>
            </span>
            <span>
                <input type="hidden" name="modulename" value="{$SOURCE_MODULE}" />
            </span>
            <span class="fieldUiHolder">
                <input type="text" class="inputElement" readonly="" name="fieldValue" value="" />
                <input type="hidden" name="valuetype" value="rawtext" />
            </span>
            <span class="cursorPointer col-lg-1">
                <i class="alignMiddle deleteCondition far fa-trash-alt"></i>
            </span>
        </div>
    {/if}
{/strip}