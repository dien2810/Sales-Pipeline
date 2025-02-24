{strip}
<div class="modal-dialog modal-content">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_SET_VALUE', $MODULE_NAME)}" }
    <form id="form-set-value-popup" class="form-horizontal setValuePopupModal form-modal" method="POST">
        <div class="form-content">
            <input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />
            <div class="d-flex align-item-center mb-3 selectContent">
                <div class="controls col-sm-4">
                    <select id="selectSetValue" class="inputElement select2" tabindex="-1">
                        <option value="">{vtranslate('LBL_CHOOSE_FIELD', $MODULE_NAME)}</option>
                        <option value="text">{vtranslate('LBL_TEXT', $MODULE_NAME)}</option>
                        <option value="dataField">{vtranslate('LBL_DATA_FIELD', $MODULE_NAME)}</option>
                        <option value="expression">{vtranslate('LBL_EXPRESSION', $MODULE_NAME)}</option>
                    </select>
                </div>
                <div id="selectDataField" class="controls col-sm-4">
                    
                </div>
                <div id="selectExpression" class="controls col-sm-4">
                    
                </div>
            </div>
            <div class="controls col-sm-12 mb-3">
                <textarea rows="3" cols="12" class="inputElement textAreaElement col-lg-12" name="description"></textarea>
            </div>

            <div class="setValueContent">
            </div>
        </div>
        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}