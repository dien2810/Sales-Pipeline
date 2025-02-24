{strip}
{if !empty($PIPELINE_LIST)}
{foreach key=PIPELINE_ID item=PIPELINE from=$PIPELINE_LIST}
<tr class="listViewEntries" data-id="{$PIPELINE.pipelineid}">
    <td>
        <span class="fieldValue">
            <span class="fieldValue">
                <a href="index.php?parent=Settings&module=PipelineConfig&view=Detail&record={$PIPELINE.pipelineid}&block=9&fieldid=67"
                    class="pipeline-name textOverflowEllipsis">
                    {$PIPELINE.name}
                </a>
            </span>
        </span>
    </td>
    <td>
        <span class="fieldValue">
            <span class="value textOverflowEllipsis pipeline-step">
                {$PIPELINE.stage}
            </span>
        </span>
    </td>zzz
    <td>
        <span class="fieldValue">
            <div class="toggle-switch {if $PIPELINE.status}active{/if} pipeline-status"></div>
        </span>
    </td>
    <td>
        <span class="fieldValue">
            <span class="value textOverflowEllipsis pipeline-permission">
                {$PIPELINE.permissions|default:"Tất cả"}
            </span>
        </span>
    </td>
    <td>
        <span class="fieldValue">
            <span class="value textOverflowEllipsis pipeline-permission">
                <!-- {$PIPELINE.module} -->
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
                <!-- {$PIPELINE.created_by} -->
                Administrator
            </span>
        </span>
    </td>
    <td>
        <span class="fieldValue">
            <div class="action-buttons">
                <a href="index.php?parent=Settings&module=PipelineConfig&view=EditPipeline&record={$PIPELINE.pipelineid}&block=9&fieldid=67"
                    class="action-icon">
                    <i class="far fa-pen icon" title="Sửa"></i>
                </a>
                <span><i class="far fa-clone icon" onclick="app.controller().clonePipeline('{$PIPELINE_ID}')"
                        title="Nhân bản"></i></span>
                <button type="button" class="btn btn-outline-danger"
                    onclick="app.controller().showDeletePipelineModal('{$PIPELINE.pipelineid}', '{$PIPELINE.module}')"
                    title="Xóa">
                    <i class="far fa-trash-alt icon"></i>
                </button>

                <!-- <span><i id="deletePipeline" class="far fa-trash-alt icon"
                        onclick="app.controller().deletePipeline('{$PIPELINE_ID}')" title="Xóa"></i></span> -->
            </div>
        </span>
    </td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="7" class="text-center">
        <div>Không tìm thấy Pipeline nào {$TEST_PIPELINE}</div>
    </td>
</tr>
{/if}
{/strip}