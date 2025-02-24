{strip}
<link rel="stylesheet" href="{vresource_url('modules/Settings/PipelineConfig/resources/EditPipeline.css')}">
</link>
<script src="{vresource_url('resources/CustomColorPicker.js')}"></script>
<div id="editPipeline-page" class="row-fluid">
    <form autocomplete="off" id="editPipeline" name="editPipeline">
        <div class="fieldBlockContainer">
            <h4 class="fieldBlockHeader" style="margin-top:10px">Tạo mới pipleline</h4>
            <div class="contents tabbable" style="margin-top: 40px;">
                <ul class="nav nav-tabs marginBottom10px">
                    <li class="tab1 active"><a data-toggle="tab" href="#tab1"><strong>
                                Thông tin pipeline</strong></a></li>
                    <li class="tab2"><a data-toggle="tab" href="#tab2"><strong>Tự động hóa</strong></a></li>
                </ul>
                <div class="tab-content overflowVisible">
                    <div class="tab-pane active" id="tab1">
                        <table class="table table-borderless">
                            <tbody>
                                <!-- Dòng 1 -->
                                <tr>
                                    <td class="fieldLabel name alignMiddle">Tên pipeline&nbsp;
                                        <span class="redColor">*</span>
                                    </td>
                                    <td class="fieldValue name">
                                        <input type="text" class="inputElement" name="name" value=""
                                            data-rule-required="true" aria-required="true">
                                    </td>
                                    <td class="fieldLabel time alignMiddle">Thời gian pipeline&nbsp;</td>

                                    <td class="fieldValue">
                                        <!-- Hiện khi là module khác -->
                                        <div class="input-group othermodule">
                                            <input type="text" name="time" value="" class="inputElement time"
                                                style="width: 30px;">
                                            <select class="inputElement select2 select2-offscreen"
                                                style="width:150px; margin-left: 25px;" name="timetype" tabindex="-1"
                                                title="">
                                                <!-- <option value="">Chọn một giá trị</option> -->
                                                <option value="Day">Ngày</option>
                                                <option value="Month">Tháng</option>
                                                <option value="Year">Năm</option>
                                            </select>
                                            <span data-toggle="tooltip" style="margin-top:7px; margin-left: 20px;"
                                                data-tippy-content="Thời gian tối đa xử lý tối đa toàn bộ các bước  của 1 quy trình (pipeline).">
                                                <i class="far fa-info-circle"></i>
                                            </span>
                                        </div>
                                        <!-- Hiện khi là module cơ hôi -->
                                        <div class="input-group potentials">
                                            <span class="textElement toal-time-pipeline"
                                                style="margin-top:7px; margin-left: 10px;">0
                                                &nbsp;ngày</span>
                                            <span data-toggle="tooltip" style="margin-top:7px; margin-left: 10px;"
                                                data-tippy-content="Chọn 1 giá trị của Loại để phân nhóm người liên hệ (Khách hàng cá nhân, Đối tác, Người liên hệ, Khác).">
                                                <i class="far fa-info-circle"></i>
                                            </span>
                                        </div>
                                    </td>


                                </tr>
                                <!-- Dòng 2 -->
                                <tr>
                                    <td class="fieldLabel module alignMiddle">Module&nbsp;
                                        <span class="redColor">*</span>
                                    </td>
                                    <td class="fieldValue module">
                                        <select class="select2-container select2 inputElement col-sm-6 selectModule"
                                            id="listModule" name="module">
                                            {foreach item=PICKLIST_MODULE from=$PICKLIST_MODULES}
                                            <option {if $SELECTED_MODULE_NAME eq $PICKLIST_MODULE->get('name')}
                                                selected="" {/if}
                                                value="{$PICKLIST_MODULE->get('name')}">{vtranslate($PICKLIST_MODULE->get('name'),$PICKLIST_MODULE->get('name'))}
                                            </option>
                                            {/foreach}
                                        </select>
                                    </td>
                                    <td class="fieldLabel auto alignMiddle">Tự động chuyển bước&nbsp;</td>
                                    <td class="fieldValue" style="width:25%">
                                        <input type="hidden" name="autoTransition" value="0">
                                        <input class="inputElement" style="width:16px;height:16px;" id="autoTransition"
                                            data-fieldname="autoTransition" data-fieldtype="checkbox" type="checkbox"
                                            name="autoTransition">
                                    </td>
                                </tr>
                                <!-- Dòng 3 -->
                                <tr>
                                    <td class="fieldLabel grant alignMiddle">
                                        Phân quyền&nbsp;
                                    </td>
                                    <td class="fieldValue grant">
                                        <select multiple name="rolesSelected[]" class="inputElement select2"
                                            id="rolesDropdown">
                                            <option value="all" selected>Tất cả</option>
                                            {foreach from=$ROLES_LIST item=ROLE}
                                            <option value="{$ROLE->get('roleid')}" disabled>{$ROLE->get('rolename')}
                                            </option>
                                            {/foreach}
                                        </select>
                                    </td>

                                    <td class="fieldLabel description alignMiddle">Mô tả&nbsp;</td>
                                    <td class="fieldValue" style="width:25%">
                                        <textarea rows="3" class="inputElement textAreaElement col-lg-12 "
                                            name="description"></textarea>
                                    </td>
                                </tr>
                                <!-- Dòng 4 -->
                                <tr>
                                    <td class="fieldLabel status alignMiddle">Trạng thái&nbsp;
                                        <span class="redColor">*</span>
                                    </td>
                                    <td class="fieldValue status">
                                        <div class="pull-left">
                                            <span style="margin-right: 50px;">
                                                <input name="status" type="radio" value="active" checked="">&nbsp;
                                                <span>Kích hoạt</span>
                                            </span>
                                            <span style="margin-right: 10px;">
                                                <input name="status" type="radio" value="inActive">
                                                &nbsp;<span>Không kích hoạt</span>
                                            </span>
                                        </div>
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                        <hr />
                        <div style="height: 500px;">
                            <div id="stagePipelineValuesContainer">
                                {if empty($NO_PICKLIST_FIELDS)}
                                {include file="modules/Settings/PipelineConfig/tpls/StagePipeline.tpl"}
                                {/if}
                            </div>
                        </div>
                    </div>
                    {* Tab tự động hóa *}
                    <div class="tab-pane" id="tab2">
                        <div id="breadcrumb" class="breadcrumb text-center">
                            <!-- Thanh tiêu đề các bước -->
                            <!-- Tự động hóa -->
                            <!-- <div>Tự động hóa </div> -->
                            {if $MODE eq "EDIT"}
                            {if $STAGE_LIST|@count > 0}
                            <ul class="crumbs">
                                {foreach from=$STAGE_LIST item=stage key=index}
                                {assign var="stepClass" value="stepOdd"}
                                {if $index % 2 == 0}
                                {assign var="stepClass" value="stepEven"}
                                {/if}
                                <li class="step {$stepClass}" style="z-index:{$STAGE_LIST|@count - $index}">
                                    <a href="javascript:void(0)">
                                        <span class="stepNum">{$index + 1}</span>
                                        <span class="stepText">{$stage.name}</span>
                                    </a>
                                </li>
                                {/foreach}
                            </ul>
                            {/if}
                            <div class="stepInfo">
                                {if $STAGE_LIST|@count > 0}
                                {foreach from=$STAGE_LIST item=stage}
                                <div class="stepItem">
                                    <div class="action-box">
                                        {assign var="onceActions" value=[]}
                                        {assign var="conditionalActions" value=[]}
                                        {foreach from=$stage.actions item=action}
                                        {if $action.action_time_type == 'Once'}
                                        {assign var="onceActions" value=$onceActions|@array_merge:[$action]}
                                        {/if}
                                        {if $action.action_time_type == 'Conditional'}
                                        {assign var="conditionalActions"
                                        value=$conditionalActions|@array_merge:[$action]}
                                        {/if}
                                        {/foreach}
                                        {if $onceActions|@count > 0}
                                        <div class="action-type">
                                            <h5 class="action-title">Hành động thực hiện một lần</h5>
                                            {foreach from=$onceActions item=action}
                                            <div class="action-item">
                                                <i class="fal fa-phone ml-2"></i>
                                                <p class="text-primary pt-3">{$action.action_name}</p>
                                                <i class="fal fa-times"></i>
                                            </div>
                                            {/foreach}
                                        </div>
                                        {/if}
                                        {if $conditionalActions|@count > 0}
                                        <div class="action-type">
                                            <h5 class="action-title">Hành động thực hiện khi thỏa điều kiện</h5>
                                            {foreach from=$conditionalActions item=action}
                                            <div class="action-item">
                                                <i class="fal fa-envelope ml-2"></i>
                                                <p class="text-primary pt-3">{$action.action_name}</p>
                                                <i class="fal fa-times"></i>
                                            </div>
                                            {/foreach}
                                        </div>
                                        {/if}
                                        <button type="button" class="btn text-primary btnAddAction"
                                            data-stageid="{$stage.stageid}">+ Thêm thiết lập hành động</button>
                                    </div>
                                    <div class="condition-box">
                                        {if $stage.conditions|@count > 0}
                                        <div class="action-item btnAddCondition">
                                            <i class="fal fa-cogs ml-2"></i>
                                            <p class="text-primary pt-3">Điều kiện chuyển bước</p>
                                            <i class="fal fa-times removeCondition"></i>
                                        </div>
                                        {else}
                                        <button type="button" class="btn text-primary btnAddCondition"
                                            data-stageid="{$stage.stageid}">+ Thêm điều kiện</button>
                                        {/if}
                                    </div>
                                </div>
                                {/foreach}
                                {else}
                                <div class="stepItem">
                                    <div class="action-box">
                                        <button type="button" class="btn text-primary btnAddAction">+ Thêm thiết lập
                                            hành động</button>
                                    </div>
                                    <div class="condition-box">
                                        <button type="button" class="btn text-primary btnAddCondition">+ Thêm điều
                                            kiện</button>
                                    </div>
                                </div>
                                {/if}
                                {* <div class="stepItem">
                                    <div class="action-box">
                                        <div class="action-type">
                                            <h5 class="action-title">Hành động thực hiện một lần</h5>
                                            <div class="action-item">
                                                <i class="fal fa-phone ml-2"></i>
                                                <p class="text-primary pt-3">Cuộc gọi xác nhận thông tin KH</p>
                                                <i class="fal fa-times"></i>
                                            </div>
                                            <div class="action-item">
                                                <i class="fal fa-phone ml-2"></i>
                                                <p class="text-primary pt-3">Cuộc gọi xác nhận thông tin KH</p>
                                                <i class="fal fa-times"></i>
                                            </div>
                                        </div>
                                        <div class="action-type">
                                            <h5 class="action-title">Hành động thực hiện khi thỏa điều kiện</h5>
                                            <div class="action-item">
                                                <i class="fal fa-envelope ml-2"></i>
                                                <p class="text-primary pt-3">Gửi mail chào mừng đến với Cloud</p>
                                                <i class="fal fa-times"></i>
                                            </div>
                                        </div>
                                        <button type="button" class="btn text-primary btnAddAction">+ Thêm thiết lập
                                            hành động</button>
                                    </div>
                                    <div class="condition-box">
                                        <button type="button" class="btn text-primary btnAddCondition">+ Thêm điều
                                            kiện</button>
                                    </div>
                                </div>
                                <div class="stepItem">
                                    <div class="action-box">
                                        <div class="action-type">
                                            <h5 class="action-title">Hành động thực hiện một lần</h5>
                                            <div class="action-item">
                                                <i class="fal fa-phone ml-2"></i>
                                                <p class="text-primary pt-3">Cuộc gọi xác nhận thông tin KH</p>
                                                <i class="fal fa-times"></i>
                                            </div>
                                            <div class="action-item">
                                                <i class="fal fa-phone ml-2"></i>
                                                <p class="text-primary pt-3">Cuộc gọi xác nhận thông tin KH</p>
                                                <i class="fal fa-times"></i>
                                            </div>
                                        </div>
                                        <button type="button" class="btn text-primary btnAddAction">+ Thêm thiết lập
                                            hành động</button>
                                    </div>
                                    <div class="condition-box">
                                        <div class="action-item btnAddCondition">
                                            <i class="fal fa-cogs ml-2"></i>
                                            <p class="text-primary pt-3">Điều kiện chuyển bước</p>
                                            <i class="fal fa-times removeCondition"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="stepItem">
                                    <div class="action-box">
                                        <button type="button" class="btn text-primary btnAddAction">+ Thêm thiết lập
                                            hành động</button>
                                    </div>
                                    <div class="condition-box">
                                        <button type="button" class="btn text-primary btnAddCondition">+ Thêm điều
                                            kiện</button>
                                    </div>
                                </div>
                                <div class="stepItem">
                                    <div class="action-box">
                                        <button type="button" class="btn text-primary btnAddAction">+ Thêm thiết lập
                                            hành động</button>
                                    </div>
                                    <div class="condition-box">
                                        <button type="button" class="btn text-primary btnAddCondition">+ Thêm điều
                                            kiện</button>
                                    </div>
                                </div> *}
                            </div>
                            {else}
                            <div class="stepInfo">
                            </div>
                            {/if}
                        </div>
                        <div id="config-footer" class="modal-overlay-footer clearfix">
                            <div class="row clear-fix">
                                <div class="textAlignCenter col-lg-12 col-md-12 col-sm-12">
                                    <a class="btn btn-default btn-outline"
                                        onclick="history.back()">{vtranslate('LBL_CANCEL', $MODULE_NAME)}</a>
                                    &nbsp;
                                    <button type="submit" class="btn btn-primary savePipeline">{vtranslate('LBL_SAVE',
                                        $MODULE_NAME)}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- Nút thêm hoặc hủy -->
<div class="modal-overlay-footer clearfix">
    <div class="row clear-fix">
        <div class="textAlignCenter col-lg-12 col-md-12 col-sm-12">
            <button id="cancelButton" class="btn cancelButton btn-default module-buttons">Hủy</button>
            <button type="submit" class="btn nextButton btn-primary module-buttons" style="margin-left: 10px">
                Tiếp theo</button>
        </div>
    </div>
</div>

{/strip}