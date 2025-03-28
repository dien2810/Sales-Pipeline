/*
    File: Config.js
    Author: The Vi
    Date: 22/1/2025
    Purpose: Handle events for Pipeline configuration interface
*/
CustomView_BaseController_Js(
  "Settings_PipelineConfig_Config_Js",
  {},
  {
    currentNameModule: "",
    currentSeachText: "",
    pipelineId: "",
    pipelineIdReplace: "",
    pipelineReplaceMapping: [],
    modulePipelineDelete: "",
    registerEvents: function () {
      // $(".custom-popover").customPopover();
      this._super();
      this.registerEventFormInit();
    },
    registerEventFormInit: function () {
      let self = this;
      let form = this.getForm();

      // Wait for DOM to be fully loaded before initializing popovers
      $(document).ready(function () {
        // Initialize popovers for existing elements
        self.initializePopovers();
        // Set up observer for dynamically added elements
        self.observeDOM();
      });
      self.loadPipelineList(form, "");
      self.registerModuleChangeEvent();
      self.registerSearchInputEvent();
      self.registerToggleStatusEvent();
      self.registerDeletePipelineEvent();
      self.registerPipelineReplaceChangeEvent();
      self.registerSubmitModalDelete();
    },
    initializePopovers: function () {
      $(".custom-popover").each(function () {
        if ($(this).closest(".custom-popover-wrapper").length > 0) {
          $(this).customPopover();
        }
      });
    },

    observeDOM: function () {
      const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
          const newNodes = mutation.addedNodes;
          newNodes.forEach((node) => {
            if (node.nodeType === 1) {
              const popovers = $(node).find(".custom-popover");
              if (popovers.length > 0) {
                this.initializePopovers();
              }
            }
          });
        });
      });

      observer.observe(document.body, {
        childList: true,
        subtree: true,
      });
    },
    registerSubmitModalDelete: function () {
      let self = this;
      jQuery(document).on("submit", "form#deletePipelineModal", function (e) {
        e.preventDefault();
        let form = jQuery(this);
        let valid = true;
        let errorMessages = [];
        let pipelineReplaceValue = jQuery("#pipeline-list-replace").val();
        if (!pipelineReplaceValue) {
          errorMessages.push(app.vtranslate("JS_CHOOSE_VALUE"));
          valid = false;
        }
        jQuery('select[name^="swap_status"]')
          .not("#pipeline-list-replace")
          .each(function () {
            if (!jQuery(this).val()) {
              let currentStage = jQuery(this)
                .closest(".form-group")
                .find(".stage-current")
                .text()
                .trim();
              errorMessages.push(
                app
                  .vtranslate("JS_ERROR_SELECT_REPLACEMENT_STAGE")
                  .replace("{stage}", currentStage)
              );
              valid = false;
            }
          });
        if (!valid) {
          app.helper.showErrorNotification({
            message: errorMessages.join("<br>"),
          });
          return false;
        }
        //Begin The Vi 28-2-2025
        const params = {
          module: "PipelineConfig",
          parent: "Settings",
          action: "SaveConfig",
          mode: "deletePipelineRecordExist",
          pipelineId: self.pipelineId,
          pipelineIdReplace: self.pipelineIdReplace,
          stageReplace: self.pipelineReplaceMapping,
        };
        app.request.post({ data: params }).then((err, response) => {
          if (err) {
            app.helper.showErrorNotification({
              message: err.message,
            });
            return;
          }

          if (response.success) {
            app.helper.showSuccessNotification({
              message: app.vtranslate("JS_PIPELINE_DELETED_SUCCESS"),
            });
          } else {
            app.helper.showErrorNotification({
              message: response.data,
            });
          }
          app.helper.hideModal();
          app.helper.hideProgress();
          let form = self.getForm();
          self.loadPipelineList(
            form,
            self.currentNameModule,
            self.currentSeachText
          );
        });
        app.helper.hideModal();
        return false;
        //End By The Vi 28-2-2025
      });
    },
    registerPipelineStageReplaceMapping: function () {
      let self = this;

      jQuery('.deletePipelineModal select[name^="swap_status"]')
        .not("#pipeline-list-replace")
        .each(function () {
          let nameAttr = jQuery(this).attr("name");
          let idCurrently = nameAttr.substring(
            nameAttr.indexOf("[") + 1,
            nameAttr.indexOf("]")
          );
          if (
            !self.pipelineReplaceMapping.some(
              (item) => item.idCurrently === idCurrently
            )
          ) {
            self.pipelineReplaceMapping.push({
              idCurrently: idCurrently,
              idReplace: "",
            });
          }
        });

      jQuery('.deletePipelineModal select[name^="swap_status"]')
        .not("#pipeline-list-replace")
        .on("change", function () {
          let newVal = jQuery(this).val();
          let nameAttr = jQuery(this).attr("name");
          let idCurrently = nameAttr.substring(
            nameAttr.indexOf("[") + 1,
            nameAttr.indexOf("]")
          );

          self.pipelineReplaceMapping.forEach(function (item) {
            if (item.idCurrently === idCurrently) {
              item.idReplace = newVal;
            }
          });
        });
    },
    registerPipelineReplaceChangeEvent: function () {
      let formModal = this.getFormModalDeletePipeline();
      jQuery("#pipeline-list-replace").on("change", function () {
        let pipelineId = jQuery(this).val();

        self.pipelineReplaceMapping.forEach(function (item) {
          item.idReplace = "";
        });

        if (!pipelineId) {
          jQuery('select[name="swap_status"]')
            .not("#pipeline-list-replace")
            .prop("disabled", true);
          return;
        } else {
          jQuery('select[name="swap_status"]')
            .not("#pipeline-list-replace")
            .prop("disabled", false);
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
                '<option value="' +
                stage.stageid +
                '">' +
                stage.name +
                "</option>";
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
      });
    },
    registerDeletePipelineEvent: function (pipelineId) {
      let self = this;
      jQuery(document).on("change", "#pipeline-list-replace", function (e) {
        let pipelineId = jQuery(this).val();

        self.pipelineReplaceMapping.forEach(function (item) {
          item.idReplace = "";
        });
        let stageSelects = jQuery(".deletePipelineModal select.select2").not(
          "#pipeline-list-replace"
        );
        if (!pipelineId) {
          stageSelects.prop("disabled", true);
          stageSelects.each(function () {
            let select = jQuery(this);
            select.empty();
            select.append(
              '<option value="">' +
                app.vtranslate("JS_CHOOSE_VALUE") +
                "</option>"
            );
            select.val("").trigger("change.select2");
          });
          return;
        } else {
          stageSelects.prop("disabled", false);
        }
        //Begin By The Vi 28-2-2025

        self.pipelineIdReplace = pipelineId;
        //End by The Vi 28-2-2025
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

          let stageSelects = jQuery(".deletePipelineModal select.select2").not(
            "#pipeline-list-replace"
          );
          stageSelects.each(function () {
            let select = jQuery(this);
            if (select.hasClass("select2-hidden-accessible")) {
              select.select2("destroy");
            }
            select.empty();
            select.append(
              '<option value="">' +
                app.vtranslate("JS_CHOOSE_VALUE") +
                "</option>"
            );
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
                placeholder: app.vtranslate("JS_CHOOSE_VALUE"),
              });
              select.val("").trigger("change.select2");
            });
          }
          app.helper.hideProgress();
        });
      });
      jQuery(document).on(
        "submit",
        "form#deletePipelineEmptyModal",
        function (e) {
          e.preventDefault();
          const form = jQuery(this);
          const idPipeline = form.find('input[name="pipelineId"]').val();

          const params = {
            module: "PipelineConfig",
            parent: "Settings",
            action: "SaveConfig",
            mode: "deletePipelineEmpty",
            pipelineId: idPipeline,
          };
          app.request.post({ data: params }).then((err, response) => {
            if (err) {
              app.helper.showErrorNotification({
                message: err.message,
              });
              return;
            }

            if (response.success) {
              app.helper.showSuccessNotification({
                message: app.vtranslate("JS_PIPELINE_DELETED_SUCCESS"),
              });
            } else {
              app.helper.showErrorNotification({
                message: response.data,
              });
            }
            app.helper.hideModal();
            app.helper.hideProgress();
            let form = self.getForm();
            self.loadPipelineList(
              form,
              self.currentNameModule,
              self.currentSeachText
            );
          });
          return false;
        }
      );
    },
    showDeletePipelineModal: function (pipelineId, moduleName) {
      let self = this;
      app.helper.hideModal();
      self.pipelineId = pipelineId;

      app.helper.showProgress();
      self.modulePipelineDelete = moduleName;

      //Check Pipeline default;
      let params1 = {
        module: "PipelineConfig",
        parent: "Settings",
        action: "SaveConfig",
        mode: "checkPipelineDefault",
        pipelineId: pipelineId,
      };
      app.request.post({ data: params1 }).then((err, res) => {
        app.helper.hideProgress();
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }
        if (res.result) {
          app.helper.showErrorNotification({
            message: app.vtranslate("JS_NOT_DELETE_PIPELINE_DEFAULT"),
          });
          return;
        } else {
          let params2 = {
            module: "PipelineConfig",
            parent: "Settings",
            view: "ConfigAjax",
            mode: "getDeletePipelineModal",
            pipelineId: pipelineId,
            moduleName: moduleName,
          };
          app.request.post({ data: params2 }).then((err, res) => {
            app.helper.hideProgress();

            if (err) {
              app.helper.showErrorNotification({ message: err.message });
              return;
            }
            app.helper.showModal(res, {
              preShowCb: function (modal) {
                const form = modal.find("form#add-stage-pipeline-new");
                form.find('[name="color"]').customColorPicker();
                form.vtValidate({
                  submitHandler: function () {
                    const formData = form.serializeFormData();

                    self.savePipelineStage(formData).then(() => {
                      form.find(".cancelLink").trigger("click");
                    });

                    return false;
                  },
                });
                let stageSelects = jQuery(
                  ".deletePipelineModal select.select2"
                ).not("#pipeline-list-replace");
                stageSelects.prop("disabled", true);
                stageSelects.each(function () {
                  let select = jQuery(this);
                  select.empty();
                  select.append(
                    '<option value="">' +
                      app.vtranslate("JS_CHOOSE_VALUE") +
                      "</option>"
                  );
                  select.val("").trigger("change.select2");
                });
                self.registerPipelineStageReplaceMapping();
              },
            });
          });
        }
      });
    },
    registerModuleChangeEvent: function () {
      let self = this;
      jQuery("#pickListModules").on("change", (e) => {
        self.currentNameModule = jQuery(e.currentTarget).val();
        let form = self.getForm();
        self.loadPipelineList(
          form,
          self.currentNameModule,
          self.currentSeachText
        );
      });
    },
    registerSearchInputEvent: function () {
      let self = this;
      jQuery(".searchWorkflows").on("input", function () {
        let searchText = jQuery(this).val();
        self.currentSeachText = searchText;
        let form = self.getForm();
        self.loadPipelineList(
          form,
          self.currentNameModule,
          self.currentSeachText
        );
      });
    },
    registerToggleStatusEvent: function () {
      let self = this;
      jQuery(document).on("click", ".toggle-switch", function (e) {
        let toggleElement = jQuery(this);
        let pipelineId = toggleElement.closest("tr").attr("data-id");
        let currentStatus = toggleElement.hasClass("active") ? 1 : 0;
        let newStatus = currentStatus === 1 ? 0 : 1;

        self.updatePipelineStatus(pipelineId, newStatus, toggleElement);
      });
    },
    updatePipelineStatus: function (pipelineId, status, toggleElement) {
      app.helper.showProgress();
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        action: "SaveConfig",
        statusPipeline: status,
        mode: "updateStatusPipeline",
        idPipeline: pipelineId,
      };

      app.request.post({ data: params }).then((err, response) => {
        if (err) {
          app.helper.showErrorNotification({
            message: err.message,
          });
          return;
        }
        if (response.success) {
          if (status === 1) {
            toggleElement.addClass("active");
          } else {
            toggleElement.removeClass("active");
          }
          app.helper.showSuccessNotification({
            message: app.vtranslate("JS_STATUS_UPDATED_SUCCESS"),
          });
        } else {
          app.helper.showErrorNotification({
            message: response.message,
          });
        }
        app.helper.hideProgress();
      });
    },
    loadPipelineList: function (form, nameModule, namePipeline) {
      var params = {
        parent: "Settings",
        module: "PipelineConfig",
        view: "ConfigAjax",
        mode: "getPipelineList",
        namePipeline: namePipeline,
        nameModule: nameModule,
      };
      app.helper.showProgress();
      app.request.post({ data: params }).then((err, data) => {
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return false;
        }
        form.find("#pipeline-list").html("");
        form.find("#pipeline-list").html(data);
        app.helper.hideProgress();
      });
    },
    // Begin Dien Nguyen
    clonePipeline: function (targetBtn) {
      let btn = $(targetBtn);
      let pipelineId = btn.closest("tr").attr("data-id");
      app.helper.showProgress();

      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        action: "SaveConfig",
        mode: "clonePipeline",
        pipelineId: pipelineId,
      };
      app.request.post({ data: params }).then((err, response) => {
        app.helper.hideProgress();
        if (err) {
          app.helper.showErrorNotification({
            message: err.message,
          });
          return;
        }
        if (response.success) {
          let form = this.getForm();
          this.loadPipelineList(
            form,
            this.currentNameModule,
            this.currentSeachText
          );
          app.helper.showSuccessNotification({
            message: app.vtranslate("JS_PIPELINE_DUPLICATED_SUCCESS"),
          });
        } else {
          app.helper.showErrorNotification({
            message: response,
          });
        }
        app.helper.hideProgress();
      });
    },
    // End Dien Nguyen
    getForm: function () {
      return $("form#pipeline");
    },
    getFormModalDeletePipeline: function () {
      return $("form#add-stage-pipeline");
    },
  }
);
