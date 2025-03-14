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
        <td>
            <span class="fieldValue">
                <span class="value textOverflowEllipsis pipeline-permission">
                    {if !empty($PIPELINE.permissions)}
                    {$PIPELINE.permissions}
                    {else}
                    {vtranslate('LBL_ALL', $MODULE_NAME)}
                    {/if}
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