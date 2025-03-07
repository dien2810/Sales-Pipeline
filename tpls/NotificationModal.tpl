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
                            <input data-rule-required="true" type="text" class="inputElement referencefield-wrapper"
                                name="name" value="">
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel alignMiddle">
                            {vtranslate('LBL_NOTIFICATION_TO', $MODULE_NAME)}
                            &nbsp;
                            <span class="redColor">*</span>
                        </td>
                        <td class="fieldValue w60">
                            <input type="text" autocomplete="off" class="inputElement select2" style="width: 100%"
                                data-rule-required="true" data-rule-main-owner="true" data-fieldtype="owner"
                                data-fieldname="assigned_user_id" data-name="assigned_user_id" name="userList" {if
                                $FOR_EVENT} data-assignable-users-only="true" data-user-only="true"
                                data-single-selection="true" {/if} {if $FIELD_VALUE}
                                data-selected-tags='{ZEND_JSON::encode(Vtiger_Owner_UIType::getCurrentOwners($FIELD_VALUE))}'
                                {/if} />
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
                                <option value="sixtyMinutes">{vtranslate('LBL_REPEAT_EVERY_SIXTY_MINUTES',
                                    $MODULE_NAME)}</option>
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
                            <textarea rows="3" cols="12" class="inputElement textAreaElement col-lg-12"
                                name="description" data-rule-required="true"></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}