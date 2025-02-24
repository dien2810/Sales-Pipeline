{strip}
<link rel="stylesheet" href="{vresource_url('modules/Settings/PipelineConfig/resources/Config.css')}">
</link>
<script src="{vresource_url('resources/CustomColorPicker.js')}"></script>
<form autocomplete="off" id="pipeline" name="pipeline">
    <div class="listPipeline" id="listPipeline">
        <div class="header">
            <h5 class="fieldBlockHeader">Thiết lập pipeline cho các module</h5>
            <button class="btn btn-default configButton" type="button">
                <i class="far fa-cog"></i>&nbsp;&nbsp;
                Cấu hình pipeline module
            </button>
        </div>
        <div style="margin-bottom:20px">
            <div class="row form-group">
                <label class="col-sm-2 textAlignLeft" style="padding-top: 7px; margin-left: 20px;">Chọn Module</label>
                <div class="fieldValue col-sm-3 col-xs-3">
                    <select class="select2 inputElement" id="pickListModules" name="pickListModules">
                        <option value="">Tất cả</option>
                        {foreach item=PICKLIST_MODULE from=$PICKLIST_MODULES}
                        <option {if $SELECTED_MODULE_NAME eq $PICKLIST_MODULE->get('name')} selected="" {/if}
                            value="{$PICKLIST_MODULE->get('name')}">{vtranslate($PICKLIST_MODULE->get('name'),$PICKLIST_MODULE->get('name'))}
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
                <input class="searchWorkflows" type="text" value="" placeholder="Tìm kiếm">
                <span aria-hidden="true" class="far fa-search"></span>
            </div>
            <div class="pagination">
                <div class="listViewActions">
                    <div class="btn-group pull-right">
                        <button type="button" id="PreviousPageButton" class="btn btn-default" disabled=""><i
                                class="far fa-chevron-left"></i></button>
                        <button type="button" id="PageJump" data-toggle="dropdown" class="btn btn-default">
                            <i class="far fa-ellipsis-h icon" title="Nhảy tới trang"></i>
                        </button>
                        <ul class="listViewBasicAction dropdown-menu" id="PageJumpDropDown">
                            <li>
                                <div class="listview-pagenum">
                                    <span>Trang</span>&nbsp;
                                    <strong><span>1</span></strong>&nbsp;
                                    <span>của</span>&nbsp;
                                    <strong><span id="totalPageCount"></span></strong>
                                </div>
                                <div class="listview-pagejump">
                                    <input type="text" id="pageToJump" class="listViewPagingInput text-center">&nbsp;
                                    <button type="button" id="pageToJumpSubmit"
                                        class="btn btn-success listViewPagingInputSubmit text-center">Chuyển</button>
                                </div>
                            </li>
                        </ul>
                        <button type="button" id="NextPageButton" class="btn btn-default"><i
                                class="far fa-chevron-right"></i></button>
                    </div>
                    <!-- Phân trang -->
                    <span class="pagingInfo pull-right">
                        <span>1 đến 20 của</span>&nbsp;
                        <span class="totalRecords cursorPointer"><i class="far fa-question showTotalRecords"
                                title="Click để xem tổng số bản ghi"></i></span>&nbsp;&nbsp;
                    </span>
                </div>
            </div>
        </div>
        <div class="content">
            <!-- Hiển thị danh sách pipeline -->
            <table class=" tableListPipeline table fieldBlockContainer" id="pipeline-table">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Số bước</th>
                        <th>Trạng thái</th>
                        <th>Phân quyền</th>
                        <th>Module</th>
                        <th>Mô tả</th>
                        <th>Được tạo bởi</th>
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
            <span>Thêm pipeline</span>
        </a>
    </div>
</form>
{/strip}