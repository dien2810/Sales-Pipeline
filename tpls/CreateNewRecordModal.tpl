{* Added by Minh Hoang on 2021-02-10 *}

{strip}
<div class="modal-dialog modal-content modal-width-900">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_CREATE_NEW_RECORD',
    $MODULE_NAME)}"
    }
    <form id="form-create-new-record" class="form-horizontal createNewRecordModal form-modal" method="POST">
        <input type="hidden" name="taskType" id="taskType" value="VTCreateEntityTask" />
        <div class="form-content">
            <div class="taskTypeUi">
                <div class="form-group">
                    <div class="controls col-sm-3 text-left ml-3">
                        {vtranslate('LBL_TASK_NAME', $MODULE_NAME)}
                        <span class="redColor">*</span>
                    </div>
                    <div class="controls col-sm-8">
                        <input name="action_name" type="text" class="inputElement" data-rule-required="true">
                    </div>
                </div>
                <div class="form-group">
                    <div class="controls col-sm-3 text-left ml-3">
                        {vtranslate('LBL_CREATE_A_RECORD_IN', $MODULE_NAME)}
                        <span class="redColor">*</span>
                    </div>
                    <div class="controls col-sm-8">
                        <select id="createEntityModule" name="entity_type" class="inputElement text-left select2"
                            data-rule-required="true">
                            <option value="">{vtranslate('LBL_CHOOSE_FIELD', $MODULE_NAME)}</option>
                            {foreach from=$RELATED_MODULES item=MODULE}
                            <option value="{$MODULE}">{vtranslate($MODULE,$MODULE)}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div id="addCreateEntityContainer" style="margin-bottom: 70px; padding: 15px;">
                    {include file="modules/Settings/PipelineConfig/tpls/CreateEntity.tpl"}
                </div>
            </div>
            <div class="newDataField">
            </div>
        </div>

        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}