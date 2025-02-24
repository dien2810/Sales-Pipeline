{strip}
<div class="modal-dialog modal-content">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="Thêm bước" }
    <form id="add-stage-pipeline-new" class="form-horizontal add-stage-pipeline-new" method="POST"
        style="margin-top: 20px;">
        <input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />
        <div class="form-group">
            <label class="control-label fieldLabel col-sm-5">
                <span>Nhãn hiển thị (Tiếng việt)</span>
                <span class="redColor">*</span>
            </label>
            <div class="controls col-sm-6">
                <input type="text" name="itemLabelDisplayVn" class="form-control" data-rule-required="true" />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label fieldLabel col-sm-5">
                <span>Nhãn hiển thị (Tiếng Anh)</span>
                <span class="redColor">*</span>
            </label>
            <div class="controls col-sm-6">
                <input type="text" name="itemLabelDisplayEn" class="form-control" data-rule-required="true" />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label fieldLabel col-sm-5">
                <span>Giá trị</span>
                <span class="redColor">*</span>
            </label>
            <div class="controls col-sm-6">
                <input type="text" name="newValue" class="form-control" data-rule-required="true" />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label fieldLabel col-sm-5">
                <span>Chọn màu</span>
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