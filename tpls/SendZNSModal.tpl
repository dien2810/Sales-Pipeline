{* Added by Minh Hoang on 2021-01-23 *}

{strip}
<div class="modal-dialog modal-content modal-width-900">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_SEND_ZNS', $MODULE_NAME)}" }
    <form id="form-send-zns" class="form-horizontal sendZNSModal form-modal" method="POST">
        <div class="d-flex align-item justify-content-center form-content">
            <div>
                <input type="hidden" name="leftSideModule" value="{$SELECTED_MODULE_NAME}" />
                <div class="form-group">
                    <div class="fieldLabel col-sm-5 text-left ml-3">
                        {vtranslate('LBL_TASK_NAME', $MODULE_NAME)}
                        <span class="redColor">*</span>
                    </div>
                    <div class="controls col-sm-6">
                        <input type="text" class="inputElement referencefield-wrapper" name="name" value="">
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-5 text-left ml-3">
                        {vtranslate('LBL_CHOOSE_ZOA', $MODULE_NAME)}
                        <span class="redColor">*</span>
                    </div>
                    <div class="controls col-sm-6">
                        <div class="referencefield-wrapper">
                            <select class="inputElement text-left select2" data-rule-required="true">
                                <option value="">Chọn tài khoản</option>
                                <option value="value1">Option 1</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-5 text-left ml-3">
                        {vtranslate('LBL_CHOOSE_PHONE', $MODULE_NAME)}	
                        <span class="redColor">*</span>
                    </div>
                    <div class="controls col-sm-6">
                        <div class="col-center">
                            <select multiple class="inputElement select2">
                                <option value="value1">Di động (0123456789)</option>
                                <option value="value2">Di động (1212312312)</option>
                                <option value="value3">Di động (9686895949)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fieldLabel col-sm-5 text-left ml-3">
                        {vtranslate('LBL_ZOA_FORM', $MODULE_NAME)}
                        <span class="redColor">*</span>
                    </div>
                    <div class="controls col-sm-6">
                        <div class="referencefield-wrapper ">
                            <select class="inputElement text-left select2" data-rule-required="true">
                                <option value="">Chọn tin nhắn mẫu</option>
                                <option value="value1">Option 1</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="display: block;">
                    <label class="text-left ml-3 def-variable-text">
                        {vtranslate('LBL_DEFINITION_VARIABLES', $MODULE_NAME)}
                    </label>
                    

                    <div class="group-variable">
                        <div class="d-flex align-item def-variable">
                            <div class="col-center">
                                <input disabled type="text" value="$contact_lastname$" class="variable-input">
                            </div>
                            <i class="far fa-equals"></i>
                            <select class="inputElement text-left select2 variable-select">
                                <option value="">{vtranslate('LBL_CHOOSE_FIELD', $MODULE_NAME)}</option>
                                <option value="value1">Option 1</option>
                            </select>
                        </div>
                        
                        <div class="d-flex align-item def-variable">
                            <div class="col-center">
                                <input disabled type="text" value="$lead_booking_code$" class="variable-input">
                            </div>
                            <i class="far fa-equals"></i>
                            <select class="inputElement text-left select2 variable-select">
                                <option value="">{vtranslate('LBL_CHOOSE_FIELD', $MODULE_NAME)}</option>
                                <option value="value1">Option 1</option>
                            </select>
                        </div>
    
                        <div class="d-flex align-item def-variable">
                            <div class="col-center">
                                <input disabled type="text" value="$phone_number$" class="variable-input">
                            </div>
                            <i class="far fa-equals"></i>
                            <select class="inputElement text-left select2 variable-select">
                                <option value="">{vtranslate('LBL_CHOOSE_FIELD', $MODULE_NAME)}</option>
                                <option value="value1">Option 1</option>
                            </select>
                        </div>

                        <div class="d-flex align-item def-variable">
                            <div class="col-center">
                                <input disabled type="text" value="$date_code$" class="variable-input">
                            </div>
                            <i class="far fa-equals"></i>
                            <select class="inputElement text-left select2 variable-select">
                                <option value="">{vtranslate('LBL_CHOOSE_FIELD', $MODULE_NAME)}</option>
                                <option value="value1">Option 1</option>
                            </select>
                        </div>

                        <div class="d-flex align-item def-variable">
                            <div class="col-center">
                                <input disabled type="text" value="$time_code$" class="variable-input">
                            </div>
                            <i class="far fa-equals"></i>
                            <select class="inputElement text-left select2 variable-select">
                                <option value="">{vtranslate('LBL_CHOOSE_FIELD', $MODULE_NAME)}</option>
                                <option value="value1">Option 1</option>
                            </select>
                        </div>  

                        <div class="d-flex align-item def-variable">
                            <div class="col-center">
                                <input disabled type="text" value="$lead_booking_address$" class="variable-input">
                            </div>
                            <i class="far fa-equals"></i>
                            <select class="inputElement text-left select2 variable-select">
                                <option value="">{vtranslate('LBL_CHOOSE_FIELD', $MODULE_NAME)}</option>
                                <option value="value1">Option 1</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="logo_zns" style="display: none;">
                <img src="./resources/images/zns.png"alt="">
            </div>

            <div class="message-form mb-3" style="display: block;">
                <div class="message-content">
                    <img src="./resources/images/logo-cloudgo.png" alt="CloudGO Logo">
                    <div class="send-content">
                        <div class="send-content-header">
                            xác nhận thông tin đặt lịch
                        </div>
                        <div class="send-content-text">
                            Cảm ơn Quý Khách 
                            <strong>&lt;contact_lastname&gt;</strong> 
                            đã đặt lịch hẹn tư vấn cùng chuyên gia CloudGO.
                        </div>
                        <div class="send-content-text">
                            Chúng tôi xác nhận đã nhận được lịch hẹn của Quý khách như sau:
                        </div>
                        <div class="info-box">
                            <table>
                                <tr>
                                    <td>Mã đặt lịch:</td>
                                    <td>&lt;lead_booking_code&gt;</td>
                                </tr>
                                <tr>
                                    <td>Điện thoại:</td>
                                    <td>&lt;phone_number&gt;</td>
                                </tr>
                                <tr>
                                    <td>Ngày hẹn:</td>
                                    <td>&lt;date_code&gt;</td>
                                </tr>
                                <tr>
                                    <td>Giờ hẹn:</td>
                                    <td>&lt;time_code&gt;</td>
                                </tr>
                                <tr>
                                    <td>Địa chỉ:</td>
                                    <td>&lt;lead_booking_address&gt;</td>
                                </tr>
                            </table>                            
                        </div>                  
                        <button class="btn btn-care-about mt-2 w100">Quan tâm OA CloudGO</button>
                        <button class="btn btn-contact mt-2 w100">
                            <i class="far fa-phone icon-phone"></i>
                            Liên hệ hotline CloudGO
                        </button>
                    </div>
                </div>
            </div>            
        </div>
        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}