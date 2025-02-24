{strip}
<div class="modal-dialog modal-content">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_NOTIFICATION', $MODULE_NAME)}" }
    <form id="form-notification" class="form-horizontal notificationModal form-modal" method="POST">
        <div class="form-content">
            <table class="table table-borderless fieldBlockContainer form-content">
                <tbody>
                    <tr>
                        <td class="fieldLabel alignMiddle">
                            {vtranslate('LBL_TASK_NAME', $MODULE_NAME)}
                            &nbsp;
                            <span class="redColor">*</span>
                        </td>
                        <td class="fieldValue">
                            <input data-rule-required="true" type="text" class="inputElement referencefield-wrapper" name="name" value="">
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel alignMiddle">
                            {vtranslate('LBL_NOTIFICATION_TO', $MODULE_NAME)}
                            &nbsp;
                            <span class="redColor">*</span>
                        </td>
                        <td class="fieldValue w60">
                            <select name="userList" multiple class="inputElement select2" data-rule-required="true">
                                <option value="value1">Option 1</option>
                                <option value="value2">Option 2</option>
                                <option value="value3">Option 1</option>
                                <option value="value4">Option 2</option>
                                <option value="value5">Option 1</option>
                                <option value="value6">Option 2</option>
                                <option value="value7">Option 1</option>
                                <option value="value8">Option 2</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel alignMiddle">
                            {vtranslate('LBL_REPEAT', $MODULE_NAME)}
                        </td>
                        <td class="fieldValue">
                            <select name="repetition" class="inputElement select2">
                                <option value="">{vtranslate('LBL_CHOOSE_A_VALUE', $MODULE_NAME)}</option>
                                <option value="nonRepetition">{vtranslate('LBL_NON_REPETITION', $MODULE_NAME)}</option>
                                <option value="sixtyMinutes">{vtranslate('LBL_REPEAT_EVERY_SIXTY_MINUTES', $MODULE_NAME)}</option>
                                <option value="everyDay">{vtranslate('LBL_REPEAT_EVERY_DAY', $MODULE_NAME)}</option>
                                <option value="everyWeek">{vtranslate('LBL_REPEAT_EVERY_WEEK', $MODULE_NAME)}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel alignMiddle">
                            {vtranslate('LBL_CONTENT_NOTIFICATION', $MODULE_NAME)}
                            &nbsp;
                            <span class="redColor">*</span>
                        </td>
                        <td class="fieldValue">
                            <textarea rows="3" cols="12" class="inputElement textAreaElement col-lg-12" name="description" data-rule-required="true"></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}