{strip}

<link rel="stylesheet" href="{vresource_url('modules/Settings/PipelineConfig/resources/EditPipeline.css')}">
</link>

<div>
    <!-- Bắt đầu nội dung hiện khi stageList khác rỗng -->
    <table id="stagesTable" style="margin: 15px;">
        <thead>
            <tr class="listViewHeaders">
                <th id="stage-name-column" style="width:30%" class="text-left">
                    <span>Tên bước</span>
                </th>
                <th id="success-rate-column" style="width:10%" class="text-center">
                    <span>Tỉ lệ thành công</span>
                </th>
                <th id="execution-time-column" style="width:10%" class="text-center">
                    <span>Thời gian thực hiện</span>
                </th>
                <th id="mandatory-column" style="width:8%" class="text-center">
                    <span>Bước bắt buộc</span>
                </th>
                <th id="next-stages-column" style="width:27%" class="text-center">
                    <span>Bước chuyển đến cho phép</span>
                </th>
                <th id="permissions-column" style="width:20%" class="text-center">
                    <span>Phân quyền</span>
                </th>
                <th id="actions-column" style="width:5%" class="text-center">
                    <span></span>
                </th>
            </tr>
        </thead>
        <tbody id="pipeline-stage-list" class="ui-sortable" style="width: auto;">

            <!-- Item stage -->
        </tbody>

    </table>
    <div style="text-align: center; margin-top: 10px; margin-bottom: 60px;">
        <button type="button" class="btn btn-default module-buttons btn-add-stage" id="addStepButton">
            <i class="far fa-plus"></i>&nbsp;&nbsp;Thêm bước
        </button>
    </div>
    <!-- Kết thúc nội dung hiện khi stageList khác rỗng -->
</div>
{/strip}