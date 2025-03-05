{* Added by The Vi on 2025-03-05 *}
{strip}
<div class="modal-dialog modal-content">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE=""}
    <form id="deletePipelineEmptyModal" class="form-content fancyScrollbar form-horizontal deletePipelineEmptyModal"
        method="POST">
        <input type="hidden" name="pipelineId" value="{$PIPELINE_ID}" />
        <input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />
        <div class="modal-body text-center" style="padding: 20px;">
            <p style="font-size: 18px; font-weight: 400;">
                {vtranslate('LBL_ARE_YOU_SURE_DELETE_PIPELINE', $MODULE_NAME)}
            </p>

        </div>

        <div class="modal-footer text-center">
            <button type="button" class="btn btn-secondary"
                style="background-color: white; color: #008ecf; border: 1px solid #ced4da;" data-dismiss="modal">
                {vtranslate('LBL_CANCEL', $MODULE_NAME)}
            </button>
            <button type="submit" class="btn btn-primary">
                {vtranslate('LBL_DELETE', $MODULE_NAME)}
            </button>
        </div>
        <!-- {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'} -->

    </form>
</div>
{/strip}