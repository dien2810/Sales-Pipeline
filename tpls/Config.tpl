{* Added by The Vi on 2025-03-05 *}
{strip}
<link rel="stylesheet" href="{vresource_url('modules/Settings/PipelineConfig/resources/Config.css')}">
</link>
<script src="{vresource_url('resources/CustomColorPicker.js')}"></script>
<form autocomplete="off" id="pipeline" name="pipeline">
    <div class="listPipeline" id="listPipeline">
        <div class="header">
            <h5 class="fieldBlockHeader">{vtranslate('LBL_PIPELINE_CONFIG', $MODULE_NAME)}</h5>
            <button class="btn btn-default configButton" type="button">
                <i class="far fa-cog"></i>&nbsp;&nbsp;{vtranslate('LBL_CONFIG_PIPELINE_MODULE', $MODULE_NAME)}
            </button>
        </div>
        <div style="margin-bottom:20px">
            <div class="row form-group">
                <label class="col-sm-2 textAlignLeft"
                    style="padding-top: 7px; margin-left: 20px;">{vtranslate('LBL_CHOOSE_MODULE', $MODULE_NAME)}</label>
                <div class="fieldValue col-sm-3 col-xs-3">
                    <select class="select2 inputElement" id="pickListModules" name="pickListModules">
                        <option value="">{vtranslate('LBL_ALL', $MODULE_NAME)}</option>
                        {foreach item=PICKLIST_MODULE from=$PICKLIST_MODULES}
                        <option {if $SELECTED_MODULE_NAME eq $PICKLIST_MODULE->get('name')} selected="" {/if}
                            value="{$PICKLIST_MODULE->get('name')}">
                            {vtranslate($PICKLIST_MODULE->get('name'), $PICKLIST_MODULE->get('name'))}
                        </option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>
        <hr />
        <!-- Tìm kiếm pipeline -->
        <div class="search-bar">
            <div class="search-link hidden-xs searchPipeline" style="margin-top: 0px;">
                <input class="searchWorkflows" type="text" value=""
                    placeholder="{vtranslate('LBL_SEARCH', $MODULE_NAME)}">
                <span aria-hidden="true" class="far fa-search"></span>
            </div>
            <div class="pagination">
                <div class="listViewActions">
                    <div class="btn-group pull-right">
                        <button type="button" id="PreviousPageButton" class="btn btn-default" disabled=""><i
                                class="far fa-chevron-left"></i></button>
                        <button type="button" id="PageJump" data-toggle="dropdown" class="btn btn-default">
                            <i class="far fa-ellipsis-h icon"
                                title="{vtranslate('LBL_JUMP_TO_PAGE', $MODULE_NAME)}"></i>
                        </button>
                        <ul class="listViewBasicAction dropdown-menu" id="PageJumpDropDown">
                            <li>
                                <div class="listview-pagenum">
                                    <span>{vtranslate('LBL_PAGE', $MODULE_NAME)}</span>&nbsp;
                                    <strong><span>1</span></strong>&nbsp;
                                    <span>{vtranslate('LBL_OF', $MODULE_NAME)}</span>&nbsp;
                                    <strong><span id="totalPageCount"></span></strong>
                                </div>
                                <div class="listview-pagejump">
                                    <input type="text" id="pageToJump" class="listViewPagingInput text-center">&nbsp;
                                    <button type="button" id="pageToJumpSubmit"
                                        class="btn btn-success listViewPagingInputSubmit text-center">{vtranslate('LBL_GO',
                                        $MODULE_NAME)}</button>
                                </div>
                            </li>
                        </ul>
                        <button type="button" id="NextPageButton" class="btn btn-default"><i
                                class="far fa-chevron-right"></i></button>
                    </div>
                    <!-- Phân trang -->
                    <span class="pagingInfo pull-right">
                        <span>{vtranslate('LBL_RECORDS_RANGE', $MODULE_NAME)}</span>&nbsp;
                        <span class="totalRecords cursorPointer"><i class="far fa-question showTotalRecords"
                                title="{vtranslate('LBL_VIEW_TOTAL_RECORDS', $MODULE_NAME)}"></i></span>&nbsp;&nbsp;
                    </span>
                </div>
            </div>
        </div>
        <div class="content">
            <!-- Hiển thị danh sách pipeline -->
            <table class=" tableListPipeline table fieldBlockContainer" id="pipeline-table">
                <thead>
                    <tr>
                        <th>{vtranslate('LBL_NAME', $MODULE_NAME)}</th>
                        <th>{vtranslate('LBL_STEPS', $MODULE_NAME)}</th>
                        <th>{vtranslate('LBL_STATUS', $MODULE_NAME)}</th>
                        <th>{vtranslate('LBL_PERMISSION', $MODULE_NAME)}</th>
                        <th>{vtranslate('LBL_MODULE', $MODULE_NAME)}</th>
                        <th>{vtranslate('LBL_DESCRIPTION', $MODULE_NAME)}</th>
                        <th>{vtranslate('LBL_CREATED_BY', $MODULE_NAME)}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="pipeline-list">

                </tbody>
            </table>
        </div>
        <!-- Thêm mới Pipeline -->
        <a href="index.php?parent=Settings&module=PipelineConfig&view=EditPipeline&block=9&fieldid=67"
            class="btn addButton btn-default module-buttons addPipelineBtn">
            <i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;
            <span>{vtranslate('LBL_ADD_PIPELINE', $MODULE_NAME)}</span>
        </a>
    </div>
</form>
{/strip}