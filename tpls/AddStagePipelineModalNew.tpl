{* Added by The Vi on 2025-03-05 *}
{strip}
<div class="modal-dialog modal-content">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_ADD_STEP', $MODULE_NAME)}" }
    <form id="add-stage-pipeline-new" class="form-horizontal add-stage-pipeline-new" method="POST"
        style="margin-top: 20px;">
        <input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />
        <div class="form-group">
            <label class="control-label fieldLabel col-sm-5">
                <span>{vtranslate('LBL_DISPLAY_LABEL_VN', $MODULE_NAME)}</span>
                <span class="redColor">*</span>
            </label>
            <div class="controls col-sm-6">
                <input type="text" name="itemLabelDisplayVn" class="form-control" data-rule-required="true" />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label fieldLabel col-sm-5">
                <span>{vtranslate('LBL_DISPLAY_LABEL_EN', $MODULE_NAME)}</span>
                <span class="redColor">*</span>
            </label>
            <div class="controls col-sm-6">
                <input type="text" name="itemLabelDisplayEn" class="form-control" data-rule-required="true" />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label fieldLabel col-sm-5">
                <span>{vtranslate('LBL_VALUE', $MODULE_NAME)}</span>
                <span class="redColor">*</span>
            </label>
            <div class="controls col-sm-6">
                <input type="text" name="newValue" class="form-control" data-rule-required="true" />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label fieldLabel col-sm-5">
                <span>{vtranslate('LBL_CHOOSE_COLOR', $MODULE_NAME)}</span>
                <span class="redColor">*</span>
            </label>
            <div class="controls col-sm-6">
                <input name="color" value="" data-rule-required="true" />
            </div>
        </div>
        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}