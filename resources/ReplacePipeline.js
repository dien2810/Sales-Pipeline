/*
    ReplacePipeline.js
    Author: Minh Hoang
    Date: 2025-03-12
    Purpose: to handle logic for replace Pipeline in module
*/

var currentNameModule = "";
const urlParam = new URLSearchParams(window.location.search);
const moduleFromUrl = urlParam.get("module");
var pipelineId = "";
var pipelineIdReplace = "";
var pipelineReplaceMapping = "";

const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
const recordId = urlParams.get("record");

jQuery(function ($) {
  $("table.detailview-table tr").each(function () {
    var $td = $(this).find("td.fieldValue.pipelinename");
    if ($td.length && $td.find(".action.pull-right").length) {
      $td.find(".action.pull-right a.editAction").on("click", function (event) {
        event.stopPropagation();
        event.preventDefault();

        showReplacePipelineModal();
        registerReplacePipelineEvent();
        registerPipelineReplaceChangeEvent();
        registerSubmitModalReplace();
      });
    }
  });
});

/**
 * Shows the "Replace Pipeline" modal.
 */
function showReplacePipelineModal() {
  let self = this;
  app.helper.hideModal();

  app.helper.showProgress();
  let params = {
    module: moduleFromUrl,
    view: "ReplacePipeline",
    mode: "getReplacePipelineModal",
    moduleName: moduleFromUrl,
  };

  app.request.post({ data: params }).then((err, res) => {
    app.helper.hideProgress();

    if (err) {
      app.helper.showErrorNotification({ message: err.message });
      return;
    }

    app.helper.showModal(res, {
      preShowCb: function (modal) {
        let stageSelects = jQuery(".replacePipelineModal select.select2").not(
          "#pipeline-list-replace"
        );

        stageSelects.each(function () {
          let select = jQuery(this);
          select.empty();
          select.append('<option value="">Chọn một tùy chọn</option>');
          select.val("").trigger("change.select2");
        });

        registerPipelineStageReplaceMapping();
      },
    });
  });
}

/**
 * Maps and tracks stage replacements in the replace pipeline modal.
 */
function registerPipelineStageReplaceMapping() {
  let self = this;

  jQuery('.replacePipelineModal select[name^="swap_status"]')
    .not("#pipeline-list-replace")
    .each(function () {
      let nameAttr = jQuery(this).attr("name");
      let idCurrently = nameAttr.substring(
        nameAttr.indexOf("[") + 1,
        nameAttr.indexOf("]")
      );

      pipelineReplaceMapping = idCurrently;

      console.log("Bước pipeline replace", self.pipelineReplaceMapping);
    });

  jQuery('.replacePipelineModal select[name^="swap_status"]')
    .not("#pipeline-list-replace")
    .on("change", function () {
      let newVal = jQuery(this).val();

      self.pipelineReplaceMapping = newVal;

      console.log(
        "Updated pipelineReplaceMapping:",
        self.pipelineReplaceMapping
      );
    });
}

/**
 * Updates stage options when the pipeline selection changes.
 */
function registerPipelineReplaceChangeEvent() {
  jQuery("#pipeline-list-replace").on("change", function () {
    let pipelineId = jQuery(this).val();

    pipelineReplaceMapping = "";

    if (!pipelineId) {
      jQuery('select[name="swap_status"]').not("#pipeline-list-replace");
      return;
    } else {
      jQuery('select[name="swap_status"]').not("#pipeline-list-replace");
    }
    if (!pipelineId) {
      return;
    }

    let params = {
      module: "PipelineConfig",
      parent: "Settings",
      action: "SaveConfig",
      mode: "getStagePipeline",
      pipelineId: pipelineId,
    };

    app.request.post({ data: params }).then((err, response) => {
      if (err) {
        app.helper.showErrorNotification({ message: err.message });
        return;
      }

      if (response.data) {
        let options = `<option value="">${app.vtranslate(
          "JS_SELECT_REPLACEMENT_PIPELINE"
        )}</option>`;

        response.data.forEach(function (stage) {
          options +=
            '<option value="' + stage.stageid + '">' + stage.name + "</option>";
        });

        jQuery('select[name="swap_status"]')
          .not("#pipeline-list-replace")
          .each(function () {
            jQuery(this).html(options);
          });

        jQuery('select[name="swap_status"]')
          .not("#pipeline-list-replace")
          .select2();
      }
    });
    console.log("Pipeline replace:", self.pipelineReplaceMapping);
  });
}

