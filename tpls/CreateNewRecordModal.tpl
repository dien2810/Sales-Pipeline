{* Added by Minh Hoang on 2021-02-10 *}

{strip}
<div class="modal-dialog modal-content modal-width-700">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_CREATE_NEW_RECORD', $MODULE_NAME)}" }
    <form id="form-create-new-record" class="form-horizontal createNewRecordModal form-modal" method="POST">
        <div class="form-content">
            <input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />
            <div class="form-group">
                <div class="controls col-sm-3 text-left ml-3">
                    {vtranslate('LBL_TASK_NAME', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <input type="text" class="inputElement">
                </div>
            </div>

            <div class="form-group">
                <div class="controls col-sm-3 text-left ml-3">
                    {vtranslate('LBL_CREATE_A_RECORD_IN', $MODULE_NAME)}
                    <span class="redColor">*</span>
                </div>
                <div class="controls col-sm-8">
                    <select id="recordModule" class="inputElement text-left select2" data-rule-required="true">
                        <option value="">{vtranslate('LBL_CHOOSE_FIELD', $MODULE_NAME)}</option>
                        {foreach from=$ALL_MODULES key=NAME item=LABEL}
                            <option value="{$NAME}" {if $NAME == $TARGET_MODULE}selected{/if}>{$LABEL}</option>
                            <!-- <option value="{$NAME}" {if $NAME == Leads}selected{/if}>{$LABEL}</option> -->
                        {/foreach}
                    </select>
                </div>
            </div>

            <div>
                <button type="button" class="btn btn-default ml-3 mb-3 mt-2" id="addNewDataField">
                    {vtranslate('LBL_ADD_DATA_FIELD', $MODULE_NAME)}
                </button>   
            </div>

            <div class="initialDataField hide">
                <div class="form-group">
                    <div class="d-flex align-item-center">
                        <div class="controls col-sm-4 text-left">
                            <select class="inputElement select2 ml-3" tabindex="-1" disabled>
                                <option value="">{vtranslate('LBL_CHOOSE_FIELD', $MODULE_NAME)}</option>
                                <option value="value1">Option 1</option>
                            </select>
                        </div>
                        <div class="controls col-sm-4">
                            <input type="text" class="inputElement ml-3 inputPopup">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="d-flex align-item-center">
                        <div class="controls col-sm-4 text-left">
                            <select class="inputElement select2 ml-3" tabindex="-1" disabled>
                                <option value="">{vtranslate('LBL_CHOOSE_FIELD', $MODULE_NAME)}</option>
                                <option value="value1">Option 1</option>
                            </select>
                        </div>
                        <div class="controls col-sm-4">
                            <select class="inputElement select2 ml-3">
                                <option value="">{vtranslate('LBL_CHOOSE_A_VALUE', $MODULE_NAME)}</option>
                                <option value="value1">Option 1</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="d-flex align-item-center">
                        <div class="controls col-sm-4 text-left">
                            <select class="inputElement select2 ml-3" tabindex="-1" disabled>
                                <option value="">{vtranslate('LBL_CHOOSE_FIELD', $MODULE_NAME)}</option>
                                <option value="value1">Option 1</option>
                            </select>
                        </div>
                        <div class="controls col-sm-4">
                            <div class="input-group inputElement ml-3">
                                <input type="text" name="inputDate" class="form-control datePicker" 
                                    data-fieldtype="date" data-rule-required="true" />
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="d-flex align-item">
                        <div class="controls col-sm-4 text-left">
                            <select class="inputElement select2 ml-3" tabindex="-1" disabled>
                                <option value="">{vtranslate('LBL_CHOOSE_FIELD', $MODULE_NAME)}</option>
                                <option value="value1">Option 1</option>
                            </select>
                        </div>
                        <div class="controls col-sm-4 ml-3">
                            <select multiple class="inputElement select2" placeholder="{vtranslate('LBL_ENTER_THE_USERNAME_OR_GROUP_NAME', $MODULE_NAME)}">
                                <option value="value1">Di động (0123456789)</option>
                                <option value="value2">Di động (1212312312)</option>
                                <option value="value3">Di động (9686895949)</option>
                            </select>
                            <div class="checkbox-label mt-2">
                                <input type="checkbox">
                                <span class="text90">{vtranslate('LBL_THE_PERSON_IN_CHARGE_OF_THE_FATHER_RECORD', $MODULE_NAME)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="newDataField">
                
            </div>
        </div>
        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}