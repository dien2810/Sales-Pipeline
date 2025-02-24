{*
	Name: AddNotificationModalAddNotificationModal.tpl
	Author: Dien Nguyen
	Date: 2025-01-13
	Purpose: Modal add notification
*}
{strip}
    <div id="addNotificationModal" class="modal-dialog modal-content"> 
        {assign var=HEADER_TITLE value="Thông báo"} 
        {include file='ModalHeader.tpl'|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
        <form class="form-horizontal addNotificationForm" method="POST">
            <input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />
            <div id="addNotificationContainer" class="form-group mt-3">
                <div class="form-group">
                    <label class="control-label fieldLabel col-sm-5">
                        <span>Nhãn hiển thị (Tiếng việt)</span>
                        <span class="redColor">*</span>
                    </label>
                    <div class="controls col-sm-6">
                        <div class="referencefield-wrapper ">
                            <select name="swap_status" class="inputElement text-left select2" data-rule-required="true">
                                <option value="">Chọn một giá trị</option>
                                <option value="value1">Option 1</option>
                                <option value="value2">Option 2</option>
                                <option value="value3">Option 3</option>
                            </select>
                            <span class="btn-add-new-stage-modal cursorPointer clearfix" title="Thêm mới"
                                style="margin-left: 7px">
                                <i class="far fa-plus"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label fieldLabel col-sm-5">
                        <span>Nhãn hiển thị (Tiếng Anh)</span>
                        <span class="redColor">*</span>
                    </label>
                    <div class="controls col-sm-6">
                        <input type="text" name="serial_no" class="form-control" data-rule-required="true" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label fieldLabel col-sm-5">
                        <span>Giá trị</span>
                        <span class="redColor">*</span>
                    </label>
                    <div class="controls col-sm-6">
                        <input type="text" name="website" class="form-control" data-rule-required="true" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label fieldLabel col-sm-5">
                        <span>Chọn màu</span>
                        <span class="redColor">*</span>
                    </label>
                    <div class="controls col-sm-6">
                        <input name="color" value="{$CURRENT_COLOR}" data-rule-required="true" />
                    </div>
                </div>
            </div>
            {include file='ModalFooter.tpl'|@vtemplate_path:'Vtiger'} 
        </form> 
    </div>
{/strip}