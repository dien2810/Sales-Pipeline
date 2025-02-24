{strip}
<div class="modal-dialog modal-content">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="Xóa bước" }
    <form id="delete-stage-pipeline" class="form-horizontal deleteStagePipelineModal" method="POST"
        style="margin-top: 20px;">
        <input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />
        <div class="form-group">
            <label class="control-label fieldLabel col-sm-5">
                <span>Bước xóa</span>
            </label>
            <div class="controls col-sm-6">
                <!-- Trường ẩn chứa giá trị cần gửi lên server -->
                <input type="hidden" name="name_stage_delete_hidden" value="" />
                <!-- Trường hiển thị cho giao diện, không có thuộc tính name nên sẽ không được submit -->
                <input type="text" value="Giá trị hiển thị" id="name_stage_delete" class="form-control" readonly />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label fieldLabel col-sm-5">
                <span>Bước thay thế</span>
                <span class="redColor">*</span>
            </label>
            <div class="controls col-sm-6">
                <div class="referencefield-wrapper">
                    <select name="list_stage_select" class="inputElement text-left select2" data-rule-required="true"
                        id="list_stage_select">
                        <option value="">Chọn một giá trị</option>
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