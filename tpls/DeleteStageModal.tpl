{strip}
<div class="modal-dialog modal-content">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_DELETE_STAGE', $MODULE_NAME)}"}
    <form id="delete-stage-pipeline" class="form-horizontal deleteStagePipelineModal" method="POST"
        style="margin-top: 20px;">
        <input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />

        <div class="form-group">
            <label class="control-label fieldLabel col-sm-5">
                <span>{vtranslate('LBL_STAGE_TO_DELETE', $MODULE_NAME)}</span>
            </label>
            <div class="controls col-sm-6">
                <!-- Trường ẩn chứa giá trị cần gửi lên server -->
                <input type="hidden" name="name_stage_delete_hidden" value="" />
                <!-- Trường hiển thị cho giao diện, không có thuộc tính name nên sẽ không được submit -->
                <input type="text" value="{vtranslate('LBL_DISPLAY_VALUE', $MODULE_NAME)}" id="name_stage_delete"
                    class="form-control" readonly />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label fieldLabel col-sm-5">
                <span>{vtranslate('LBL_REPLACEMENT_STAGE', $MODULE_NAME)}</span>
                <span class="redColor">*</span>
            </label>
            <div class="controls col-sm-6">
                <div class="referencefield-wrapper">
                    <select name="list_stage_select" class="inputElement text-left select2" data-rule-required="true"
                        id="list_stage_select">
                        <option value="">{vtranslate('LBL_SELECT_VALUE', $MODULE_NAME)}</option>
                        {foreach from=$STAGE_LIST item=stage}
                        <option value="{$stage.stageid}" data-color="{$stage.color_code|default:'#ffffff'}">
                            {$stage.name}
                        </option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>

        {include file="ModalFooter.tpl"|vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}