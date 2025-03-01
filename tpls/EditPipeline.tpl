{strip}
<link rel="stylesheet" href="{vresource_url('modules/Settings/PipelineConfig/resources/EditPipeline.css')}">
</link>
<script src="{vresource_url('resources/CustomColorPicker.js')}"></script>
<div id="editPipeline-page" class="row-fluid">
    <form autocomplete="off" id="editPipeline" name="editPipeline">
        <div class="fieldBlockContainer">
            <!-- Sử dụng LBL_CREATE_PIPELINE thay cho “Tạo mới pipeline” -->
            <h4 class="fieldBlockHeader" style="margin-top:10px">{vtranslate('LBL_CREATE_PIPELINE', $MODULE_NAME)}</h4>
            <div class="contents tabbable" style="margin-top: 40px;">
                <ul class="nav nav-tabs marginBottom10px">
                    <li class="tab1 active">
                        <a data-toggle="tab" href="#tab1">
                            <strong>{vtranslate('LBL_PIPELINE_INFORMATION', $MODULE_NAME)}</strong>
                        </a>
                    </li>
                    <li class="tab2">
                        <a data-toggle="tab" href="#tab2">
                            <strong>{vtranslate('LBL_AUTOMATION', $MODULE_NAME)}</strong>
                        </a>
                    </li>
                </ul>
                <div class="tab-content overflowVisible">
                    <div class="tab-pane active" id="tab1">
                        <table class="table table-borderless">
                            <tbody>
                                <!-- Row 1 -->
                                <tr>
                                    <td class="fieldLabel name alignMiddle">
                                        {vtranslate('LBL_PIPELINE_NAME', $MODULE_NAME)}&nbsp;
                                        <span class="redColor">*</span>
                                    </td>
                                    <td class="fieldValue name">
                                        <input type="text" class="inputElement" name="name" value=""
                                            data-rule-required="true" aria-required="true">
                                    </td>
                                    <td class="fieldLabel time alignMiddle">
                                        {vtranslate('LBL_PIPELINE_TIME', $MODULE_NAME)}&nbsp;
                                    </td>
                                    <td class="fieldValue">
                                        <!-- Hiển thị khi là module khác -->
                                        <div class="input-group othermodule">
                                            <input type="text" name="time" value="" class="inputElement time"
                                                style="width: 30px;">
                                            <select class="inputElement select2 select2-offscreen"
                                                style="width:150px; margin-left: 25px;" name="timetype" tabindex="-1"
                                                title="">
                                                <option value="Day">{vtranslate('LBL_DAY', $MODULE_NAME)}</option>
                                                <option value="Month">{vtranslate('LBL_MONTH', $MODULE_NAME)}</option>
                                                <option value="Year">{vtranslate('LBL_YEAR', $MODULE_NAME)}</option>
                                            </select>
                                            <span data-toggle="tooltip" style="margin-top:7px; margin-left: 20px;"
                                                data-tippy-content="{vtranslate('LBL_PIPELINE_TIME_TOOLTIP', $MODULE_NAME)}">
                                                <i class="far fa-info-circle"></i>
                                            </span>
                                        </div>
                                        <!-- Hiện khi là module cốt lõi -->
                                        <div class="input-group potentials">
                                            <span class="textElement toal-time-pipeline"
                                                style="margin-top:7px; margin-left: 10px;">0
                                                &nbsp;{vtranslate('LBL_DAY', $MODULE_NAME)}</span>
                                            <span data-toggle="tooltip" style="margin-top:7px; margin-left: 10px;"
                                                data-tippy-content="{vtranslate('LBL_PIPELINE_INFO_TOOLTIP', $MODULE_NAME)}">
                                                <i class="far fa-info-circle"></i>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Row 2 -->
                                <tr>
                                    <td class="fieldLabel module alignMiddle">
                                        {vtranslate('LBL_MODULE', $MODULE_NAME)}&nbsp;
                                        <span class="redColor">*</span>
                                    </td>
                                    <td class="fieldValue module">
                                        <select class="select2-container select2 inputElement col-sm-6 selectModule"
                                            id="listModule" name="module">
                                            {foreach item=PICKLIST_MODULE from=$PICKLIST_MODULES}
                                            <option {if $SELECTED_MODULE_NAME eq $PICKLIST_MODULE->get('name')}
                                                selected="" {/if}
                                                value="{$PICKLIST_MODULE->get('name')}">
                                                {vtranslate($PICKLIST_MODULE->get('name'),
                                                $PICKLIST_MODULE->get('name'))}
                                            </option>
                                            {/foreach}
                                        </select>
                                    </td>
                                    <td class="fieldLabel auto alignMiddle">
                                        {vtranslate('LBL_AUTO_TRANSITION', $MODULE_NAME)}&nbsp;
                                    </td>
                                    <td class="fieldValue" style="width:25%">
                                        <input type="hidden" name="autoTransition" value="0">
                                        <input class="inputElement" style="width:16px;height:16px;" id="autoTransition"
                                            data-fieldname="autoTransition" data-fieldtype="checkbox" type="checkbox"
                                            name="autoTransition">
                                    </td>
                                </tr>
                                <!-- Row 3 -->
                                <tr>
                                    <td class="fieldLabel grant alignMiddle">
                                        {vtranslate('LBL_PERMISSIONS', $MODULE_NAME)}&nbsp;
                                    </td>
                                    <td class="fieldValue grant">
                                        <select multiple name="rolesSelected[]" class="inputElement select2"
                                            id="rolesDropdown">
                                            <option value="all" selected>{vtranslate('LBL_ALL', $MODULE_NAME)}</option>
                                            {foreach from=$ROLES_LIST item=ROLE}
                                            <option value="{$ROLE->get('roleid')}" disabled>
                                                {vtranslate($ROLE->get('rolename'), $MODULE_NAME)}
                                            </option>
                                            {/foreach}
                                        </select>
                                    </td>
                                    <td class="fieldLabel description alignMiddle">
                                        {vtranslate('LBL_DESCRIPTION', $MODULE_NAME)}&nbsp;
                                    </td>
                                    <td class="fieldValue" style="width:25%">
                                        <textarea rows="3" class="inputElement textAreaElement col-lg-12"
                                            name="description"></textarea>
                                    </td>
                                </tr>
                                <!-- Row 4 -->
                                <tr>
                                    <td class="fieldLabel status alignMiddle">
                                        {vtranslate('LBL_STATUS', $MODULE_NAME)}&nbsp;
                                        <span class="redColor">*</span>
                                    </td>
                                    <td class="fieldValue status">
                                        <div class="pull-left">
                                            <span style="margin-right: 50px;">
                                                <input name="status" type="radio" value="active" checked="">&nbsp;
                                                <span>{vtranslate('LBL_ACTIVATE', $MODULE_NAME)}</span>
                                            </span>
                                            <span style="margin-right: 10px;">
                                                <input name="status" type="radio" value="inActive">
                                                &nbsp;<span>{vtranslate('LBL_INACTIVATE', $MODULE_NAME)}</span>
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
                                            <h5 class="action-title">
                                                {vtranslate('LBL_ONCE_ACTION', $MODULE_NAME)}
                                            </h5>
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
                                            <h5 class="action-title">
                                                {vtranslate('LBL_CONDITIONAL_ACTION', $MODULE_NAME)}
                                            </h5>
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
                                            data-stageid="{$stage.stageid}">
                                            + {vtranslate('LBL_ADD_ACTION', $MODULE_NAME)}
                                        </button>
                                    </div>
                                    <div class="condition-box">
                                        {if $stage.conditions|@count > 0}
                                        <div class="action-item btnAddCondition">
                                            <i class="fal fa-cogs ml-2"></i>
                                            <p class="text-primary pt-3">
                                                {vtranslate('LBL_AUTO_TRANSITION_CONDITION', $MODULE_NAME)}
                                            </p>
                                            <i class="fal fa-times removeCondition"></i>
                                        </div>
                                        {else}
                                        <button type="button" class="btn text-primary btnAddCondition"
                                            data-stageid="{$stage.stageid}">
                                            + {vtranslate('LBL_ADD_CONDITION', $MODULE_NAME)}
                                        </button>
                                        {/if}
                                    </div>
                                </div>
                                {/foreach}
                                {else}
                                <div class="stepItem">
                                    <div class="action-box">
                                        <button type="button" class="btn text-primary btnAddAction">
                                            + {vtranslate('LBL_ADD_ACTION', $MODULE_NAME)}
                                        </button>
                                    </div>
                                    <div class="condition-box">
                                        <button type="button" class="btn text-primary btnAddCondition">
                                            + {vtranslate('LBL_ADD_CONDITION', $MODULE_NAME)}
                                        </button>
                                    </div>
                                </div>
                                {/if}
                            </div>
                            {else}
                            <div class="stepInfo">
                            </div>
                            {/if}
                        </div>
                        <div id="config-footer" class="modal-overlay-footer clearfix">
                            <div class="row clear-fix">
                                <div class="textAlignCenter col-lg-12 col-md-12 col-sm-12">
                                    <a class="btn btn-default btn-outline" onclick="history.back()">
                                        {vtranslate('LBL_CANCEL', $MODULE_NAME)}
                                    </a>
                                    &nbsp;
                                    <button type="submit" class="btn btn-primary savePipeline">
                                        {vtranslate('LBL_SAVE', $MODULE_NAME)}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- Nút Thêm hoặc Hủy -->
<div class="modal-overlay-footer clearfix">
    <div class="row clear-fix">
        <div class="textAlignCenter col-lg-12 col-md-12 col-sm-12">
            <button id="cancelButton" class="btn cancelButton btn-default module-buttons">
                {vtranslate('LBL_CANCEL', $MODULE_NAME)}
            </button>
            <button type="submit" class="btn nextButton btn-primary module-buttons" style="margin-left: 10px">
                {vtranslate('LBL_NEXT', $MODULE_NAME)}
            </button>
        </div>
    </div>
</div>
{/strip}