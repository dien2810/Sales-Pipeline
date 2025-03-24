{* Added by The Vi on 2025-03-05 *}
{strip}
{if !empty($PIPELINE_LIST)}
{foreach key=PIPELINE_ID item=PIPELINE from=$PIPELINE_LIST}
<tr class="listViewEntries" data-id="{$PIPELINE.pipelineid}">
    <td>
        <span class="fieldValue">
            <a href="index.php?parent=Settings&module=PipelineConfig&view=Detail&record={$PIPELINE.pipelineid}&block=9&fieldid=67"
                class="pipeline-name textOverflowEllipsis">
                {$PIPELINE.name}
            </a>
        </span>
    </td>
    <td>
        <span class="fieldValue">
            <span class="value textOverflowEllipsis pipeline-step">
                {$PIPELINE.stage}
            </span>
        </span>
    </td>
    <td>
        <span class="fieldValue">
            <div class="toggle-switch {if $PIPELINE.status}active{/if} pipeline-status"></div>
        </span>
    </td>
    <td class="listViewEntryValue" data-name="assigned_user_id" data-field-type="owner">
        <span class="fieldValue">
            <span class="value">
                <span class="owners custom-popover-wrapper">
                    {assign var="ROLE_COUNT" value=$PIPELINE.permissions|count}
                    {assign var="ROLES" value=[]}
                    {foreach from=$PIPELINE.permissions item=roleId}
                    {assign var="roleDetail" value=Settings_Roles_Record_Model::getInstanceById($roleId)}
                    {$ROLES[] = $roleDetail->getData()}
                    {/foreach}
                    {if $ROLE_COUNT == 0}
                    <a class="no-owner" href="javascript: void(0)"></a>
                    {elseif $ROLE_COUNT == 1}
                    <span class="stand-owner">
                        <a href="index.php?module=Roles&parent=Settings&view=Detail&record={$ROLES[0]['roleid']}">
                            {$ROLES[0]['rolename']}
                        </a>
                    </span>
                    {else}
                    <a class="stand-owner-plus custom-popover"
                        title="{vtranslate('LBL_ROLE_PERMISSIONS', $MODULE_NAME)}" data-title="{vtranslate('Roles')}">
                        <span class="stand-owner-plus-text">{$ROLES[0]['rolename']}</span>
                        <span class="stand-owner-plus-icon"> +{$ROLE_COUNT - 1}</span>
                    </a>
                    <div class="custom-popover-content" style="display: none">

                        <ul class="owners-detail_owners">
                            {foreach from=$ROLES item=role}
                            <li class="owners-detail_owner">
                                <a target="_blank"
                                    href="index.php?module=Roles&parent=Settings&view=Edit&record={$role['roleid']}">
                                    {$role['rolename']}
                                </a>
                            </li>
                            {/foreach}
                        </ul>
                    </div>
                    {/if}
                </span>
                <!-- <span class="owners custom-popover-wrapper"><a class="stand-owner-plus custom-popover" title=""
                        data-title="Giao cho" data-original-title="Giao cho"><span
                            class="stand-owner-plus-text">Administrator</span><span class="stand-owner-plus-icon">
                            +1</span></a>
                    <div class="custom-popover-content" style="display: none">
                        <p class="owners-detail_title">Người dùng:</p>
                        <ul class="owners-detail_owners">
                            <li class="owners-detail_owner"><a target="_blank"
                                    href="index.php?module=Users&amp;parent=Settings&amp;view=Detail&amp;record=1">Administrator</a>
                            </li>
                            <li class="owners-detail_owner"><a target="_blank"
                                    href="index.php?module=Users&amp;parent=Settings&amp;view=Detail&amp;record=23">System
                                    Admin</a></li>
                        </ul>
                    </div>
                </span> -->
            </span>
        </span>
    </td>
    <td>
        <span class="fieldValue">
            <span class="value textOverflowEllipsis pipeline-permission">
                {vtranslate($PIPELINE.module, $MODULE_NAME)}
            </span>
        </span>
    </td>
    <td>
        <span class="fieldValue">
            <span class="value pipeline-description">
                {$PIPELINE.description}
            </span>
        </span>
    </td>
    <td>
        <span class="fieldValue">
            <span class="value textOverflowEllipsis pipeline-creator">
                {$PIPELINE.create_by}
            </span>
        </span>
    </td>
    <td>
        <span class="fieldValue">
            <div class="action-buttons">
                <a href="index.php?parent=Settings&module=PipelineConfig&view=EditPipeline&record={$PIPELINE.pipelineid}&block=9&fieldid=67"
                    class="action-icon">
                    <i class="far fa-pen icon" title="{vtranslate('LBL_EDIT', $MODULE_NAME)}"></i>
                </a>
                <span>
                    <i class="far fa-clone icon" onclick="app.controller().clonePipeline(this)"
                        title="{vtranslate('LBL_CLONE', $MODULE_NAME)}"></i>
                </span>
                <button type="button" class="btn btn-outline-danger"
                    onclick="app.controller().showDeletePipelineModal('{$PIPELINE.pipelineid}', '{$PIPELINE.module}')"
                    title="{vtranslate('LBL_DELETE', $MODULE_NAME)}">
                    <i class="far fa-trash-alt icon"></i>
                </button>
            </div>
        </span>
    </td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="8" class="text-center">
        <div>{vtranslate('LBL_NO_PIPELINE_FOUND', $MODULE_NAME)}</div>
    </td>
</tr>
{/if}
{/strip}