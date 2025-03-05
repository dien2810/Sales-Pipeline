{* Added by The Vi on 2025-03-05 *}
{strip}
<div class="modal-dialog modal-content">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_ADD_STEP', $MODULE_NAME)}" }
    <form id="add-stage-pipeline" class="form-horizontal addStepPipelineModal" method="POST" style="margin-top: 20px;">
        <input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />
        <div class="form-group">
            <!-- <di>{$FIELD_VALUE_ID}</di> -->
            <label class="control-label fieldLabel col-sm-5">
                <span>{vtranslate('LBL_DISPLAY_LABEL_VN', $MODULE_NAME)}</span>
                <span class="redColor">*</span>
            </label>
            <div class="controls col-sm-6">
                <div class="referencefield-wrapper">
                    <select name="vn_label_select" class="inputElement text-left select2" data-rule-required="true"
                        id="vn_label_select">
                        <option value="">{vtranslate('LBL_CHOOSE_A_VALUE', $MODULE_NAME)}</option>
                        {foreach from=$SELECTED_PICKLISTFIELD_ALL_VALUES key=key item=PICKLIST_VALUE}
                        <option data-en="{$PICKLIST_VALUE.LABEL_DISPLAY_EN}" data-value="{$PICKLIST_VALUE.value}"
                            data-color="{$PICKLIST_VALUE.color|default:'#ffffff'}">
                            {$PICKLIST_VALUE.LABEL_DISPLAY_VN}
                        </option>
                        {/foreach}
                    </select>
                    <button type="button" class="btn-add-new-stage-modal cursorPointer clearfix"
                        title="{vtranslate('LBL_ADD_NEW', $MODULE_NAME)}" style="margin-left: 7px">
                        <i class="far fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label fieldLabel col-sm-5">
                <span>{vtranslate('LBL_DISPLAY_LABEL_EN', $MODULE_NAME)}</span>
                <span class="redColor">*</span>
            </label>
            <div class="controls col-sm-6">
                <input type="text" name="en_label" id="en_label" class="form-control" data-rule-required="true"
                    readonly />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label fieldLabel col-sm-5">
                <span>{vtranslate('LBL_VALUE', $MODULE_NAME)}</span>
                <span class="redColor">*</span>
            </label>
            <div class="controls col-sm-6">
                <input type="text" name="value" id="value_field" class="form-control" data-rule-required="true"
                    readonly />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label fieldLabel col-sm-5">
                <span>{vtranslate('LBL_CHOOSE_COLOR', $MODULE_NAME)}</span>
                <span class="redColor">*</span>
            </label>
            <div class="controls col-sm-6">
                <input type="color" id="color_field" name="color" value="" data-rule-required="true" />
            </div>
        </div>
        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#vn_label_select').on('change', function () {
            var selectedOption = jQuery(this).find('option:selected');
            var enLabel = selectedOption.data('en');
            var value = selectedOption.data('value');
            var color = selectedOption.data('color');

            jQuery('#en_label').val(enLabel);
            jQuery('#value_field').val(value);
            jQuery('#color_field').val(color || '#ffffff');
            var select2Chosen = jQuery('#select2-chosen-7');
            select2Chosen.css('background-color', color);
        });
    });
</script>
{/strip}