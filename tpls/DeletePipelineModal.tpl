{strip}
<div class="modal-dialog modal-content">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_DELETE_PIPELINE', $MODULE_NAME)}"}
    <form class="deletePipelineModal form-content fancyScrollbar form-horizontal" id="deletePipelineModal"
        method="POST">
        <input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />

        <div class="row form-group" style="margin-top: 20px;">
            <div class="fieldLabel text-right col-md-4">
                {vtranslate('LBL_REPLACEMENT_PIPELINE', $MODULE_NAME)} <span class="redColor">*</span>
            </div>
            <div class="fieldValue col-md-8">
                <select name="swap_status" id="pipeline-list-replace" class="inputElement text-left select2"
                    data-rule-required="true">
                    <option value="">{vtranslate('LBL_SELECT_OPTION', $MODULE_NAME)}</option>
                    {foreach from=$PIPELINE_REPLACE_LIST item=pipeline}
                    <option value="{$pipeline.pipelineid}">{$pipeline.name}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="row form-group">
            <div class="fieldLabel text-right col-md-4">
                {vtranslate('LBL_CURRENT_STEP', $MODULE_NAME)}
            </div>
            <div class="fieldValue col-md-8" style="text-align: center;">
                {vtranslate('LBL_REPLACEMENT_STEP', $MODULE_NAME)} <span class="redColor"
                    style="margin-right: 100px;">*</span>
            </div>
        </div>

        {foreach from=$STAGE_CURRENT_LIST item=stage}
        <div class="row form-group">
            <div class="fieldLabel text-right col-md-4" style="margin-top:5px">
                <span class="stage-current" style="display: inline-block;">{$stage.name}</span>
                <span style="display: inline-block; margin-left: 10px;">❯</span>
            </div>
            <div class="fieldValue col-md-8">
                <select name="swap_status[{$stage.stageid}]" class="inputElement text-left select2"
                    data-rule-required="true">
                    <option value="">{vtranslate('LBL_SELECT_OPTION', $MODULE_NAME)}</option>
                </select>
            </div>
        </div>
        {/foreach}

        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}