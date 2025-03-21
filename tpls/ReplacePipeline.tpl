{strip}
<div class="modal-dialog modal-content">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_EDIT_PIPELINE', $MODULE_NAME)}"}
    <form class="replacePipelineModal form-content fancyScrollbar form-horizontal" id="replacePipelineModal"
        method="POST">
        <input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />
        <table class="table table-borderless fieldBlockContainer form-content">
            <tr class="row form-group" style="margin-top: 20px;">
                <td class="fieldLabel alignMiddle text-right col-md-4">
                    {vtranslate('LBL_REPLACEMENT_PIPELINE', $MODULE_NAME)} <span class="redColor">*</span>
                </td>
                <td class="fieldValue col-md-8">
                    <select name="swap_status" id="pipeline-list-replace" class="inputElement text-left select2"
                        data-rule-required="true">
                        <option value="">{vtranslate('LBL_SELECT_OPTION', $MODULE_NAME)}</option>
                        {foreach from=$PIPELINE_REPLACE_LIST item=pipeline}
                        <option value="{$pipeline.pipelineid}">{$pipeline.name}</option>
                        {/foreach}
                    </select>
                </td>
            </tr>

            <tr class="row form-group" style="margin-top: 20px;">
                <td class="fieldLabel alignMiddle text-right col-md-4">
                    {vtranslate('LBL_STAGE_PIPELINE', $MODULE_NAME)} <span class="redColor">*</span>
                </td>
                <td class="fieldValue col-md-8">
                    <select name="swap_status[{$stage.stageid}]" class="inputElement text-left select2"
                        data-rule-required="true">
                        <option value="">{vtranslate('LBL_SELECT_OPTION', $MODULE_NAME)}</option>
                    </select>
                </td>
            </tr>
        </table>
        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}