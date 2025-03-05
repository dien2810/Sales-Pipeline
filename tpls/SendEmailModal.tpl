{* Added by Minh Hoang on 2021-02-10 *}

{strip}
<div class="modal-dialog modal-content modal-width-1300">
    {include file="ModalHeader.tpl"|vtemplate_path:'Vtiger' TITLE="{vtranslate('LBL_SEND_EMAIL', $QUALIFIED_MODULE)}" }
    <form id="form-send-email" class="form-horizontal sendEmailModal form-modal" method="POST">
        <!-- <input type="hidden" name="module" value="{$MODULE}" /> -->
        <div class="taskTypeUi">
            <div id="VtEmailTaskContainer" class="form-content" style="padding-left: 20px; padding-right: 20px;">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="row form-group">
                            <div class="col-sm-6 col-xs-6">
                                <div class="row">
                                    <div class="col-sm-3 col-xs-3">{vtranslate('LBL_TASK_NAME',$QUALIFIED_MODULE)}<span class="redColor">*</span></div>
                                    <div class="col-sm-9 col-xs-9"><input name="titleEmail" class="inputElement" data-rule-required="true" type="text" /></div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group" >
                            <div class="col-sm-6 col-xs-6">
                                <div class="row">
                                    <div class="col-sm-3 col-xs-3">{vtranslate('LBL_FROM', $QUALIFIED_MODULE)}</div>
                                    <div class="col-sm-9 col-xs-9">
                                        <input name="fromEmail" class=" fields inputElement" type="text" value="{$TASK_OBJECT->fromEmail}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5 col-xs-5">
                                <select id="fromEmailOption" style="min-width: 250px" class="select2" data-placeholder={vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}>
                                    <option></option>
                                    {$FROM_EMAIL_FIELD_OPTION}
                                </select>
                            </div>
                        </div>
        
                        <div class="row form-group">
                            <div class="col-sm-6 col-xs-6">
                                <div class="row">
                                    <div class="col-sm-3 col-xs-3">{vtranslate('Reply To',$QUALIFIED_MODULE)}</div>
                                    <div class="col-sm-9 col-xs-9">
                                        <input name="replyTo" class="fields inputElement" type="text" value="{$TASK_OBJECT->replyTo}"/>
                                    </div>
                                </div>
                            </div>
                            <span class="col-sm-5 col-xs-5">
                                <select style="min-width: 250px" class="task-fields select2 overwriteSelection" data-placeholder={vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}>
                                    <option></option>
                                    {$EMAIL_FIELD_OPTION}
                                </select>
                            </span>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6 col-xs-6">
                                <div class="row">
                                    <span class="col-sm-3 col-xs-3">{vtranslate('LBL_TO',$QUALIFIED_MODULE)}<span class="redColor">*</span></span>
                                    <div class="col-sm-9 col-xs-9">
                                        <input data-rule-required="true" name="recepient" class="fields inputElement" type="text" value="{$TASK_OBJECT->recepient}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5 col-xs-5">
                                <select style="min-width: 250px" class="task-fields select2" data-placeholder={vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}>
                                    <option></option>
                                    {$EMAIL_FIELD_OPTION}
                                </select>
                            </div>
                        </div>
                        <div class="row form-group {if empty($TASK_OBJECT->emailcc)}hide {/if}" id="ccContainer">
                            <div class="col-sm-6 col-xs-6">
                                <div class="row">
                                    <div class="col-sm-3 col-xs-3">{vtranslate('LBL_CC',$QUALIFIED_MODULE)}</div>
                                    <div class="col-sm-9 col-xs-9">
                                        <input class="fields inputElement" type="text" name="emailcc" value="{$TASK_OBJECT->emailcc}" />
                                    </div>
                                </div>
                            </div>
                            <span class="col-sm-5 col-xs-5">
                                <select class="task-fields select2" data-placeholder='{vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}' style="min-width: 250px">
                                    <option></option>
                                    {$EMAIL_FIELD_OPTION}
                                </select>
                            </span>
                        </div>
                        <div class="row form-group {if empty($TASK_OBJECT->emailbcc)}hide {/if}" id="bccContainer">
                            <div class="col-sm-6 col-xs-6">
                                <div class="row">
                                    <div class="col-sm-3 col-xs-3">{vtranslate('LBL_BCC',$QUALIFIED_MODULE)}</div>
                                    <div class="col-sm-9 col-xs-9">
                                        <input class="fields inputElement" type="text" name="emailbcc" value="{$TASK_OBJECT->emailbcc}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5 col-xs-5">
                                <select class="task-fields select2" data-placeholder='{vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}' style="min-width: 250px">
                                    <option></option>
                                    {$EMAIL_FIELD_OPTION}
                                </select>
                            </div>
                        </div>
                        <div class="row form-group {if (!empty($TASK_OBJECT->emailcc)) and (!empty($TASK_OBJECT->emailbcc))} hide {/if}">
                            <div class="col-sm-6 col-xs-6">
                                <div class="row">
                                    <div class="col-sm-3 col-xs-3"></div>
                                    <div class="col-sm-9 col-xs-9">
                                        <a class="cursorPointer mr-3 {if (!empty($TASK_OBJECT->emailcc))}hide{/if}" id="ccLink">{vtranslate('LBL_ADD_CC',$QUALIFIED_MODULE)}</a>
                                        <a class="cursorPointer {if (!empty($TASK_OBJECT->emailbcc))}hide{/if}" id="bccLink">{vtranslate('LBL_ADD_BCC',$QUALIFIED_MODULE)}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6 col-xs-6">
                                <div class="row">
                                    <div class="col-sm-3 col-xs-3">{vtranslate('LBL_SUBJECT',$QUALIFIED_MODULE)}<span class="redColor">*</span></div>
                                    <div class="col-sm-9 col-xs-9">
                                        <input data-rule-required="true" name="subject" class="fields inputElement" type="text" name="subject" value="{htmlentities($TASK_OBJECT->subject)}" id="subject" spellcheck="true"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5 col-xs-5">
                                <select style="min-width: 250px" class="task-fields select2" data-placeholder={vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}>
                                    <option></option>
                                    {$ALL_FIELD_OPTIONS}
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6 col-xs-6">
                                <div class="row">
                                    <div style="margin-top: 7px" class="col-sm-3 col-xs-3">{vtranslate('LBL_ADD_FIELD',$QUALIFIED_MODULE)}</div>
                                    <div class="col-sm-9 col-xs-9">
                                        <select style="min-width: 455px" id="task-fieldnames" class="select2" data-placeholder={vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}>
                                            <option></option>
                                            {$ALL_FIELD_OPTIONS}
                                        </select>
                                    </div>	
                                </div>
                            </div>
                            <div class="col-sm-5 col-xs-5">
                                <div class="row">
                                    <div style="margin-top: 7px" class="col-sm-5 col-xs-5">{vtranslate('LBL_GENERAL_FIELDS',$QUALIFIED_MODULE)}</div>&nbsp;&nbsp;
                                    <div class="col-sm-6 col-xs-6">
                                        <select style="min-width: 255px" id="task_timefields" class="select2" data-placeholder={vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}>
                                            <option></option>
                                            {foreach from=$META_VARIABLES item=META_VARIABLE_KEY key=META_VARIABLE_VALUE}
                                                <option value="{if strpos(strtolower($META_VARIABLE_VALUE), 'url') === false}${/if}{$META_VARIABLE_KEY}">{vtranslate($META_VARIABLE_VALUE,$QUALIFIED_MODULE)}</option>
                                            {/foreach}	
                                        </select>
                                    </div>	
                                </div>
                            </div>
                        </div>
                        <div class="row from-group">
                            {if $EMAIL_TEMPLATES}
                                <div class="col-sm-6 col-xs-6">
                                    <div class="row">
                                        <div class="col-sm-3 col-xs-3">{vtranslate('LBL_EMAIL_TEMPLATES','EmailTemplates')}</div>
                                        <div class="col-sm-9 col-xs-9">
                                            <select style="min-width: 250px" id="task-emailtemplates" class="select2" data-placeholder={vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}>
                                                <option></option>
                                                {foreach from=$EMAIL_TEMPLATES item=EMAIL_TEMPLATE}
                                                    {if !$EMAIL_TEMPLATE->isDeleted()}
                                                        <option value="{$EMAIL_TEMPLATE->get('body')}">{vtranslate($EMAIL_TEMPLATE->get('templatename'),$QUALIFIED_MODULE)}</option>
                                                    {/if}
                                                {/foreach}	
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            {/if}
                        </div>
        
                        <div class="row from-group">
                            <div class="col-md-4 col-lg-4">{vtranslate('LBL_SAFE_CONTENT', $QUALIFIED_MODULE)}</div>
                            <div class="col-md-8 col-lg-8">
                                <input id="safe_content" type="checkbox" class="inputElement" name="safe_content" {if {$TASK_OBJECT->safe_content} == 'on'}checked{/if} />
                                &nbsp;&nbsp;<span style="color:red">{vtranslate('LBL_SAFE_CONTENT_HINT', $QUALIFIED_MODULE)}</span>
                            </div>
                        </div>
                        <br />
        
                        <div class="row form-group">
                            <div class="col-sm-12 col-xs-12">
                                <textarea id="content" name="content">{htmlentities($TASK_OBJECT->content)}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {include file="ModalFooter.tpl"|@vtemplate_path:'Vtiger'}
    </form>
</div>
{/strip}