/**
 * Registers an event listener for changes to the "Replace Pipeline" dropdown.
 *
 * When the user selects a new pipeline, this function fetches the corresponding stages from the server and updates the stage selection dropdown accordingly.
 */
function registerReplacePipelineEvent(pipelineId) {
  let self = this;

  jQuery(document).on("change", "#pipeline-list-replace", function (e) {
    let pipelineId = jQuery(this).val();
    let stageSelects = jQuery(".replacePipelineModal select.select2").not(
      "#pipeline-list-replace"
    );

    if (!pipelineId) {
      stageSelects.each(function () {
        let select = jQuery(this);
        select.empty();
        select.append('<option value="">Chọn một tùy chọn</option>');
        select.val("").trigger("change.select2");
      });
      return;
    }

    self.pipelineIdReplace = pipelineId;
    app.helper.showProgress();
    let params = {
      module: "PipelineConfig",
      parent: "Settings",
      action: "SaveConfig",
      mode: "getStagePipeline",
      pipelineId: pipelineId,
    };

    app.request.post({ data: params }).then((err, response) => {
      if (err) {
        app.helper.showErrorNotification({
          message: err.message,
        });
        return;
      }

      let stageSelects = jQuery(".replacePipelineModal select.select2").not(
        "#pipeline-list-replace"
      );

      stageSelects.each(function () {
        let select = jQuery(this);
        if (select.hasClass("select2-hidden-accessible")) {
          select.select2("destroy");
        }
        select.empty();
        select.append('<option value="">Chọn một tùy chọn</option>');
      });

      if (response.data && response.data.length > 0) {
        stageSelects.each(function () {
          let select = jQuery(this);
          response.data.forEach(function (stage) {
            select.append(
              `<option value="${stage.stageid}">${stage.name}</option>`
            );
          });
          select.select2({
            width: "100%",
            placeholder: "Chọn một tùy chọn",
          });
          select.val("").trigger("change.select2");
        });
      }

      app.helper.hideProgress();
    });
  });
}

/**
 * Registers an event listener for submitting the "Replace Pipeline" modal form.
 *
 * This function intercepts the form submission, prevents the default behavior, and sends an request to update the pipeline and stage for the selected record.
 */
function registerSubmitModalReplace() {
  let self = this;

  jQuery(document).on("submit", "form#replacePipelineModal", function (e) {
    e.preventDefault();
    let pipelineReplaceValue = jQuery("#pipeline-list-replace").val();
    let stageReplaceValue = jQuery('select[name^="swap_status"]')
      .not("#pipeline-list-replace")
      .val();

    const params = {
      module: "PipelineConfig",
      parent: "Settings",
      action: "SaveConfig",
      mode: "replacePipelineAndStageInRecord",
      recordId: recordId,
      pipelineIdReplace: self.pipelineIdReplace,
      stageIdReplace: self.pipelineReplaceMapping,
      moduleName: moduleFromUrl,
    };

    app.request.post({ data: params }).then((err, response) => {
      if (err) {
        app.helper.showErrorNotification({ message: err.message });
        return;
      }

      if (response.success) {
        app.helper.showSuccessNotification({
          message: app.vtranslate("JS_PIPELINE_REPLACE_SUCCESS"),
        });

        location.reload();
      } else {
        app.helper.showErrorNotification({ message: response.message });
      }

      app.helper.hideModal();
      app.helper.hideProgress();
    });

    app.helper.hideModal();
    return false;
  });
}
