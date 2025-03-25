{strip}
<link rel="stylesheet" href="{vresource_url('modules/Settings/PipelineConfig/resources/DetailPipeline.css')}">
</link>
<link rel="stylesheet" href="{vresource_url('layouts/v7/resources/custom.css')}">
</link>
<script src="{vresource_url('resources/CustomPopover.js')}"></script>
<div class="editViewBody">
    <div class="addPipeline">
        <div class="fieldBlockContainer">
            <h4 class="fieldBlockHeader" style="margin-top:10px">{$PIPELINE_DETAIL.name}</h4>
            <div class="contents tabbable" style="margin-top: 40px;">
                <ul class="nav nav-tabs marginBottom10px">
                    <li class="tab1 active">
                        <a data-toggle="tab" href="#tab1"><strong>{vtranslate('LBL_PIPELINE_INFO',
                                $MODULE_NAME)}</strong></a>
                    </li>
                    <li class="tab2">
                        <a data-toggle="tab" href="#tab2"><strong>{vtranslate('LBL_AUTOMATION',
                                $MODULE_NAME)}</strong></a>
                    </li>
                </ul>
                <div class="tab-content overflowVisible">
                    <div class="tab-pane active" id="tab1">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="fieldLabel name alignMiddle">
                                        {vtranslate('LBL_PIPELINE_NAME', $MODULE_NAME)}&nbsp;<span
                                            class="redColor">*</span>
                                    </td>
                                    <td class="fieldValue name">
                                        <span>{$PIPELINE_DETAIL.name}</span>
                                    </td>
                                    <td class="fieldLabel time alignMiddle">{vtranslate('LBL_PIPELINE_TIME',
                                        $MODULE_NAME)}&nbsp;</td>
                                    <td class="fieldValue time">
                                        <div class="input-group d-flex align-item-center justify-content-center">
                                            <span>{$PIPELINE_DETAIL.time} {$PIPELINE_DETAIL.timetype}</span>
                                            <span data-toggle="tooltip" style="margin-top:7px; margin-left: 20px;"
                                                data-tippy-content="{vtranslate('LBL_TOTAL_EXECUTION_TIME', $MODULE_NAME)}">
                                                <i class="far fa-info-circle"></i>
                                            </span>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fieldLabel module alignMiddle">
                                        {vtranslate('LBL_MODULE', $MODULE_NAME)}&nbsp;<span class="redColor">*</span>
                                    </td>
                                    <td class="fieldValue module">
                                        <span>{vtranslate($PIPELINE_DETAIL.module, $MODULE_NAME)}</span>
                                    </td>
                                    <td class="fieldLabel auto alignMiddle">{vtranslate('LBL_AUTO_TRANSITION',
                                        $MODULE_NAME)}&nbsp;</td>
                                    <td class="fieldValue donotcall" style="width:25%">
                                        <span>{if $PIPELINE_DETAIL.autoTransition}{vtranslate('LBL_YES',
                                            $MODULE_NAME)}{else}{vtranslate('LBL_NO', $MODULE_NAME)}{/if}</span>
                                    </td>
                                </tr>

                                <tr class="listViewEntries">
                                    <td class="fieldLabel grant alignMiddle">
                                        {vtranslate('LBL_PERMISSIONS', $MODULE_NAME)}&nbsp;
                                    </td>
                                    <td class="listViewEntryValue" data-name="assigned_user_id" data-field-type="owner">
                                        <span class="fieldValue">
                                            <span class="value">
                                                <span class="owners custom-popover-wrapper">
                                                    {assign var="ROLE_COUNT" value=$PIPELINE_DETAIL.rolesSelected|count}
                                                    {if $ROLE_COUNT == 0}
                                                    <a class="no-owner" href="javascript: void(0)"></a>
                                                    {elseif $ROLE_COUNT == 1}
                                                    <span class="stand-owner">
                                                        <a
                                                            href="index.php?module=Roles&parent=Settings&view=Edit&record={$PIPELINE_DETAIL.rolesSelected[0].role_id}">
                                                            {$PIPELINE_DETAIL.rolesSelected[0].role_name}
                                                        </a>
                                                    </span>
                                                    {else}
                                                    <a class="stand-owner-plus custom-popover"
                                                        title="{vtranslate('LBL_ROLE_PERMISSIONS', $MODULE_NAME)}"
                                                        data-title="{vtranslate('Roles')}">
                                                        <span
                                                            class="stand-owner-plus-text">{$PIPELINE_DETAIL.rolesSelected[0].role_name}</span>
                                                        <span class="stand-owner-plus-icon"> +{$ROLE_COUNT - 1}</span>
                                                    </a>
                                                    <div class="custom-popover-content" style="display: none">
                                                        <ul class="owners-detail_owners">
                                                            {foreach from=$PIPELINE_DETAIL.rolesSelected item=role}
                                                            <li class="owners-detail_owner">
                                                                <a target="_blank"
                                                                    href="index.php?module=Roles&parent=Settings&view=Edit&record={$role.role_id}">
                                                                    {$role.role_name}
                                                                </a>
                                                            </li>
                                                            {/foreach}
                                                        </ul>
                                                    </div>
                                                    {/if}
                                                </span>
                                            </span>
                                        </span>
                                    </td>
                                    <td class="fieldLabel description alignMiddle">{vtranslate('LBL_DESCRIPTION',
                                        $MODULE_NAME)}&nbsp;</td>
                                    <td class="fieldValue" style="width:25%">
                                        <span>{$PIPELINE_DETAIL.description}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fieldLabel status alignMiddle">
                                        {vtranslate('LBL_STATUS', $MODULE_NAME)}&nbsp;<span class="redColor">*</span>
                                    </td>
                                    <td class="fieldValue status">
                                        <span>{if $PIPELINE_DETAIL.status == "1"}{vtranslate('LBL_ACTIVE',
                                            $MODULE_NAME)}{else}{vtranslate('LBL_INACTIVE', $MODULE_NAME)}{/if}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <hr />
                        <div>
                            <table>
                                <thead>
                                    <tr class="listViewHeaders">
                                        <th style="width:{if $PIPELINE_DETAIL.module == 'Potentials'}20%{else}30%{/if}"
                                            class="text-left">
                                            <span>{vtranslate('LBL_STAGE_NAME', $MODULE_NAME)}</span>
                                        </th>
                                        {if $PIPELINE_DETAIL.module == 'Potentials'}
                                        <th style="width:15%" class="text-center">
                                            <span>{vtranslate('LBL_SUCCESS_RATE', $MODULE_NAME)}</span>
                                        </th>
                                        <th style="width:15%" class="text-center">
                                            <span>{vtranslate('LBL_EXECUTION_TIME', $MODULE_NAME)}</span>
                                        </th>
                                        {/if}
                                        <th style="width:{if $PIPELINE_DETAIL.module == 'Potentials'}10%{else}20%{/if}"
                                            class="text-center">
                                            <span>{vtranslate('LBL_MANDATORY_STEP', $MODULE_NAME)}</span>
                                        </th>
                                        <th style="width:{if $PIPELINE_DETAIL.module == 'Potentials'}25%{else}30%{/if}"
                                            class="text-center">
                                            <span>{vtranslate('LBL_ALLOWED_TRANSITIONS', $MODULE_NAME)}</span>
                                        </th>
                                        <th style="width:{if $PIPELINE_DETAIL.module == 'Potentials'}20%{else}20%{/if}"
                                            class="text-center">
                                            <span>{vtranslate('LBL_ROLE_PERMISSIONS', $MODULE_NAME)}</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="pipeline-step-list" class="ui-sortable" style="width: auto;">
                                    {foreach from=$PIPELINE_DETAIL.stagesList item=stage name=stages}
                                    <tr class="tr-height">
                                        <td class="textOverflowEllipsis">
                                            <span>{$stage.name} (B{$stage.sequence})</span>
                                        </td>
                                        {if $PIPELINE_DETAIL.module == 'Potentials'}
                                        <td class="fieldValue">
                                            <div class="col-center">
                                                <span>{$stage.success_rate}%</span>
                                            </div>
                                        </td>
                                        <td class="fieldValue">
                                            <div class="col-center">
                                                <span>{$stage.execution_time.value} {$stage.execution_time.unit}</span>
                                            </div>
                                        </td>
                                        {/if}
                                        <td class="fieldValue">
                                            <div class="col-center">
                                                <input type="hidden" value="{$stage.is_mandatory}">
                                                <input class="inputElement"
                                                    style="width: 24px !important; height: 24px !important;"
                                                    data-fieldtype="checkbox" type="checkbox" disabled {if
                                                    $stage.is_mandatory}checked{/if}>
                                            </div>
                                        </td>
                                        <td class="fieldValue">
                                            <div class="col-center">
                                                {if $stage.next_stages|@count gt 0}
                                                {foreach from=$stage.next_stages item=next name=nexts}
                                                <span>{$next.name}{if not $smarty.foreach.nexts.last}, {/if}</span>
                                                {/foreach}
                                                {/if}
                                            </div>
                                        </td>
                                        <td class="fieldValue">
                                            <div class="col-center">
                                                {if $stage.permissions|@count gt 0}
                                                {foreach from=$stage.permissions item=perm name=perms}
                                                <span>{$perm.role_name}{if not
                                                    $smarty.foreach.perms.last}, {/if}</span>
                                                {/foreach}
                                                {else}
                                                <span>{vtranslate('LBL_ALL', $MODULE_NAME)}</span>
                                                {/if}
                                            </div>
                                        </td>
                                    </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab2">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/strip}