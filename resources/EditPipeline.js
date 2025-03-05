/*
    File: EditPipeline.js
    Author:  Team
    Date: 22/1/2025
    Purpose: Handle events for EditPipeline interface
*/

const urlParams = new URLSearchParams(window.location.search);
const record = urlParams.get("record");
const mode = record ? "Edit" : "Add";
CustomView_BaseController_Js(
  "Settings_PipelineConfig_EditPipeline_Js",
  {},
  {
    //Begin The Vi
    urlParams: urlParams,
    record: record,
    mode: mode,
    stageReplaceMapping: [],
    //End by The Vi
    currentNameModule: "Potentials",
    pipelineId: "",
    isFirstOpen: 1,
    stagesList: [],
    rolesList: [],
    getPicklistname: function () {
      switch (this.currentNameModule) {
        case "Potentials":
          return "sales_stage";
        case "Leads":
          return "leadstatus";
        case "Project":
          return "projectstatus";
        case "HelpDesk":
          return "ticketstatus";
        default:
          return "sales_stage";
      }
    },

    //Begin Tran Dien
    pushAction: function (action, isEdit = false) {
      let stage = this.stagesList.find(
        (stage) => stage.id === action["stageId"]
      );
      if (stage && !isEdit) {
        stage.actions = stage.actions || [];
        stage.actions.push(action);
      }
      console.log(this.stagesList);
      let actionBox = document.querySelector(
        `.action-box[data-stageid='${action["stageId"]}']`
      );
      let actionTypeOnce = Array.from(
        actionBox.querySelectorAll(".action-type")
      ).find(
        (el) =>
          el.querySelector("h5").textContent.trim() ===
          "Hành động thực hiện một lần"
      );
      let actionTypeCondition = Array.from(
        actionBox.querySelectorAll(".action-type")
      ).find(
        (el) =>
          el.querySelector("h5").textContent.trim() ===
          "Hành động thực hiện khi thỏa điều kiện"
      );

      let iconMap = {
        addCall: "fa-phone",
        addMeeting: "fa-users",
        createNewTask: "fa-list",
        createNewProjectTask: "fa-clipboard-list",
        createNewRecord: "fa-plus-circle",
        updateDataField: "fa-pencil-alt",
        sendZNSMessage: "fa-comments-alt",
        sendSMSMessage: "fa-sms",
        sendEmail: "fa-envelope",
        notification: "fa-bell",
      };
      let iconClass = iconMap[action.action_type] || "fa-pencil-alt";
      let newActionItem = document.createElement("div");
      newActionItem.classList.add("action-item", "btnAddAction");
      newActionItem.setAttribute("data-isEdit", "true");
      newActionItem.setAttribute("data-action", JSON.stringify(action));
      newActionItem.innerHTML = `
            <i class="fal ${iconClass} ml-2 text-primary"></i>
            <p class="text-primary pt-3">${action["action_name"]}</p>
            <i class="fal fa-times"></i>
        `;
      if (action.frequency === "onceAction") {
        if (!actionTypeOnce) {
          let newActionType = document.createElement("div");
          newActionType.classList.add("action-type");
          newActionType.innerHTML = `
                <h5 class="action-title">Hành động thực hiện một lần</h5>
            `;
          actionBox.prepend(newActionType);
          actionTypeOnce = newActionType;
        }
        actionTypeOnce.appendChild(newActionItem);
      } else if (action.frequency === "actionWithCondition") {
        if (!actionTypeCondition) {
          let newActionType = document.createElement("div");
          newActionType.classList.add("action-type");
          newActionType.innerHTML = `
                <h5 class="action-title">Hành động thực hiện khi thỏa điều kiện</h5>
            `;
          if (actionTypeOnce) {
            actionTypeOnce.after(newActionType);
          } else {
            actionBox.prepend(newActionType);
          }
          actionTypeCondition = newActionType;
        }
        actionTypeCondition.appendChild(newActionItem);
      }
    },

    getCurrentNameModule: function () {
      return this.currentNameModule;
    },

    pushCondition: function (condition) {
      if (Object.keys(condition["filterInfo"]).length > 0) {
        if (
          (condition["filterInfo"]["1"]["columns"] &&
            Object.keys(condition["filterInfo"]["1"]["columns"]).length > 0) ||
          (condition["filterInfo"]["2"]["columns"] &&
            Object.keys(condition["filterInfo"]["2"]["columns"]).length > 0)
        ) {
          let stage = this.stagesList.find(
            (stage) => stage.id === condition["stageid"]
          );

          if (stage) {
            // Đảm bảo stage có thuộc tính conditions là một object trước khi push
            stage.conditions = condition.filterInfo || {};
          }
          console.log(this.stagesList);
          // Tìm tất cả các thẻ có class "condition-box" và kiểm tra điều kiện
          let conditionBox = document.querySelector(
            `.condition-box[data-stageid='${condition["stageid"]}']`
          );
          // Nếu tồn tại thẻ phù hợp, cập nhật nội dung của nó
          jQuery(conditionBox).html(`
            <div class="action-item btnAddCondition" data-stageid="${
              condition.stageid
            }" data-conditions='${JSON.stringify(condition)}'> 
                <i class="fal fa-cogs ml-2"></i>
                <p class="text-primary pt-3">Điều kiện chuyển bước</p>
                <i class="fal fa-times removeCondition"></i>
            </div>
        `);
        }
      }
    },

    //End Tran Dien
    registerEvents: function () {
      this._super();
      this.registerEventFormInit();
      this.registerCheckboxEvents();
    },
    registerEventFormInit: function () {
      // alert("registerEventFormInitUpdate update3");
      let self = this;
      let form = this.getForm();
      // alert(self.mode);
      // alert(self.record);
      // alert("Hello");
      //Begin The Vi
      this.registerModuleChangeEvent();
      this.registerCancelButtonClick();
      this.registerAddStageNewSaveEvent();
      this.registerSelectRolesEvent(form);
      this.registerStagesSortableEvent();
      this.registerTimeUnitChangeEvent();
      this.registerTimeValueChangeEvent();
      this.registerNextButtonClickEvent();
      this.registerGetRoleList();
      this.handleColumnVisibility();
      form.find(".btn-add-stage").on("click", function () {
        self.showStagePipelineModal(this);
      });
      jQuery(document).on("click", ".btn-add-new-stage-modal", function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.showStagePipelineModalNew(this);
      });
      jQuery("#stagesTable").on("click", ".icon-del", function (e) {
        e.stopPropagation();
        const stageId = jQuery(this).closest("tr").data("stage-id");
        self.deleteStage(stageId);
      });
      if (this.currentNameModule === "Potentials") {
        this.sortStagesBySuccessRate();
      }
      this.handleColumnVisibilityTimePipeline();
      jQuery(document).on("submit", "#delete-stage-pipeline", function (e) {
        e.preventDefault();
        let hiddenValue = jQuery('input[name="name_stage_delete_hidden"]')
          .val()
          .trim();
        let selectedValue = jQuery('select[name="list_stage_select"]')
          .find("option:selected")
          .val()
          .trim();
        if (!hiddenValue || !selectedValue) {
          app.helper.showErrorNotification({
            message:
              "Cả giá trị của bước xóa và bước thay thế đều đang rỗng. Vui lòng nhập đầy đủ thông tin!",
          });
          return;
        }
        if (hiddenValue == selectedValue) {
          app.helper.showErrorNotification({
            message: "Bước xóa và bước thay thế phải khác nhau !",
          });
          return;
        }
        let params = {
          module: "PipelineConfig",
          parent: "Settings",
          action: "SaveEdit",
          mode: "deleteStagePipeline",
          idStageDelete: hiddenValue,
          idStageReplace: selectedValue,
          current_module: self.currentNameModule,
        };
        app.request.post({ data: params }).then((err, response) => {
          app.helper.hideProgress();
          console.log("params", params);
          console.log("Responsive", response);
          if (err) {
            app.helper.showErrorNotification({
              message: err.message || "Có lỗi xảy ra khi thêm giai đoạn mới",
            });
            return;
          }
          self.stagesList = self.stagesList.filter(
            (stage) => stage.id != hiddenValue
          );
          // Remove from DOM
          jQuery(`tr[data-stage-id="${hiddenValue}"]`).remove();
          app.helper.showSuccessNotification({
            message: "Đã xóa bước thành công",
          });
          self.calculateTotalTime();
          self.updateNextStagesOptions();
          console.log("Updated stagesList after delete:", self.stagesList);
          app.helper.hideModal();
          return;
        });
      });

      //End The Vi

      //  =================================================================================================

      //Begin Tran Dien

      // this.renderStageCrumbs(this.stagesList);
      // this.renderStagesInfo(this.stagesList);
      // this.registerAddActionSettingModal(form);
      // this.registerAddCondition(form);
      // this.registerSavePipelineButtonClickEvent(form.find(".savePipeline"));
      //End Tran Dien
      if (mode == "Edit") {
        let params = {
          module: "PipelineConfig",
          parent: "Settings",
          action: "SaveEdit",
          mode: "getDetailPipeline",
          id: self.record,
        };

        function decodeHTMLEntities(text) {
          if (!text) return text;
          const textArea = document.createElement("textarea");
          textArea.innerHTML = text;
          return textArea.value;
        }

        function decodeAllStrings(obj) {
          if (!obj) return obj;
          if (typeof obj === "string") {
            return decodeHTMLEntities(obj);
          }
          if (Array.isArray(obj)) {
            return obj.map((item) => decodeAllStrings(item));
          }
          if (typeof obj === "object") {
            const decoded = {};
            for (const key in obj) {
              decoded[key] = decodeAllStrings(obj[key]);
            }
            return decoded;
          }
          return obj;
        }

        app.request.post({ data: params }).then((err, response) => {
          app.helper.hideProgress();
          if (err) {
            app.helper.showErrorNotification({
              message: err.message || "Có lỗi xảy ra khi thêm giai đoạn mới",
            });
            return;
          }
          console.log("RESPonse", response);

          const decodedResponse = decodeAllStrings(response);
          console.log("Decoded Response:", decodedResponse);

          if (decodedResponse) {
            self.currentNameModule = decodedResponse.module;
            self.pipelineId = decodedResponse.id;
            $('input[name="name"]').val(decodedResponse.name);

            if (decodedResponse.module === "Potentials") {
              $(".othermodule").hide();
              $(".potentials").show();
              const totalTime = decodedResponse.stagesList.reduce(
                (sum, stage) => {
                  return sum + (stage.execution_time?.value || 0);
                },
                0
              );
              $(".toal-time-pipeline").text(totalTime + " ngày");
            } else {
              $(".othermodule").show();
              $(".potentials").hide();

              const defaultTime = decodedResponse.time || 0;
              const defaultTimeUnit = decodedResponse.time_unit || "Day";

              $('input[name="time"]').val(defaultTime);

              const $timeTypeSelect = $('select[name="timetype"]');
              $timeTypeSelect.val("");
              $timeTypeSelect.val(defaultTimeUnit);

              if ($timeTypeSelect.hasClass("select2-offscreen")) {
                $timeTypeSelect.select2("val", defaultTimeUnit);
              }
            }

            $("#listModule").val(decodedResponse.module).trigger("change");

            $("#autoTransition").prop(
              "checked",
              decodedResponse.autoTransition
            );
            if (
              decodedResponse.rolesSelected &&
              decodedResponse.rolesSelected.length
            ) {
              const $rolesDropdown = $("#rolesDropdown");
              $rolesDropdown
                .find('option[value="all"]')
                .prop("selected", false);

              decodedResponse.rolesSelected.forEach((role) => {
                const $option = $rolesDropdown.find(
                  `option[value="${role.role_id}"]`
                );
                if ($option.length) {
                  $option.prop("disabled", false).prop("selected", true);
                }
              });
              $rolesDropdown.trigger("change");
            }

            $('textarea[name="description"]').val(decodedResponse.description);

            const statusValue =
              decodedResponse.status === "1" ? "active" : "inActive";
            $(`input[name="status"][value="${statusValue}"]`).prop(
              "checked",
              true
            );

            self.stagesList = decodedResponse.stagesList.map((stage) => {
              let decodedActions = stage.actions;
              try {
                if (typeof stage.actions === "string") {
                  decodedActions = JSON.parse(
                    decodeHTMLEntities(stage.actions)
                  );
                }
              } catch (e) {
                console.error("Error parsing actions:", e);
                decodedActions = [];
              }

              return {
                ...stage,
                id: parseInt(stage.id, 10),
                actions: decodedActions,
                next_stages: stage.next_stages.map((next) => ({
                  ...next,
                  id: parseInt(next.id, 10),
                })),
              };
            });

            self.handleColumnVisibility();
            console.log("StagesList sau khi xử lý:", self.stagesList);
            self.renderStages();
          }
        });
      }
    },
    decodeHTMLEntities(text) {
      const textArea = document.createElement("textarea");
      textArea.innerHTML = text;
      return textArea.value;
    },
    decodeAllStrings(obj) {
      if (!obj) return obj;
      if (typeof obj === "string") return decodeHTMLEntities(obj);
      if (Array.isArray(obj)) return obj.map((item) => decodeAllStrings(item));
      if (typeof obj === "object") {
        const decoded = {};
        for (const key in obj) {
          decoded[key] = decodeAllStrings(obj[key]);
        }
        return decoded;
      }
      return obj;
    },

    renderStages() {
      this.stagesList.forEach((stage) => {
        console.log("Stage:", stage);
        const stageRow = this.createStageRow({
          stageid: stage.id,
          pipelineid: self.record,
          name: stage.name,
          vnLabel: stage.vnLabel,
          enLabel: stage.enLabel,
          value: stage.value,
          color_code: stage.color,
          success_rate: stage.success_rate,
          time: stage.execution_time.value,
          time_unit: stage.execution_time.unit,
          is_mandatory: stage.is_mandatory,
          next_stages: stage.next_stages,
          permissions: stage.permissions,
        });

        // const stageRow = self.createStageRow(stageData);
        jQuery("#stagesTable tbody").append(stageRow);
      });

      // // Initialize drag & drop if needed
      // if (this.currentNameModule !== "Potentials") {
      //   this.initStageDragDrop();
      // }
    },
    //Begin The Vi
    handleColumnVisibility: function () {
      if (this.currentNameModule !== "Potentials") {
        $("#success-rate-column, #execution-time-column").hide();

        $("#stage-name-column").css("width", "15%");
        $("#mandatory-column").css("width", "8%");
        $("#next-stages-column").css("width", "25%");
        $("#permissions-column").css("width", "25%");
        $("#actions-column").css("width", "5%");
        $("#mandatory-column").css({
          "max-width": "80px",
          "min-width": "60px",
        });
        $(".mandatory-checkbox").parent().css({
          display: "flex",
          "justify-content": "center",
          "align-items": "center",
        });
      } else {
        $("#success-rate-column, #execution-time-column").show();
        $("#stage-name-column").css("width", "15%");
        $("#success-rate-column").css("width", "10%");
        $("#execution-time-column").css("width", "10%");
        $("#mandatory-column").css("width", "8%");
        $("#next-stages-column").css("width", "27%");
        $("#permissions-column").css("width", "25%");
        $("#actions-column").css("width", "5%");
        $("#mandatory-column").css({
          "max-width": "80px",
          "min-width": "60px",
        });
      }
    },
    handleColumnVisibilityTimePipeline: function () {
      if (this.currentNameModule === "Potentials") {
        jQuery(".othermodule").hide();
        jQuery(".potentials").show();
      } else {
        jQuery(".othermodule").show();
        jQuery(".potentials").hide();
      }
    },
    registerGetRoleList: function () {
      let self = this;
      app.helper.hideModal();
      app.helper.showProgress();
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        action: "SaveEdit",
        mode: "getRoleList",
      };
      app.request.post({ data: params }).then((err, res) => {
        app.helper.hideProgress();
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }

        self.rolesList = [{ id: "all", name: "Tất cả" }];
        res.forEach((role) => {
          self.rolesList.push({
            id: role.roleid,
            name: role.rolename,
          });
        });
        console.log("Updated rolesList:", self.rolesList);
      });
    },

    // Submit save Pipeline final
    registerSavePipelineButtonClickEvent: function ($button) {
      let self = this;
      $button.on("click", function (e) {
        //Begin Tran Dien
        self.stagesList.forEach((stage) => {
          stage["actions"] = stage["actions"] || [];
          stage["actions"] = JSON.stringify(stage["actions"]);
          stage["conditions"] = stage["conditions"] || {};
          stage["conditions"] = JSON.stringify(stage["conditions"]);
        });
        //End Tran Dien
        e.preventDefault();
        app.helper.showProgress();
        // alert("Lưu");
        let pipelineData = {};
        let formDataArray = $("#editPipeline").serializeArray();
        formDataArray.forEach(function (field) {
          pipelineData[field.name] = field.value;
        });
        let timeValue = 0;
        let timeType = "Day";

        if (self.currentNameModule === "Potentials") {
          timeValue =
            $(".toal-time-pipeline").text().trim().replace(/\D/g, "") || 0;
          timeType = "Day";
        } else {
          timeValue = pipelineData.time || 0;
          timeType = pipelineData.timetype || "Day";
        }
        let rolesSelected = [];
        $("#rolesDropdown option:selected").each(function () {
          rolesSelected.push($(this).val());
        });
        let autoTransition = $("#autoTransition").is(":checked");
        let Pipeline = {
          id: self.pipelineId,
          name: pipelineData.name || "",
          time: parseInt(timeValue),
          timetype: timeType,
          module: pipelineData.module || "",
          autoTransition: autoTransition,
          rolesSelected: rolesSelected,
          description: pipelineData.description || "",
          status: pipelineData.status || "active",
          stagesList: self.stagesList || [],
        };
        console.log("Pipeline:", Pipeline);
        if (self.mode != "Edit") {
          let params = {
            module: "PipelineConfig",
            parent: "Settings",
            action: "SaveEdit",
            mode: "savePipeline",
            dataPipeline: Pipeline,
          };
          app.request.post({ data: params }).then((err, response) => {
            app.helper.hideProgress();
            console.log("Response savePipeline:", response);
            app.helper.showSuccessNotification({
              message: "Thêm pipeline thành công !",
            });
            if (err) {
              app.helper.showErrorNotification({
                message: err.message || "Có lỗi xảy ra khi thêm giai đoạn mới",
              });
              return;
            }
          });
        } else {
          console.log("Update Pipeline:", Pipeline);
          let params = {
            module: "PipelineConfig",
            parent: "Settings",
            action: "SaveEdit",
            mode: "updatePipeline",
            dataPipeline: Pipeline,
          };
          app.request.post({ data: params }).then((err, response) => {
            app.helper.hideProgress();
            console.log("Response updatePipeline:", response);
            app.helper.showSuccessNotification({
              message: "Cập nhật pipeline thành công !",
            });
            if (err) {
              app.helper.showErrorNotification({
                message: err.message || "Có lỗi xảy ra khi thêm giai đoạn mới",
              });
              return;
            }
          });
          app.helper.hideProgress();
        }
      });
    },
    registerNextButtonClickEvent: function () {
      let self = this;
      let form = this.getForm();
      jQuery(".nextButton").on("click", function (e) {
        e.preventDefault();
        let name = jQuery('input[name="name"]').val().trim();
        let module = jQuery('select[name="module"]').val();
        let stageCount = self.stagesList.length;
        let hasError = false;
        let errorMessage = "";
        if (!name) {
          hasError = true;
          errorMessage += "Vui lòng nhập tên pipeline\n";
        }
        if (!module) {
          hasError = true;
          errorMessage += "Vui lòng chọn module\n";
        }
        if (stageCount < 1) {
          hasError = true;
          errorMessage += "Vui lòng thêm ít nhất 1 stage\n";
        }
        if (hasError) {
          app.helper.showErrorNotification({
            message: errorMessage,
          });
          return false;
        }
        jQuery(".tab1").removeClass("active");
        jQuery("#tab1").removeClass("active");
        jQuery(".tab2").addClass("active");
        jQuery("#tab2").addClass("active");
        let $button = jQuery(this);
        $button.off("click");
        $button.removeClass("nextButton").addClass("savePipeline").text("Lưu");
        self.registerSavePipelineButtonClickEvent($button);
        // alert("Chuyển");
        //Begin Tran Dien
        console.log("Bước: ", self.stagesList);
        self.renderStageCrumbs(self.stagesList);
        self.renderStagesInfo(self.stagesList);
        self.registerAddActionSettingModal(form);
        self.registerAddCondition(form);
        self.registerRemoveCondition(form);
        // self.registerSavePipelineButtonClickEvent(form.find(".savePipeline"));

        //End Tran Dien
      });
    },
    registerTimeUnitChangeEvent: function () {
      let self = this;
      jQuery(document).on("change", ".time-unit-select", function (e) {
        let newUnit = jQuery(this).val();
        let stageRow = jQuery(this).closest("tr.stageRow");
        let stageId = stageRow.data("stage-id");
        let stageItem = self.stagesList.find((item) => item.id === stageId);
        if (stageItem) {
          stageItem.execution_time.unit = newUnit;

          self.calculateTotalTime();
          console.log("Cập nhật stage:", stageItem);
        }
      });
    },
    registerTimeValueChangeEvent: function () {
      let self = this;
      jQuery(document).on("input", ".time-value-input", function (e) {
        let newTimeValue = jQuery(this).val();
        if (isNaN(newTimeValue) || newTimeValue === "") {
          newTimeValue = 0;
        }
        let stageRow = jQuery(this).closest("tr.stageRow");
        let stageId = stageRow.data("stage-id");
        let stageItem = self.stagesList.find((item) => item.id === stageId);
        if (stageItem) {
          stageItem.execution_time.value = newTimeValue;

          self.calculateTotalTime();
          console.log("Cập nhật stage:", self.stagesList);
        }
      });
    },
    registerStagesSortableEvent: function () {
      var thisInstance = this;
      var tbody = jQuery("tbody", jQuery("#stagesTable"));

      tbody.sortable({
        helper: function (e, ui) {
          // Maintain cell widths during drag
          ui.children().each(function (index, element) {
            element = jQuery(element);
            element.width(element.width());
          });
          return ui;
        },
        handle: ".cursorDrag",
        containment: tbody,
        revert: true,
        cursor: "move",
        update: function (e, ui) {
          thisInstance.saveSequence();
        },
      });
    },
    saveSequence: function () {
      var self = this;
      var stageRows = jQuery("#stagesTable").find(".stageRow");
      var updatedStages = [];
      stageRows.each(function (index) {
        var stageId = jQuery(this).data("stage-id");
        var stageItem = self.stagesList.find((item) => item.id === stageId);
        if (stageItem) {
          var updatedStage = { ...stageItem };
          updatedStage.sequence = index + 1;
          updatedStages.push(updatedStage);
        }
      });
      self.stagesList = updatedStages;
      self.stagesList.sort((a, b) => a.sequence - b.sequence);
      self.updateNextStagesOptions();
      app.helper.showSuccessNotification({
        message: "Bước đã được sắp xếp lại thành công",
      });
      console.log("Updated stagesList2:", self.stagesList);
    },
    registerSelectRolesEvent: function (data) {
      data.find('[name="rolesSelected[]"]').on("change", function (e) {
        // alert("regiserSelectRolesEvent");
        var rolesSelectElement = jQuery(e.currentTarget);
        var selectedValue = rolesSelectElement.val();
        if (jQuery.inArray("all", selectedValue) != -1) {
          rolesSelectElement.select2("val", "");
          rolesSelectElement.select2("val", "all");
          rolesSelectElement.select2("close");
          rolesSelectElement
            .find("option")
            .not(":first")
            .attr("disabled", "disabled");
          if (jQuery(".allRoleSelected").length < 1)
            data
              .find(jQuery(".modal-body"))
              .append(
                '<div class="alert alert-info textAlignCenter allRoleSelected">' +
                  app.vtranslate("JS_ALL_ROLES_SELECTED") +
                  "</div>"
              );
        } else {
          rolesSelectElement.find("option").removeAttr("disabled", "disabled");
          data.find(".modal-body").find(".alert").remove();
        }
      });
    },
    updateNextStagesOptions: function () {
      let self = this;
      const sortedStages = [...self.stagesList].sort(
        (a, b) => a.sequence - b.sequence
      );

      jQuery("#stagesTable tbody .stageRow").each(function () {
        const row = jQuery(this);
        const stageId = row.data("stage-id");
        const currentIndex = sortedStages.findIndex(
          (item) => item.id == stageId
        );
        if (currentIndex !== -1) {
          let selected = row.find("select.stage-next-select").val() || [];
          let optionsHtml = "";
          for (let i = currentIndex + 1; i < sortedStages.length; i++) {
            const stageItem = sortedStages[i];
            const isSelected = selected.includes(stageItem.id.toString())
              ? "selected"
              : "";
            optionsHtml += `<option value="${stageItem.id}" ${isSelected}>${stageItem.name}</option>`;
            if (stageItem.is_mandatory) {
              break;
            }
          }
          let $select = row.find("select.stage-next-select");
          $select.html(optionsHtml);
          $select.trigger("change");
        }
      });
    },
    showStagePipelineModalNew: function (targetBtn) {
      app.helper.hideModal();
      let self = this;
      app.helper.showProgress();
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        view: "EditPipelineAjax",
        mode: "getStagePipelineModalNew",
      };
      app.request.post({ data: params }).then((err, res) => {
        app.helper.hideProgress();

        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }
        app.helper.showModal(res, {
          preShowCb: function (modal) {
            const form = modal.find("form#add-stage-pipeline-new");
            form.find('[name="color"]').customColorPicker();
            // form.vtValidate({
            //   submitHandler: function () {
            //     const formData = form.serializeFormData();
            //     self.savePipelineStage(formData).then(() => {
            //       form.find(".cancelLink").trigger("click");
            //     });

            //     return false;
            //   },
            // });
          },
        });
      });
    },
    registerCheckboxEvents: function () {
      let self = this;

      jQuery("#stagesTable").on("change", ".mandatory-checkbox", function () {
        const stageRow = jQuery(this).closest(".stageRow");
        const stageId = stageRow.data("stage-id");
        const isChecked = jQuery(this).prop("checked");

        self.stagesList = self.stagesList.map((stage) => {
          if (stage.id === stageId) {
            return {
              ...stage,
              is_mandatory: isChecked,
            };
          }
          return stage;
        });
        app.helper.showSuccessNotification({
          message: "Cập nhật bước bắt buộc thành công !",
        });
        console.log(
          "Updated stagesList after checkbox change:",
          self.stagesList
        );

        self.updateNextStagesOptions();
      });
    },

    showStagePipelineModal: function (targetBtn) {
      app.helper.hideModal();
      let self = this;
      console.log("Initdd", self.stagesList);

      app.helper.showProgress();
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        view: "EditPipelineAjax",
        mode: "getStagePipelineModal",
        source_module: self.currentNameModule,
      };
      app.request.post({ data: params }).then((err, res) => {
        app.helper.hideProgress();
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }
        app.helper.showModal(res, {
          preShowCb: function (modal) {
            const form = modal.find("form#add-stage-pipeline");
            form.find('[name="color"]').customColorPicker();
            form.on("submit", function (e) {
              e.preventDefault();
              const formData = form.serializeFormData();
              let errors = [];
              if (!formData.vn_label_select) {
                errors.push("Vui lòng chọn nhãn hiển thị Tiếng Việt");
              }
              if (!formData.en_label) {
                errors.push("Vui lòng nhập nhãn hiển thị Tiếng Anh");
              }
              if (!formData.value) {
                errors.push("Vui lòng nhập giá trị");
              }
              if (!formData.color) {
                errors.push("Vui lòng chọn màu");
              }
              if (errors.length > 0) {
                const errorMessage = errors.join("<br>");
                app.helper.showErrorNotification({
                  message: errorMessage,
                });
                return false;
              }
              const inputValue = formData.value.trim().toLowerCase();
              console.log("Check trùng", self.stagesList);
              const isDuplicate = self.stagesList.some(function (stage) {
                return stage.value.trim().toLowerCase() === inputValue;
              });

              if (isDuplicate) {
                app.helper.showErrorNotification({
                  message: "Giá trị đã tồn tại, không thể thêm bước trùng",
                });
                return false;
              }
              const selectedOptionText = form
                .find("#vn_label_select option:selected")
                .text();
              const newStageId =
                formData.stage_id ||
                Math.floor(Math.random() * 9000000) + 1000000;
              const pipelineId = form.find('[name="pipeline_id"]').val();
              const stageData = {
                stageid: newStageId,
                pipelineid: pipelineId,
                name: selectedOptionText,
                vnLabel: selectedOptionText,
                enLabel: formData.en_label,
                value: formData.value,
                success_rate: formData.success_rate || 0,
                time: formData.time || 0,
                time_unit: formData.time_unit || "Day",
                is_mandatory:
                  formData.is_mandatory === "on" ||
                  formData.is_mandatory === true,

                color_code: formData.color,
                next_stages: [],
                permissions: [],
              };

              const stageRow = self.createStageRow(stageData);
              jQuery("#stagesTable tbody").append(stageRow);
              console.log("Beginx", self.stagesList);
              self.stagesList.push({
                id: newStageId,
                sequence: self.stagesList.length + 1,
                name: selectedOptionText,
                success_rate: formData.success_rate || 0,
                vnLabel: selectedOptionText,
                enLabel: formData.en_label,
                value: formData.value,
                execution_time: {
                  value: formData.time || 0,
                  unit: formData.time_unit || "Day",
                },
                is_mandatory: formData.is_mandatory || false,
                next_stages: [],
                permissions: [],
                color: formData.color,
                status: "active",
                created_time: new Date().toISOString(),
                modified_time: new Date().toISOString(),
                created_user_id: app.getUserId(),
                modified_user_id: app.getUserId(),
              });
              console.log("Endxx", self.stagesList);
              self.sortStagesBySuccessRate();

              // console.log("End", self.stagesList);
              app.helper.hideModal();
              app.helper.showSuccessNotification({
                message: "Thêm giai đoạn mới thành công",
              });
              //Begin The Vi
              // self.renderStageCrumbs(self.stagesList);
              // self.renderStagesInfo(self.stagesList);
            });
          },
        });
      });
    },

    updateNextStageOptions: function () {
      const self = this;
      const rows = jQuery("#stagesTable tbody tr.stageRow");

      rows.each(function (index) {
        const row = jQuery(this);
        const stageId = row.data("stage-id");
        const nextStageSelect = row.find(".stage-next-select");

        // Clear current options
        nextStageSelect.empty();

        // Get all stages after current one
        const currentStageIndex = self.stagesList.findIndex(
          (s) => s.id === stageId
        );
        const laterStages = self.stagesList.slice(currentStageIndex + 1);

        // Add new options
        laterStages.forEach((stage) => {
          nextStageSelect.append(new Option(stage.name, stage.id));
        });

        // Re-initialize select2
        nextStageSelect.select2();

        // Restore selected values if any
        const stage = self.stagesList.find((s) => s.id === stageId);
        if (stage && stage.next_stages) {
          const selectedIds = stage.next_stages.map((s) => s.id);
          nextStageSelect.val(selectedIds).trigger("change");
        }
      });
    },
    sortStagesBySuccessRate: function () {
      const self = this;

      const zeroRateStages = this.stagesList.filter(
        (stage) => stage.success_rate === 0
      );
      const nonZeroStages = this.stagesList.filter(
        (stage) => stage.success_rate > 0
      );

      nonZeroStages.sort((a, b) => a.success_rate - b.success_rate);

      this.stagesList = [...nonZeroStages, ...zeroRateStages];

      this.stagesList.forEach((stage, index) => {
        stage.sequence = index + 1;
      });

      const tbody = jQuery("#stagesTable tbody");
      tbody.empty();

      this.stagesList.forEach((stage) => {
        const stageData = {
          stageid: stage.id,
          pipelineid: stage.pipeline_id,
          name: stage.name,
          vnLabel: stage.vnLabel,
          enLabel: stage.enLabel,
          value: stage.value,
          success_rate: stage.success_rate,
          time: stage.execution_time.value,
          time_unit: stage.execution_time.unit,
          is_mandatory: stage.is_mandatory,
          color_code: stage.color,
          next_stages: stage.next_stages,
          permissions: stage.permissions,
        };
        const row = this.createStageRow(stageData);
        tbody.append(row);
      });
    },

    createStageRow: function (stageData) {
      const self = this;
      const roleOptions = this.rolesList
        .map((role) => {
          const isSelected = stageData.permissions.some(
            (p) => p.role_id === role.id
          )
            ? "selected"
            : "";
          return `<option value="${role.id}" ${isSelected}>${role.name}</option>`;
        })
        .join("");
      const showRateAndTime = this.currentNameModule === "Potentials";
      const showDragIcon = this.currentNameModule !== "Potentials";

      let optionsHtml = "";
      const sortedStages = [...self.stagesList].sort(
        (a, b) => a.sequence - b.sequence
      );
      const currentIndex = sortedStages.findIndex(
        (item) => item.id === stageData.stageid
      );

      if (currentIndex !== -1) {
        const selected = stageData.next_stages.map((ns) => ns.id) || [];
        for (let i = currentIndex + 1; i < sortedStages.length; i++) {
          const stageItem = sortedStages[i];
          const isSelected = selected.includes(stageItem.id) ? "selected" : "";
          optionsHtml += `<option value="${stageItem.id}" ${isSelected}>${stageItem.name}</option>`;
          if (stageItem.is_mandatory) {
            break;
          }
        }
      }
      const rowHtml = `
    <tr class="tr-height stageRow cursorPointer"      
         data-stage-id="${stageData.stageid}"
         data-pipeline-id="${stageData.pipelineid}"
         data-name="${stageData.name}"
         data-vn-label="${stageData.vnLabel}"
         data-en-label="${stageData.enLabel}"
         data-value="${stageData.value}"
         data-color="${stageData.color_code}"
    >
      <td class="textOverflowEllipsis">
        <div class="row-one" style="border-left: 5px solid ${
          stageData.color_code
        };">
          <span class="pull-left textOverflowEllipsis">${stageData.name}</span>
          <span class="edit-icon">
            <i class="far fa-pen icon"></i>
            ${
              showDragIcon
                ? `&nbsp;&nbsp;<i class="far fa-up-down-left-right icon cursorDrag" style="cursor: move;"></i>`
                : ""
            }
          </span>
        </div>
      </td>
      ${
        showRateAndTime
          ? `
      <td class="fieldValue">
        <div class="col-center">
<select class="inputElement select2 select2-offscreen success-rate-select textAlignCenter" tabindex="-1">
  <option value="0" ${stageData.success_rate == 0 ? "selected" : ""}>0%</option>
  <option value="10" ${
    stageData.success_rate == 10 ? "selected" : ""
  }>10%</option>
  <option value="25" ${
    stageData.success_rate == 25 ? "selected" : ""
  }>25%</option>
  <option value="50" ${
    stageData.success_rate == 50 ? "selected" : ""
  }>50%</option>
  <option value="75" ${
    stageData.success_rate == 75 ? "selected" : ""
  }>75%</option>
  <option value="100" ${
    stageData.success_rate == 100 ? "selected" : ""
  }>100%</option>
</select>
        </div>
      </td>
      <td class="fieldValue">
        <div class="col-center">
          <input type="text" value="${
            stageData.time || 0
          }" class="inputElement time-value-input textAlignCenter" style="width: 60px; margin-right: 5px;">
          <select class="inputElement time-unit-select select2 select2-offscreen textAlignCenter" tabindex="-1" style="width: 100px" title="">
            <option value="Day" ${
              stageData.time_unit === "Day" || stageData.time_unit === "Ngày"
                ? "selected"
                : ""
            }>Ngày</option>
            <option value="Month" ${
              stageData.time_unit === "Month" || stageData.time_unit === "Tháng"
                ? "selected"
                : ""
            }>Tháng</option>
            <option value="Year" ${
              stageData.time_unit === "Year" || stageData.time_unit === "Năm"
                ? "selected"
                : ""
            }>Năm</option>
          </select>
        </div>
      </td>
      `
          : ""
      }
      <td class="fieldValue">
        <div class="col-center">
          <input type="hidden" value="0">
          <input class="inputElement mandatory-checkbox" 
                 style="width: 20px !important; height: 20px !important;" 
                 data-fieldtype="checkbox" 
                 type="checkbox"
                 ${stageData.is_mandatory ? "checked" : ""}>
        </div>
      </td>
      <td class="fieldValue">
        <div class="col-center">
           <select multiple class="inputElement select2 stage-next-select">
        ${optionsHtml}
      </select>
        </div>
      </td>
      <td class="fieldValue">
        <div class="col-center">
          <select multiple class="inputElement select2 roles-select">
            ${roleOptions}
          </select>
        </div>
      </td>
      <td class="fieldValue">
        <div class="col-center">
          <span><i class="far fa-close icon-del"></i></span>
        </div>
      </td>
    </tr>
  `;

      const row = $(rowHtml);
      row.find("select.select2").each(function () {
        $(this).select2({});
      });

      row.find(".icon-del").on("click", (e) => {
        e.stopPropagation();
        this.deleteStage(stageData.stageid);
      });

      row.find(".roles-select").on("change", function (e) {
        const selectedRoles = $(this).val();
        const stageId = row.data("stage-id");

        const stageIndex = self.stagesList.findIndex(
          (stage) => stage.id === stageId
        );

        if (stageIndex !== -1 && Array.isArray(selectedRoles)) {
          self.stagesList[stageIndex].permissions = selectedRoles.map(
            (roleId) => {
              const role = self.rolesList.find((r) => r.id === roleId);
              return {
                role_id: role ? role.id : null,
                role_name: role ? role.name : "",
              };
            }
          );
        }
        console.log(self.stagesList);
      });
      row.find("select.success-rate-select").on("change", function (e) {
        const selectedRate = $(this).val();
        const stageId = row.data("stage-id");

        const stageIndex = self.stagesList.findIndex(
          (stage) => stage.id === stageId
        );
        if (stageIndex !== -1) {
          self.stagesList[stageIndex].success_rate = parseInt(selectedRate);
          self.sortStagesBySuccessRate();
          app.helper.showSuccessNotification({
            message: `Đã cập nhật tỉ lệ thành công thành ${selectedRate}%`,
          });

          console.log("Updated and sorted stages:", self.stagesList);
        }
      });

      row.find("select.stage-next-select").on("change", function (e) {
        const selectedNextStages = $(this).val() || [];
        const stageId = row.data("stage-id");
        const stageIndex = self.stagesList.findIndex(
          (stage) => stage.id === stageId
        );
        if (stageIndex !== -1) {
          self.stagesList[stageIndex].next_stages = selectedNextStages
            .map((id) => {
              const stage = sortedStages.find((s) => s.id == id);
              return stage ? { id: stage.id, name: stage.name } : null;
            })
            .filter(Boolean);
        }
        console.log("Updated next_stages:", self.stagesList);
      });

      if (showRateAndTime) {
        self.calculateTotalTime();
      }

      return row;
    },
    deleteStage: function (stageId) {
      const self = this;
      if (self.mode != "Edit") {
        app.helper
          .showConfirmationBox({
            message: "Bạn có chắc chắn muốn xóa bước này?",
          })
          .then(function () {
            // Remove from stagesList array
            self.stagesList = self.stagesList.filter(
              (stage) => stage.id != stageId
            );

            // Remove from DOM
            jQuery(`tr[data-stage-id="${stageId}"]`).remove();

            app.helper.showSuccessNotification({
              message: "Đã xóa bước thành công",
            });
            self.calculateTotalTime();
            self.updateNextStagesOptions();
            console.log("Updated stagesList after delete:", self.stagesList);
          });
      } else {
        // console.log("Pipelineid", self.pipelineId);
        app.helper.hideModal();

        app.helper.showProgress();
        let params = {
          module: "PipelineConfig",
          parent: "Settings",
          view: "EditPipelineAjax",
          mode: "getDeleteStageModal",
          idPipeline: self.pipelineId,
        };
        app.request.post({ data: params }).then((err, res) => {
          app.helper.hideProgress();

          if (err) {
            app.helper.showErrorNotification({ message: err.message });
            return;
          }

          // console.log("Hello", stage.name);
          app.helper.showModal(res, {
            preShowCb: function (modal) {
              const form = modal.find("form#add-stage-pipeline-new");
              form.find('[name="color"]').customColorPicker();
              const stage = self.stagesList.find((item) => item.id === stageId);
              console.log("Id stage", stage.id);
              jQuery('input[name="name_stage_delete_hidden"]').val(stage.id);
              jQuery("#name_stage_delete").val(stage.name);
            },
          });
        });
      }
    },
    registerCancelButtonClick: function () {
      jQuery(document).on(
        "click",
        "#cancelButton, .cancelButton",
        function (e) {
          e.preventDefault();
          app.helper.hideModal();
          window.location.href =
            "index.php?parent=Settings&module=PipelineConfig&view=Config&block=9&fieldid=67";
        }
      );
    },
    registerModuleChangeEvent: function () {
      let self = this;

      jQuery("#listModule").on("change", (e) => {
        // alert(self.isFirstOpen);

        const newModule = jQuery(e.currentTarget).val();
        const confirmMessage =
          "Thay đổi module sẽ xóa tất cả các bước hiện tại. Bạn có chắc chắn muốn thực hiện?";
        // alert(self.isFirstOpen);

        if (self.mode !== "Edit" || self.isFirstOpen !== 1) {
          app.helper
            .showConfirmationBox({ message: confirmMessage })
            .then(function () {
              self.currentNameModule = newModule;
              jQuery("#stagesTable tbody").empty();
              self.stagesList = [];
              self.calculateTotalTime();

              app.helper.showSuccessNotification({
                message: "Đã thay đổi module thành công",
              });

              self.handleColumnVisibility();
              self.handleColumnVisibilityTimePipeline();
              console.log(self.stagesList);
            });
        }

        if (self.mode === "Edit" && self.isFirstOpen === 1) {
          self.isFirstOpen = 0;
        }
      });
    },
    registerAddStageNewSaveEvent: function () {
      //Update by The Vi 3/4/2025
      let self = this;
      jQuery(document).on(
        "submit",
        "form#add-stage-pipeline-new",
        function (e) {
          e.preventDefault();
          const form = jQuery(this);
          const formData = form.serializeFormData();
          // Validate required fields
          let errors = [];

          if (
            !formData.itemLabelDisplayVn ||
            formData.itemLabelDisplayVn.trim() === ""
          ) {
            errors.push("Vui lòng nhập nhãn hiển thị Tiếng Việt");
          }

          if (
            !formData.itemLabelDisplayEn ||
            formData.itemLabelDisplayEn.trim() === ""
          ) {
            errors.push("Vui lòng nhập nhãn hiển thị Tiếng Anh");
          }

          if (!formData.newValue || formData.newValue.trim() === "") {
            errors.push("Vui lòng nhập giá trị");
          }

          if (!formData.color || formData.color.trim() === "") {
            errors.push("Vui lòng chọn màu");
          }
          if (errors.length > 0) {
            const errorMessage = errors.join("<br>");
            app.helper.showErrorNotification({
              message: errorMessage,
            });
            return false;
          }
          app.helper.showProgress();
          let params = {
            module: "PipelineConfig",
            parent: "Settings",
            action: "SaveEdit",
            mode: "addStagePipelineNew",
            picklistName: self.getPicklistname(),
            source_module: self.currentNameModule,
            selectedColor: formData.color,
            rolesSelected: ["H2"],
            newValue: formData.newValue,
            itemLabelDisplayEn: formData.itemLabelDisplayEn,
            itemLabelDisplayVn: formData.itemLabelDisplayVn,
          };
          app.request.post({ data: params }).then((err, response) => {
            app.helper.hideProgress();
            if (err) {
              app.helper.showErrorNotification({
                message: err.message || "Có lỗi xảy ra khi thêm giai đoạn mới",
              });
              return;
            }
            if (response && response.picklistValueId) {
              app.helper.showSuccessNotification({
                message: "Thêm giai đoạn mới thành công",
              });
            } else {
              const errorMsg =
                (response && response.message) ||
                "Thêm giai đoạn mới không thành công";
              app.helper.showErrorNotification({
                message: errorMsg,
              });
            }
          });
          app.helper.hideModal();
          return false;
        }
      );
    },
    calculateTotalTime: function () {
      let totalDays = 0;

      this.stagesList.forEach((stage) => {
        const timeValue = parseFloat(stage.execution_time.value) || 0;
        const timeUnit = stage.execution_time.unit;

        switch (timeUnit) {
          case "Year":
          case "Năm":
            totalDays += timeValue * 365;
            break;
          case "Month":
          case "Tháng":
            totalDays += timeValue * 30;
            break;
          case "Day":
          case "Ngày":
          default:
            totalDays += timeValue;
            break;
        }
      });

      jQuery(".toal-time-pipeline").text(Math.round(totalDays) + " ngày");
    },
    //End The Vi
    //================================================================
    //Begin Tran Dien
    renderStagesInfo: function (stagesList) {
      let self = this;
      const stepInfo = document.querySelector(".stepInfo");
      stagesList.forEach((stage) => {
        const stepItem = document.createElement("div");
        stepItem.classList.add("stepItem");

        const actionBox = document.createElement("div");
        actionBox.classList.add("action-box");
        actionBox.dataset.stageid = stage.id;
        actionBox.innerHTML = `<button type="button" class="btn text-primary btnAddAction" data-stageid="${stage.id}">+ Thêm thiết lập hành động</button>`;

        const conditionBox = document.createElement("div");
        conditionBox.classList.add("condition-box");
        conditionBox.dataset.stageid = stage.id;
        conditionBox.innerHTML = `<button type="button" class="btn text-primary btnAddCondition" data-stageid="${stage.id}">+ Thêm điều kiện</button>`;

        stepItem.appendChild(actionBox);
        stepItem.appendChild(conditionBox);
        stepInfo.appendChild(stepItem);
        if (stage.actions) {
          stage.actions.forEach((action) => {
            self.pushAction(action, true);
          });
        }
        let filterInfo = {};
        if (stage.conditions) {
          filterInfo = stage.conditions;
        }
        const condition = {
          stageid: stage.id,
          filterInfo: filterInfo,
        };
        self.pushCondition(condition);
      });
    },

    renderStageCrumbs: function (stagesList) {
      if (!stagesList || stagesList.length === 0) return;

      let ul = document.createElement("ul");
      ul.classList.add("crumbs");

      stagesList.forEach((stage, index) => {
        let stepClass = index % 2 === 0 ? "stepEven" : "stepOdd";
        let li = document.createElement("li");
        li.classList.add("step", stepClass);
        li.style.zIndex = stagesList.length - index;

        let a = document.createElement("a");
        a.href = "javascript:void(0)";

        let stepNum = document.createElement("span");
        stepNum.classList.add("stepNum");
        stepNum.textContent = index + 1;

        let stepText = document.createElement("span");
        stepText.classList.add("stepText");
        stepText.textContent = stage.name;

        a.appendChild(stepNum);
        a.appendChild(stepText);
        li.appendChild(a);
        ul.appendChild(li);
      });

      const breadcrumb = document.getElementById("breadcrumb");
      if (breadcrumb.firstChild) {
        breadcrumb.insertBefore(ul, breadcrumb.firstChild);
      } else {
        breadcrumb.appendChild(ul);
      }
    },
    registerAddActionSettingModal: function (form) {
      var self = this;
      form.on("click", ".btnAddAction", function () {
        console.log("Show add action setting modal");
        let actionJson = this.dataset.action;
        let actionData = actionJson ? JSON.parse(this.dataset.action) : null;
        console.log(actionData);
        var addActionSettingModalController =
          new Settings_PipelineConfig_AddActionSetting_Js();

        addActionSettingModalController.showAddActionSettingModal(
          this,
          self,
          actionData
        );
      });
    },

    registerAddCondition: function (form) {
      var self = this;
      form.on("click", ".btnAddCondition", function () {
        console.log("Show add action setting modal");
        self.showAddConditionModal(this);
      });
    },

    registerRemoveCondition: function (form) {
      var self = this;
      form.on("click", ".removeCondition", function (e) {
        e.stopPropagation();
        let stageId = jQuery(this).closest(".action-item").data("stageid");

        // Tìm stage trong danh sách và đặt conditions thành {}
        let stage = self.stagesList.find((stage) => stage.id === stageId);
        if (stage) {
          stage.conditions = {};
        }

        // Cập nhật giao diện về nút "Thêm điều kiện"
        let conditionBox = jQuery(`.condition-box[data-stageid='${stageId}']`);
        conditionBox.html(`
            <button type="button" class="btn text-primary btnAddCondition" data-stageid="${stageId}">+ Thêm điều kiện</button>
        `);

        console.log("StagesList after remove condition:", self.stagesList);
      });
    },

    showAddConditionModal: function (targetBtn) {
      console.log("SHOW ADD CONDITION MODAL");
      var self = this;
      app.helper.showProgress();
      if (targetBtn.dataset.conditions) {
        var conditions = JSON.parse(targetBtn.dataset.conditions);
        var filterInfo = conditions["filterInfo"];
      } else
        var filterInfo = {
          1: {
            columns: {},
            condition: "and",
          },
          2: {
            columns: {},
          },
        };
      // Request modal content
      console.log(filterInfo);
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        view: "EditPipelineAjax",
        mode: "getAddConditionModal",
        conditions: filterInfo,
        currentNameModule: self.currentNameModule,
      };
      app.request.post({ data: params }).then((err, res) => {
        app.helper.hideProgress();
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }
        // Show modal
        app.helper.showModal(res, {
          cb: function (modal) {
            var form = modal.find(".transitionConditionForm");
            var controller = Vtiger_Edit_Js.getInstance();
            controller.registerBasicEvents(form);
            vtUtils.applyFieldElementsView(form);
            var addConditionController =
              new Settings_PipelineConfig_AddCondition_Js(modal);
            var params = {
              submitHandler: function (form) {
                var form = jQuery(form);
                //handled advanced filters saved values.
                var advfilterlist = addConditionController.getValues();
                console.log(advfilterlist);
                var stageId = $(targetBtn).data("stageid");
                let condition = {
                  stageid: stageId,
                  filterInfo: advfilterlist,
                };
                self.pushCondition(condition);
                app.helper.hideModal();
                return false;
              },
            };
            form.vtValidate(params);
            form.find(".select2").each(function () {
              if (!jQuery(this).data("select2")) {
                jQuery(this).select2();
              }
            });

            modal.find(".addAndConditionBtn").on("click", function () {
              app.helper.showProgress();
              var params = {
                module: "PipelineConfig",
                parent: "Settings",
                view: "PipelineAndConditionRowAjax",
                currentNameModule: self.currentNameModule,
              };
              app.request.post({ data: params }).then(function (error, result) {
                app.helper.hideProgress();

                if (error) {
                  var errorMsg = app.vtranslate(
                    "JS_ADD_NEW_CONDITION_ERROR_MSG"
                  );
                  app.helper.showErrorNotification({ message: errorMsg });
                  return;
                }
                // Show result
                var tempContainer = jQuery("<div></div>").html(result);
                modal.find("#newAndRow").append(tempContainer.children());
                modal.find("#newAndRow .select2").each(function () {
                  if (jQuery(this).find("option").length > 0) {
                    vtUtils.showSelect2ElementView(jQuery(this));
                  }
                });
              });
              return false; // Prevent submit button to reload the page
            });
            modal.find(".addOrConditionBtn").on("click", function () {
              app.helper.showProgress();
              var params = {
                module: "PipelineConfig",
                parent: "Settings",
                view: "PipelineOrConditionRowAjax",
                currentNameModule: self.currentNameModule,
              };
              app.request.post({ data: params }).then(function (error, result) {
                app.helper.hideProgress();

                if (error) {
                  var errorMsg = app.vtranslate(
                    "JS_ADD_NEW_CONDITION_ERROR_MSG"
                  );
                  app.helper.showErrorNotification({ message: errorMsg });
                  return;
                }
                // Show result
                var tempContainer = jQuery("<div></div>").html(result);
                modal.find("#newOrRow").append(tempContainer.children());
                modal.find("#newOrRow .select2").each(function () {
                  if (jQuery(this).find("option").length > 0) {
                    vtUtils.showSelect2ElementView(jQuery(this));
                  }
                });
                modal.find(".fa-trash-alt").on("click", function () {
                  console.log("Vao fa-trash-alt");
                  jQuery(this).closest("div.conditionRow").remove();
                });
              });
              return false; // Prevent submit button to reload the page
            });
          },
        });
      });
    },

    doOperation: function (url) {
      var aDeferred = new jQuery.Deferred();
      app.helper.showProgress();
      app.request.get({ url: url }).then(function (error, data) {
        app.helper.hideProgress();
        aDeferred.resolve(data);
      });

      return aDeferred.promise();
    },

    //End Tran Dien
    getForm: function () {
      return $("form#editPipeline");
    },
    getFormModal: function () {
      return $("form#add-stage-pipeline");
    },
  }
);
//Begin Tran Dien
CustomView_BaseController_Js(
  "Settings_PipelineConfig_AddActionSetting_Js",
  {},
  {
    registerEvents: function () {
      this._super();
      this.registerEventFormInit();
    },

    registerEventFormInit: function () {
      let self = this;
      let modal = this.getModal();
    },

    showAddActionSettingModal: function (
      targetBtn,
      targetController,
      action = null
    ) {
      var self = this;
      app.helper.showProgress();
      // Request modal content
      var stageId = $(targetBtn).data("stageid");
      console.log(`Action: ${action}`);
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        view: "EditPipelineAjax",
        mode: "getAddActionSettingModal",
        actionInfo: action,
      };
      app.request.post({ data: params }).then((err, res) => {
        app.helper.hideProgress();
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }
        // Show add action setting modal
        app.helper.showModal(res, {
          cb: function (modal) {
            modal.find("#addActionSettingModal").removeClass("hide");
            var form = modal.find(".addActionSettingForm");
            var controller = Vtiger_Edit_Js.getInstance();
            controller.registerBasicEvents(form);
            vtUtils.applyFieldElementsView(form);

            // Form validation
            var params = {
              submitHandler: function (form) {
                var form = jQuery(form);
                var params = form.serializeFormData();
                // Kiểm tra nếu action_time_type là "scheduled" thì bắt buộc nhập time
                if (params.action_time_type === "scheduled" && !params.time) {
                  app.helper.showErrorNotification({
                    message: "Vui lòng nhập giá trị thời gian!",
                  });
                  return false;
                }
                var action = {
                  stageId: stageId,
                  frequency: params.frequency,
                  action_time_type: params.action_time_type,
                  time: params.time,
                  time_unit: params.time_unit,
                };
                var actionSettingController =
                  new Settings_PipelineConfig_ActionSetting_Js();
                actionSettingController.showActionSettingModal(
                  targetBtn,
                  targetController,
                  action
                );
                return false;
              },
            };
            form.vtValidate(params);
            form.find(".select2").each(function () {
              if (!jQuery(this).data("select2")) {
                jQuery(this).select2();
              }
            });
            // Handle changing event for time of execution
            modal.find("#action_time_type").on("change", function () {
              if (jQuery(this).val() === "scheduled") {
                modal.find("#scheduled_fields").removeClass("hide");
              } else {
                modal.find("#scheduled_fields").addClass("hide");
              }
            });
          },
        });
      });
    },

    getModal: function () {
      return $("#addActionSettingModal");
    },
  }
);

CustomView_BaseController_Js(
  "Settings_PipelineConfig_ActionSetting_Js",
  {},
  {
    targetController: null,

    // Initialize CKEditor instance
    ckEditorInstance: false,
    currentNameModule: "Potentials",
    action: {},
    validationSupportedFieldConditionMap: {
      email: ["e", "n"],
    },
    //Hols field type for which there is validations always needed
    allConditionValidationNeededFieldList: ["double", "integer", "currency"],
    fieldModelInstance: false,
    dateConditionInfo: false,
    isEdit: false,
    fieldValueMap: false,

    setCurrentNameModule: function (moduleName) {
      this.currentNameModule = moduleName;
    },

    getDateSpecificConditionInfo: function () {
      return this.dateConditionInfo;
    },

    getFieldValueMapping: function () {
      var fieldValueMap = this.fieldValueMap;
      if (fieldValueMap != false) {
        return fieldValueMap;
      } else {
        return "";
      }
    },

    registerEvents: function () {
      this._super();
      this.registerEventFormInit();

      this.registerFillContentEvent(
        "#form-send-email",
        ".task-fields",
        ".fields"
      );
      this.registerFillContentEvent(
        "#form-send-email",
        "#task-fieldnames,#task_timefields,#task-templates,#task-emailtemplates",
        "content",
        true
      );
      this.registerFillContentEvent(
        "#form-send-sms",
        ".task-fields",
        'input[name="sms_recepient"]'
      );
      this.registerFillContentEvent(
        "#form-send-sms",
        "#task-fieldnames",
        'textarea[name="content"]'
      );
    },

    registerEventFormInit: function () {
      let self = this;
      let modal = this.getModal();

      modal.find("#addCall").on("click", function () {
        self.showAddCallModal(this);
      });

      modal.find("#addMeeting").on("click", function () {
        self.showAddMeetingModal(this);
      });

      modal.find("#createNewTask").on("click", function () {
        self.showCreateNewTaskModal(this);
      });

      modal.find("#createNewProjectTask").on("click", function () {
        self.showCreateNewProjectTaskModal(this);
      });

      modal.find("#createNewRecord").on("click", function () {
        self.showCreateNewRecordModal(this);
      });

      modal.find("#updateDataField").on("click", function () {
        self.showDataFieldUpdateModal(this);
        self.registerShowDataUpdateFieldsModalEvents();
      });

      modal.find("#sendZNSMessage").on("click", function () {
        self.showSendZNSModal(this);
      });

      modal.find("#sendSMSMessage").on("click", function () {
        self.showSendSMSModal(this);
      });

      modal.find("#sendEmail").on("click", function () {
        self.showSendEmailModal(this);
      });

      modal.find("#addNotification").on("click", function () {
        self.showNotificationModal(this);
      });
    },

    initialize: function () {
      // this.changeFieldElementsView(this.getFilterContainer());
      this.initializeOperationMappingDetails();
      // this.loadFieldSpecificUiForAll();
    },

    registerShowDataUpdateFieldsModalEvents: function () {
      this.registerFieldChange();
    },

    /**
     * Function which will save the field condition mapping condition label mapping
     */
    initializeOperationMappingDetails: function () {
      var filterContainer = jQuery("#form-update-data-field");
      this.fieldTypeConditionMapping = jQuery(
        'input[name="advanceFilterOpsByFieldType"]',
        filterContainer
      ).data("value");
      this.conditonOperatorLabelMapping = jQuery(
        'input[name="advanceFilterOptions"]',
        filterContainer
      ).data("value");
      this.dateConditionInfo = jQuery('[name="date_filters"]').data("value");
      console.log(jQuery('[name="date_filters"]').data("value"));
      return this;
    },

    registerFieldChange: function () {
      var self = this;
      $(document).on(
        "change",
        '.updateDataFieldModal select[name="column_name"]',
        function (e, data) {
          var currentElement = $(e.currentTarget);
          self.loadFieldSpecificUi(currentElement);
        }
      );
    },

    loadFieldSpecificUi: function (fieldSelect) {
      var selectedOption = fieldSelect.find("option:selected");
      var row = fieldSelect.closest("div.fieldRow");
      var fieldUiHolder = row.find(".fieldUiHolder");
      var fieldInfo = selectedOption.data("fieldinfo");

      var fieldType = "string";
      if (typeof fieldInfo != "undefined") {
        fieldType = fieldInfo.type;
      }
      fieldInfo.comparatorElementVal = "e";
      if (fieldType == "date" || fieldType == "datetime") {
        fieldInfo.dateSpecificConditions = this.getDateSpecificConditionInfo();
      }
      var moduleName = "AdvanceFilter";
      var fieldModel = Vtiger_Field_Js.getInstance(fieldInfo, moduleName);
      this.fieldModelInstance = fieldModel;

      var fieldSpecificUi =
        this.getFieldSpecificUiOnUpdateDataField(fieldSelect);

      //remove validation since we dont need validations for all eleements
      // Both filter and find is used since we dont know whether the element is enclosed in some conainer like currency
      var fieldName = fieldModel.getName();
      if (fieldModel.getType() == "multipicklist") {
        fieldName = fieldName + "[]";
      }
      if (
        (fieldModel.getType() == "picklist" ||
          fieldModel.getType() == "owner") &&
        fieldSpecificUi.is("select")
      ) {
        fieldName = fieldName + "[]";
      }

      if (fieldSpecificUi.find(".add-on").length > 0) {
        fieldSpecificUi.filter(".input-append").addClass("row-fluid");
        fieldSpecificUi.find(".input-append").addClass("row-fluid");
        fieldSpecificUi.filter(".input-prepend").addClass("row-fluid");
        fieldSpecificUi.find(".input-prepend").addClass("row-fluid");
        fieldSpecificUi.find('input[type="text"]').css("width", "79%");
      } else {
        fieldSpecificUi
          .filter('[name="' + fieldName + '"]')
          .addClass("row-fluid");
        fieldSpecificUi
          .find('[name="' + fieldName + '"]')
          .addClass("row-fluid");
      }

      fieldSpecificUi
        .filter('[name="' + fieldName + '"]')
        .attr("data-value", "value")
        .removeAttr("data-validation-engine")
        .addClass("ignore-validation");
      fieldSpecificUi
        .find('[name="' + fieldName + '"]')
        .attr("data-value", "value")
        .removeAttr("data-validation-engine")
        .addClass("ignore-validation");

      if (fieldModel.getType() == "currency") {
        fieldSpecificUi
          .filter('[name="' + fieldName + '"]')
          .attr("data-decimal-separator", fieldInfo.decimal_separator)
          .attr("data-group-separator", fieldInfo.group_separator);
        fieldSpecificUi
          .find('[name="' + fieldName + '"]')
          .attr("data-decimal-separator", fieldInfo.decimal_separator)
          .attr("data-group-separator", fieldInfo.group_separator);
      }
      fieldUiHolder.html(fieldSpecificUi);
      fieldSpecificUi = jQuery(fieldSpecificUi[0]); // Added by Hieu Nguyen on 2020-12-16 to fix bug multi-select field cause js error

      if (fieldSpecificUi.is("input.select2")) {
        var tagElements = fieldSpecificUi.data("tags");
        var params = { tags: tagElements, tokenSeparators: [","] };
        vtUtils.showSelect2ElementView(fieldSpecificUi, params);
      } else if (fieldSpecificUi.is("select")) {
        if (fieldSpecificUi.hasClass("chzn-select")) {
          app.changeSelectElementView(fieldSpecificUi);
        } else {
          vtUtils.showSelect2ElementView(fieldSpecificUi);
        }
      } else if (fieldSpecificUi.has("input.dateField").length > 0) {
        vtUtils.registerEventForDateFields(fieldSpecificUi);
      } else if (fieldSpecificUi.has("input.timepicker-default").length > 0) {
        vtUtils.registerEventForTimeFields(fieldSpecificUi);
      }
      this.addValidationToFieldIfNeeded(fieldSelect);

      // Added by Hieu Nguyen on 2019-06-13 to render owner and main owner field
      var singleOwner = moduleName == "Workflows" ? true : false;

      if (
        fieldModel.getName() == "assigned_user_id" ||
        fieldModel.getName() == "main_owner_id"
      ) {
        var selectedTags = "";
        if (fieldModel.get("selected_tags")) {
          selectedTags = JSON.stringify(fieldModel.get("selected_tags"));
        }
        fieldSpecificUi =
          '<input type="text" name="' +
          fieldModel.getName() +
          '" value="' +
          fieldModel.getValue() +
          "\" data-selected-tags='" +
          selectedTags +
          '\' data-value="value" class="form-control">';

        fieldUiHolder.html(fieldSpecificUi);

        // Init select2
        var input = fieldUiHolder.find(
          'input[name="' + fieldModel.getName() + '"]'
        );
        input.data("singleSelection", singleOwner);
        CustomOwnerField.initCustomOwnerFields(input);
      }
      // End Hieu Nguyen

      // Added by Hieu Nguyen on 2019-10-22 to render user reference field
      if (
        fieldModel.getName() == "createdby" ||
        fieldModel.getName() == "modifiedby" ||
        fieldInfo.column == "inventorymanager"
      ) {
        var selectedTags = "";
        if (fieldModel.get("selected_tags")) {
          selectedTags = JSON.stringify(fieldModel.get("selected_tags"));
        }
        fieldSpecificUi =
          '<input type="text" name="' +
          fieldModel.getName() +
          '" value="' +
          fieldModel.getValue() +
          "\" data-selected-tags='" +
          selectedTags +
          '\' data-value="value" class="form-control">';
        fieldUiHolder.html(fieldSpecificUi);
        // Init select2
        var input = fieldUiHolder.find(
          'input[name="' + fieldModel.getName() + '"]'
        );
        input.data("singleSelection", singleOwner);
        CustomOwnerField.initCustomOwnerFields(input);
      }
      // End Hieu Nguyen

      // Added by Hieu Nguyen on 2021-12-21 to update field info value to fix bug filter input re-rendered with the old value
      fieldUiHolder.find(":input").on("change", function () {
        fieldInfo.value = $(this).val();
      });
      // End Hieu Nguyen

      return this;
    },

    getFieldSpecificUiOnUpdateDataField: function (fieldSelectElement) {
      var selectedOption = fieldSelectElement.find("option:selected");
      var fieldModel = this.fieldModelInstance;
      console.log(fieldModel.getType());
      if (fieldModel.getType().toLowerCase() == "boolean") {
        console.log("TYPE: BOOLEAN");
        var conditionRow = fieldSelectElement.closest(".fieldRow");
        var selectedValue = conditionRow.find('[data-value="value"]').val();
        var html =
          '<select class="select2 col-lg-12" name="' +
          fieldModel.getName() +
          '">';
        html += '<option value="0"';
        if (selectedValue == "0") {
          html += ' selected="selected" ';
        }
        html += ">" + app.vtranslate("JS_IS_DISABLED") + "</option>";

        html += '<option value="1"';
        if (selectedValue == "1") {
          html += ' selected="selected" ';
        }
        html += ">" + app.vtranslate("JS_IS_ENABLED") + "</option>";
        html += "</select>";
        return jQuery(html);
      } else if (fieldModel.getType().toLowerCase() == "reference") {
        console.log("TYPE: REFERRENCE");
        var html =
          '<input class="inputElement" type="text" name="' +
          fieldModel.getName() +
          '" data-label="' +
          fieldModel.get("label") +
          '" data-rule-' +
          fieldModel.getType() +
          "=true />";
        html = jQuery(html).val(app.htmlDecode(fieldModel.getValue()));
        return jQuery(html);
      } else {
        console.log("ELSE");
        const fieldHtml = jQuery(fieldModel.getUiTypeSpecificHtml());
        return jQuery(fieldModel.getUiTypeSpecificHtml());
      }
    },

    /**
     * Function to add the validation if required
     * @prarms : selectFieldElement - select element which will represents field list
     */
    addValidationToFieldIfNeeded: function (selectFieldElement) {
      var selectedOption = selectFieldElement.find("option:selected");
      var row = selectFieldElement.closest("div.fieldRow");
      var fieldSpecificElement = row.find('[data-value="value"]');
      var validator = selectedOption.attr("data-validator");

      if (this.isFieldSupportsValidation(selectFieldElement)) {
        //data attribute will not be present while attaching validation engine events . so we are
        //depending on the fallback option which is class
        //TODO : remove the hard coding and get it from field element data-validation-engine
        fieldSpecificElement
          .addClass(
            "validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
          )
          .attr(
            "data-validation-engine",
            "validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
          )
          .attr(
            "data-fieldinfo",
            JSON.stringify(selectedOption.data("fieldinfo"))
          );
        if (typeof validator != "undefined") {
          fieldSpecificElement.attr("data-validator", validator);
        }
        fieldSpecificElement.removeClass("ignore-validation");
      } else {
        fieldSpecificElement
          .removeClass(
            "validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
          )
          .removeAttr("data-validation-engine")
          .removeAttr("data-fieldinfo");
        fieldSpecificElement.addClass("ignore-validation");
      }
      return this;
    },

    /**
     * Check if field supports validation
     * @prarms : selectFieldElement - select element which will represents field list
     * @return - boolen true/false
     */
    isFieldSupportsValidation: function (fieldSelect) {
      var fieldModel = this.fieldModelInstance;
      var type = fieldModel.getType();

      if (
        jQuery.inArray(type, this.allConditionValidationNeededFieldList) >= 0
      ) {
        return true;
      }
      var conditionValue = "e";

      if (type in this.validationSupportedFieldConditionMap) {
        if (
          jQuery.inArray(
            conditionValue,
            this.validationSupportedFieldConditionMap[type]
          ) >= 0
        ) {
          return true;
        }
      }
      return false;
    },

    isEmptyFieldSelected: function (fieldSelect) {
      var selectedOption = fieldSelect.find("option:selected");
      //assumption that empty field will be having value none
      if (selectedOption.val() == "none") {
        return true;
      }
      return false;
    },

    getValuesFromDataFieldUpdateModal: function () {
      var self = this;
      var container = jQuery(".newDataField");

      var fieldList = new Array("column_name", "value");

      var values = {};
      var columnIndex = 0;
      var fieldRows = jQuery(".fieldRow", container);

      fieldRows.each(function (i, fieldRowDomElement) {
        var rowElement = jQuery(fieldRowDomElement);
        var fieldSelectElement = jQuery('[name="column_name"]', rowElement);
        var valueSelectElement = jQuery('[data-value="value"]', rowElement);
        //To not send empty fields to server
        if (self.isEmptyFieldSelected(fieldSelectElement)) {
          return true;
        }
        var fieldDataInfo = fieldSelectElement
          .find("option:selected")
          .data("fieldinfo");
        var fieldType = fieldDataInfo.type;
        var rowValues = {};
        if (
          fieldType == "owner" ||
          fieldType == "ownergroup" ||
          jQuery.inArray(fieldDataInfo.name, ["createdby", "modifiedby"]) >=
            0 ||
          fieldDataInfo.column == "inventorymanager"
        ) {
          for (var key in fieldList) {
            var field = fieldList[key];
            if (field == "value" && valueSelectElement.is("select")) {
              var selectedOptions = valueSelectElement.find("option:selected");
              var newvaluesArr = [];
              jQuery.each(selectedOptions, function (i, e) {
                newvaluesArr.push(jQuery.trim(jQuery(e).text()));
              });
              if (selectedOptions.length == 0) {
                rowValues[field] = "";
              } else {
                rowValues[field] = newvaluesArr.join(",");
              }
            } else if (field == "value" && valueSelectElement.is("input")) {
              rowValues[field] = valueSelectElement.val();
            } else {
              rowValues[field] = jQuery(
                '[name="' + field + '"]',
                rowElement
              ).val();
            }
          }
        } else if (fieldType == "picklist" || fieldType == "multipicklist") {
          for (var key in fieldList) {
            var field = fieldList[key];
            if (field == "value" && valueSelectElement.is("input")) {
              var commaSeperatedValues = valueSelectElement.val();
              var pickListValues = valueSelectElement.data("picklistvalues");
              var valuesArr = commaSeperatedValues.split(",");
              var newvaluesArr = [];
              for (i = 0; i < valuesArr.length; i++) {
                if (typeof pickListValues[valuesArr[i]] != "undefined") {
                  newvaluesArr.push(pickListValues[valuesArr[i]]);
                } else {
                  newvaluesArr.push(valuesArr[i]);
                }
              }
              var reconstructedCommaSeperatedValues = newvaluesArr.join(",");
              rowValues[field] = reconstructedCommaSeperatedValues;
            } else if (
              field == "value" &&
              valueSelectElement.is("select") &&
              fieldType == "picklist"
            ) {
              var value = valueSelectElement.val();
              if (value == null) {
                rowValues[field] = value;
              } else {
                rowValues[field] = value.join(",");
              }
            } else if (
              field == "value" &&
              valueSelectElement.is("select") &&
              fieldType == "multipicklist"
            ) {
              var value = valueSelectElement.val();
              if (value == null) {
                rowValues[field] = value;
              } else {
                rowValues[field] = value.join(",");
              }
            } else {
              console.log("GAN VALUE");
              rowValues[field] = jQuery(
                '[name="' + field + '"]',
                rowElement
              ).val();
            }
          }
        }
        // Added by Hieu Nguyen on 2021-01-26 to support tags field (NOTE: Currently for tags field in MAIN MODULE ONLY!)
        else if (fieldType == "tags") {
          for (var key in fieldList) {
            var field = fieldList[key];

            if (field == "value") {
              var selectedTags = valueSelectElement.val();
              rowValues[field] = selectedTags ? selectedTags.join(",") : null;
            } else {
              rowValues[field] = jQuery(
                '[name="' + field + '"]',
                rowElement
              ).val();
            }
          }
        }
        // End Hieu Nguyen
        else {
          for (var key in fieldList) {
            var field = fieldList[key];

            // Modified by Hieu Nguyen on 2021-05-19 to get unformatted value of numeric fields
            if (field == "value") {
              var value = valueSelectElement.val();

              if (
                fieldType == "integer" ||
                field == "decimal" ||
                fieldType == "currency" ||
                fieldType == "double"
              ) {
                // Modified by Phu Vo on 2021.06.14 to add type double
                value = app.unformatCurrencyToUser(value);
              }

              rowValues[field] = value;
            }
            // End Hieu Nguyen
            else {
              rowValues[field] = jQuery(
                '[name="' + field + '"]',
                rowElement
              ).val();
            }
          }
        }
        values[columnIndex] = rowValues;
        columnIndex++;
      });
      return values;
    },

    // Add by Minh Hoàng
    /**
     * Function to get ckEditorInstance
     */
    getckEditorInstance: function () {
      if (this.ckEditorInstance == false) {
        this.ckEditorInstance = new Vtiger_CkEditor_Js();
      }
      return this.ckEditorInstance;
    },

    preSaveVTEmailTask: function (tasktype) {
      var textAreaElement = jQuery("#content");
      //To keep the plain text value to the textarea which need to be
      //sent to server
      textAreaElement.val(CKEDITOR.instances["content"].getData());
    },

    /**
     * Function to fill the values ​​into the input box when selecting the options of Select
     */
    registerFillTaskFromEmailFieldEvent: function () {
      jQuery("#form-send-email").on("change", "#fromEmailOption", function (e) {
        var currentElement = jQuery(e.currentTarget);
        var inputElement = currentElement.closest(".row").find(".fields");

        inputElement.val(currentElement.val());
      });
    },

    registerFillContentEvent: function (
      formSelector,
      fieldSelector,
      targetSelector,
      isCKEditor = false
    ) {
      // Attach change event to the specified form and field
      jQuery(formSelector).on("change", fieldSelector, function (e) {
        var currentElement = jQuery(e.currentTarget);
        var value = currentElement.val();

        // If the target is a CKEditor instance
        if (isCKEditor) {
          var textarea = CKEDITOR.instances[targetSelector];

          if (textarea != undefined) {
            textarea.insertHtml(value);
          } else if (jQuery('textarea[name="' + targetSelector + '"]')) {
            var textArea = jQuery('textarea[name="' + targetSelector + '"]');

            textArea.insertAtCaret(value);
          }
        } else {
          // If the target is a regular input element
          var inputElement = currentElement
            .closest(".row")
            .find(targetSelector);

          if (currentElement.hasClass("overwriteSelection")) {
            inputElement.val(value);
          } else {
            var oldValue = inputElement.val();
            var newValue = oldValue + value;

            inputElement.val(newValue);
          }
        }
      });
    },

    registerTooltipEventForSignatureField: function () {
      jQuery("#signaturePopover").on("mouseover", function (e) {
        jQuery("#signaturePopover").popover({
          html: true,
        });
      });
    },

    registerToggleCheckboxEvent: function (form) {
      form.find("#toggleCheckbox").on("change", function () {
        if ($(this).is(":checked")) {
          form.find("#toggleContent").removeClass("hide").show();
        } else {
          form.find("#toggleContent").hide().addClass("hide");
        }
      });
    },

    // Add by Minh Hoang on 2025-01-23 to set validation rules for SMS content field
    validateContentSMSModal: function () {
      let contentInput = jQuery('[name="content"]');

      contentInput
        .closest("div")
        .prev("div")
        .append('<span class="redColor">*</span>');
      contentInput.attr("data-rule-required", "true");

      if (
        typeof _PROVIDER_INFO !== "undefined" &&
        !_PROVIDER_INFO["unicode_sms_supported"]
      ) {
        contentInput.attr("data-rule-asciiOnly", "true");
      }
    },

    /**
     * Function to get values from modals
     */
    getValuesFromSendSMSModal: function () {
      var values = {};
      var modal = jQuery("#form-send-sms");

      values.titleSMS = modal.find('input[name="titleSMS"]').val();
      values.sms_recepient = modal.find('input[name="sms_recepient"]').val();
      values.content = modal.find('textarea[name="content"]').val();

      return values;
    },

    getValuesFromSendEmailModal: function () {
      var values = {};
      var modal = jQuery("#form-send-email");

      values.titleEmail = modal.find('input[name="titleEmail"]').val();
      values.fromEmail = modal.find('input[name="fromEmail"]').val();
      values.replyTo = modal.find('input[name="replyTo"]').val();
      values.recepient = modal.find('input[name="recepient"]').val();
      values.emailcc = modal.find('input[name="emailcc"]').val();
      values.emailbcc = modal.find('input[name="emailbcc"]').val();
      values.subject = modal.find('input[name="subject"]').val();
      values.safe_content = modal
        .find('input[name="safe_content"]')
        .is(":checked")
        ? 1
        : 0;
      values.content = modal.find('textarea[name="content"]').val();

      return values;
    },

    /**
     * Function to show modals
     */
    showAddCallModal: function (targetBtn) {
      var self = this;
      app.helper.showProgress();
      // Request modal content
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        view: "EditPipelineAjax",
        mode: "getAddCallModal",
      };
      app.request.post({ data: params }).then((err, res) => {
        app.helper.hideProgress();
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }
        // Show modal
        app.helper.showModal(res, {
          preShowCb: function (modal) {
            modal.off("hidden.bs.modal");
          },
          cb: function (modal) {
            modal.css("display", "block");
            const form = modal.find("form#form-add-call");
            vtUtils.initDatePickerFields(form);
            self.registerToggleCheckboxEvent(form);
            CustomOwnerField.initCustomOwnerFields(
              form.find('input[name="assigned_user_id"]')
            );
            self.registerOwnerFieldEvent(form);
            $("#fullInfo").click(function () {
              $("#extraInfo").slideDown();
              $(this).hide();
            });
            function calculateEndTime() {
              let startTime = $("input[name='startTime']").val();
              let duration = parseInt($("input[name='duration']").val());
              let durationUnit = $("select[name='durationUnit']").val();
              let endTimeInput = $("input[name='endTime']");

              if (startTime && !isNaN(duration)) {
                let timeFormat =
                  startTime.includes("AM") || startTime.includes("PM")
                    ? "hh:mm A"
                    : "HH:mm";
                let momentStartTime = moment(startTime, timeFormat);

                if (durationUnit === "hours") {
                  momentStartTime.add(duration, "hours");
                } else {
                  momentStartTime.add(duration, "minutes");
                }

                let newEndTime = momentStartTime.format(timeFormat);
                endTimeInput.val(newEndTime);
              }
            }

            $(
              "input[name='startTime'], input[name='duration'], select[name='durationUnit']"
            ).on("change keyup", calculateEndTime);
            var controller = Vtiger_Edit_Js.getInstance();
            controller.registerBasicEvents(form);
            vtUtils.applyFieldElementsView(form);

            // Form validation
            var params = {
              submitHandler: function (form) {
                var form = jQuery(form);
                var params = form.serializeFormData();
                console.log(params);
                let callInfo = {
                  assigned_user_id: params.assigned_user_id,
                  assign_parent_record_owners:
                    params.assign_parent_record_owners
                      ? params.assign_parent_record_owners
                      : null,
                  description: params.description,
                  duration: parseInt(params.duration),
                  durationUnit: params.durationUnit,
                  endTime: params.endTime,
                  eventName: params.eventName,
                  events_call_direction: params.events_call_direction,
                  calendar_repeat_limit_date: self.convertDateFormat(
                    params.calendar_repeat_limit_date
                  ),
                  recurringCheck: params.recurringcheck,
                  recurringtype: params.recurringtype,
                  repeat_frequency: params.repeat_frequency
                    ? parseInt(params.repeat_frequency)
                    : null,
                  startDays: parseInt(params.startDate),
                  startDatefield: params.startDateField,
                  startDirection: params.startDirection,
                  startTime: params.startTime,
                  status: params.status,
                };
                self.action["callInfo"] = callInfo;
                self.action["action_name"] = params.action_name;
                self.action["action_type"] = "addCall";
                self.action["time"] = self.action["time"]
                  ? parseInt(self.action["time"])
                  : null;
                self.targetController.pushAction(self.action, self.isEdit);
                app.helper.hideModal();
                return false;
              },
            };
            form.vtValidate(params);
            form.find(".select2").each(function () {
              if (!jQuery(this).data("select2")) {
                jQuery(this).select2();
              }
            });
          },
        });
      });
    },

    convertDateFormat(dateString) {
      var parts = dateString.split("-");
      if (parts.length === 3) {
        return `${parts[2]}-${parts[1]}-${parts[0]}`; // yyyy-mm-dd
      }
      return "Invalid Date Format";
    },

    // Added by Hieu Nguyen on 2020-10-26 to support assign new record to parent record owners
    registerOwnerFieldEvent: function (container) {
      var assignedUsersInput = container.find('input[name="assigned_user_id"]');
      var assignParentRecordOwnersInput = container.find(
        'input[name="assign_parent_record_owners"]'
      );

      // Init at form load
      displayOnwerFieldStatus();

      // Init when checkbox change
      assignParentRecordOwnersInput.on("change", function () {
        displayOnwerFieldStatus();
      });

      function displayOnwerFieldStatus() {
        if (assignParentRecordOwnersInput.is(":checked")) {
          console.log(true);
          assignedUsersInput.select2("data", null).trigger("change");
          assignedUsersInput.select2("enable", false);
          assignedUsersInput.removeAttr("data-rule-required");
          vtUtils.hideValidationMessage(assignedUsersInput);
          assignedUsersInput
            .closest(".fieldValue")
            .find(".input-error")
            .removeClass("input-error");
        } else {
          console.log(false);
          assignedUsersInput.select2("enable", true);
          assignedUsersInput.attr("data-rule-required", "true");
        }
      }
    },

    showAddMeetingModal: function (targetBtn) {
      var self = this;
      app.helper.showProgress();
      // Request modal content
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        view: "EditPipelineAjax",
        mode: "getAddMeetingModal",
      };
      app.request.post({ data: params }).then((err, res) => {
        app.helper.hideProgress();
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }
        // Show modal
        app.helper.showModal(res, {
          preShowCb: function (modal) {
            modal.off("hidden.bs.modal");
          },
          cb: function (modal) {
            modal.css("display", "block");
            const form = modal.find("form#form-add-meeting");
            vtUtils.initDatePickerFields(form);
            // Register checkbox
            self.registerToggleCheckboxEvent(form);
            CustomOwnerField.initCustomOwnerFields(
              form.find('input[name="assigned_user_id"]')
            );
            self.registerOwnerFieldEvent(form);
            $("#fullInfo").click(function () {
              $("#extraInfo").slideDown();
              $(this).hide();
            });

            function calculateEndTime() {
              let startTime = $("input[name='startTime']").val();
              let duration = parseInt($("input[name='duration']").val());
              let durationUnit = $("select[name='durationUnit']").val();
              let endTimeInput = $("input[name='endTime']");

              if (startTime && !isNaN(duration)) {
                let timeFormat =
                  startTime.includes("AM") || startTime.includes("PM")
                    ? "hh:mm A"
                    : "HH:mm";
                let momentStartTime = moment(startTime, timeFormat);

                if (durationUnit === "hours") {
                  momentStartTime.add(duration, "hours");
                } else {
                  momentStartTime.add(duration, "minutes");
                }

                let newEndTime = momentStartTime.format(timeFormat);
                endTimeInput.val(newEndTime);
              }
            }

            // calculate end time when enter start time and duration
            $(
              "input[name='startTime'], input[name='duration'], select[name='durationUnit']"
            ).on("change keyup", calculateEndTime);
            var controller = Vtiger_Edit_Js.getInstance();
            controller.registerBasicEvents(form);
            vtUtils.applyFieldElementsView(form);

            // Form validation
            var params = {
              submitHandler: function (form) {
                var form = jQuery(form);
                var params = form.serializeFormData();
                console.log(params);
                let meetingInfo = {
                  assigned_user_id: params.assigned_user_id,
                  assign_parent_record_owners:
                    params.assign_parent_record_owners
                      ? params.assign_parent_record_owners
                      : null,
                  description: params.description,
                  duration: parseInt(params.duration),
                  durationUnit: params.durationUnit,
                  endTime: params.endTime,
                  eventName: params.eventName,
                  calendar_repeat_limit_date: self.convertDateFormat(
                    params.calendar_repeat_limit_date
                  ),
                  recurringcheck: params.recurringcheck,
                  recurringtype: params.recurringtype,
                  repeat_frequency: params.repeat_frequency
                    ? parseInt(params.repeat_frequency)
                    : null,
                  startDays: parseInt(params.startDays),
                  startDatefield: params.startDatefield,
                  startDirection: params.startDirection,
                  startTime: params.startTime,
                  status: params.status,
                };
                self.action["meetingInfo"] = meetingInfo;
                self.action["action_name"] = params.action_name;
                self.action["action_type"] = "addMeeting";
                self.action["time"] = self.action["time"]
                  ? parseInt(self.action["time"])
                  : null;
                self.targetController.pushAction(self.action, self.isEdit);
                app.helper.hideModal();
                return false;
              },
            };
            form.vtValidate(params);
            form.find(".select2").each(function () {
              if (!jQuery(this).data("select2")) {
                jQuery(this).select2();
              }
            });
          },
        });
      });
    },

    showCreateNewTaskModal: function (targetBtn) {
      var self = this;
      app.helper.showProgress();
      // Request modal content
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        view: "EditPipelineAjax",
        mode: "getCreateNewTaskModal",
        currentNameModule: self.currentNameModule,
      };
      app.request.post({ data: params }).then((err, res) => {
        app.helper.hideProgress();
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }
        // Show modal
        app.helper.showModal(res, {
          preShowCb: function (modal) {
            modal.off("hidden.bs.modal");
          },
          cb: function (modal) {
            modal.css("display", "block");
            var form = modal.find("#form-create-new-task");
            // Register checkbox
            self.registerToggleCheckboxEvent(form);
            CustomOwnerField.initCustomOwnerFields(
              form.find('input[name="assigned_user_id"]')
            );
            self.registerOwnerFieldEvent(form);
            $("#fullInfo").click(function () {
              $("#extraInfo").slideDown();
              $(this).hide();
            });
            var controller = Vtiger_Edit_Js.getInstance();
            controller.registerBasicEvents(form);
            vtUtils.applyFieldElementsView(form);

            // Form validation
            var params = {
              submitHandler: function (form) {
                var form = jQuery(form);
                var params = form.serializeFormData();
                console.log(params);
                let taskInfo = {
                  assigned_user_id: params.assigned_user_id,
                  assign_parent_record_owners:
                    params.assign_parent_record_owners ? 1 : null,
                  datefield: params.datefield,
                  days: parseInt(params.days),
                  description: params.description,
                  direction: params.direction,
                  priority: params.priority,
                  sendNotification: params.sendNotification ? 1 : 0,
                  status: params.status,
                  todo: params.todo,
                };
                self.action["taskInfo"] = taskInfo;
                self.action["action_name"] = params.action_name;
                self.action["action_type"] = "createNewTask";
                self.action["time"] = self.action["time"]
                  ? parseInt(self.action["time"])
                  : null;
                self.targetController.pushAction(self.action, self.isEdit);
                app.helper.hideModal();
                return false;
              },
            };
            form.vtValidate(params);
            form.find(".select2").each(function () {
              if (!jQuery(this).data("select2")) {
                jQuery(this).select2();
              }
            });
          },
        });
      });
    },

    showCreateNewProjectTaskModal: function (targetBtn) {
      app.helper.showProgress();
      // Request modal content
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        view: "EditPipelineAjax",
        mode: "getCreateNewProjectTaskModal",
      };
      app.request.post({ data: params }).then((err, res) => {
        app.helper.hideProgress();
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }
        // Show modal
        app.helper.showModal(res, {
          preShowCb: function (modal) {
            modal.off("hidden.bs.modal");
          },
          cb: function (modal) {
            modal.css("display", "block");
            var form = modal.find(".addNotificationForm");
            var controller = Vtiger_Edit_Js.getInstance();
            controller.registerBasicEvents(form);
            vtUtils.applyFieldElementsView(form);

            // Form validation
            var params = {
              submitHandler: function (form) {
                var form = jQuery(form);
                var params = form.serializeFormData();
                return false;
              },
            };
            form.vtValidate(params);
            form.find(".select2").each(function () {
              if (!jQuery(this).data("select2")) {
                jQuery(this).select2();
              }
            });
          },
        });
      });
    },
    // Add by Dien Nguyen on 2025-03-03 to show create new record modal
    showCreateNewRecordModal: function (targetBtn) {
      var self = this;
      app.helper.showProgress();
      // Request modal content
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        view: "EditPipelineAjax",
        mode: "getCreateNewRecordModal",
      };
      app.request.post({ data: params }).then((err, res) => {
        app.helper.hideProgress();
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }
        app.helper.hideModal();
        // Show modal fix error
        app.helper.loadPageContentOverlay(res).then(function (modal) {
          modal.css("display", "block");

          var form = modal.find("#form-create-new-record");
          form.on("change", "#createEntityModule", function (e) {
            // form
            //   .find(".initialDataField")
            //   .toggleClass("hide", !$(this).val());
            var relatedModule = jQuery(e.currentTarget).val();
            var module_name = jQuery("#module_name").val();
            if (relatedModule == module_name) {
              jQuery(e.currentTarget)
                .closest(".taskTypeUi")
                .find(".sameModuleError")
                .removeClass("hide");
            } else {
              jQuery(e.currentTarget)
                .closest(".taskTypeUi")
                .find(".sameModuleError")
                .addClass("hide");
            }
            var params = {
              module: app.getModuleName(),
              parent: app.getParentModuleName(),
              view: "EditPipelineAjax",
              mode: "getCreateEntity",
              relatedModule: jQuery(e.currentTarget).val(),
              module_name: self.currentNameModule,
            };

            app.helper.showProgress();
            app.request.post({ data: params }).then(function (error, data) {
              if (error) {
                console.log(error);
              }
              app.helper.hideProgress();
              var createEntityContainer = jQuery("#addCreateEntityContainer");
              createEntityContainer.html(data);
              vtUtils.showSelect2ElementView(
                createEntityContainer.find(".select2")
              );

              self.registerAddFieldEvent();
              self.fieldValueMap = false;
              if (jQuery("#fieldValueMapping").val()) {
                self.fieldValueReMapping();
              }
              var fields = jQuery("#save_fieldvaluemapping").find(
                'select[name="fieldname"]'
              );
              jQuery.each(fields, function (i, field) {
                self.loadFieldSpecificUiOnCreateNewRecord(jQuery(field));
              });
            });
          });
          self.registerVTUpdateFieldsTaskEvents();
          self.registerSaveTaskSubmitEvent();
        });
      });
    },

    registerVTUpdateFieldsTaskEvents: function () {
      var thisInstance = this;
      this.registerAddFieldEvent();
      this.registerDeleteConditionEvent();
      this.registerCreateEntityFieldChange();
      this.fieldValueMap = false;
      if (jQuery("#fieldValueMapping").val()) {
        this.fieldValueReMapping();
      }
      var fields = jQuery("#save_fieldvaluemapping").find(
        'select[name="fieldname"]'
      );
      jQuery.each(fields, function (i, field) {
        thisInstance.loadFieldSpecificUiOnCreateNewRecord(jQuery(field));
      });
      this.getPopUp(jQuery("#form-create-new-record"));
    },
    /**
     * Add by Dien Nguyen on 2025-03-01
     * Function which will register row create event
     */
    registerAddFieldEvent: function () {
      jQuery("#addFieldBtn").on("click", function (e) {
        var newAddFieldContainer = jQuery(".basicAddFieldContainer")
          .clone(true, true)
          .removeClass("basicAddFieldContainer hide")
          .addClass("conditionRow");
        jQuery("select", newAddFieldContainer).addClass("select2");
        jQuery("#save_fieldvaluemapping").append(newAddFieldContainer);
        vtUtils.showSelect2ElementView(newAddFieldContainer.find(".select2"));
      });
    },
    /**
     * Add by Dien Nguyen on 2025-03-01
     * Function which will register row delete event
     */
    registerDeleteConditionEvent: function () {
      jQuery("#form-create-new-record").on(
        "click",
        ".deleteCondition",
        function (e) {
          jQuery(e.currentTarget).closest(".conditionRow").remove();
        }
      );
    },

    /**
     * Add by Dien Nguyen on 2025-03-01
     * Function which will register field change event
     */
    registerCreateEntityFieldChange: function () {
      var thisInstance = this;
      jQuery("#form-create-new-record").on(
        "change",
        'select[name="fieldname"]',
        function (e) {
          var selectedElement = jQuery(e.currentTarget);
          if (selectedElement.val() != "none") {
            var conditionRow = selectedElement.closest(".conditionRow");
            var moduleNameElement = conditionRow.find('[name="modulename"]');
            if (moduleNameElement.length > 0) {
              var selectedOptionFieldInfo = selectedElement
                .find("option:selected")
                .data("fieldinfo");
              var type = selectedOptionFieldInfo.type;
              if (type == "picklist" || type == "multipicklist") {
                var moduleName = jQuery("#createEntityModule").val();
                moduleNameElement
                  .find('option[value="' + moduleName + '"]')
                  .attr("selected", true);
                moduleNameElement.trigger("change");
                moduleNameElement.select2("disable");
              }
            }
            thisInstance.loadFieldSpecificUiOnCreateNewRecord(selectedElement);
          }
        }
      );
    },

    fieldValueReMapping: function () {
      var object = JSON.parse(jQuery("#fieldValueMapping").val());
      var fieldValueReMap = {};

      jQuery.each(object, function (i, array) {
        fieldValueReMap[array.fieldname] = {};
        var values = {};
        jQuery.each(array, function (key, value) {
          values[key] = value;
        });
        fieldValueReMap[array.fieldname] = values;
      });
      this.fieldValueMap = fieldValueReMap;
    },

    getFieldSpecificUi: function (fieldSelectElement) {
      var fieldModel = this.fieldModelInstance;
      return jQuery(fieldModel.getUiTypeSpecificHtml());
    },

    // Add by Dien Nguyen on 2025-03-01 to load UI field
    loadFieldSpecificUiOnCreateNewRecord: function (fieldSelect) {
      var selectedOption = fieldSelect.find("option:selected");
      var row = fieldSelect.closest("div.conditionRow");
      var fieldUiHolder = row.find(".fieldUiHolder");
      var fieldInfo = selectedOption.data("fieldinfo");
      var fieldValueMapping = this.getFieldValueMapping();
      var fieldValueMappingKey = fieldInfo.name;
      var taskType = jQuery("#taskType").val();

      if (taskType == "VTUpdateFieldsTask") {
        fieldValueMappingKey = fieldInfo.workflow_columnname;
        if (
          fieldValueMappingKey === undefined ||
          fieldValueMappingKey === null
        ) {
          fieldValueMappingKey = selectedOption.val();
        }
      }

      if (
        fieldValueMapping != "" &&
        typeof fieldValueMapping[fieldValueMappingKey] != "undefined"
      ) {
        fieldInfo.value = fieldValueMapping[fieldValueMappingKey]["value"];
        fieldInfo.workflow_valuetype =
          fieldValueMapping[fieldValueMappingKey]["valuetype"];
      } else {
        fieldInfo.workflow_valuetype = "rawtext";
      }

      if (fieldInfo.type == "reference" || fieldInfo.type == "multireference") {
        fieldInfo.referenceLabel = fieldUiHolder
          .find('[name="referenceValueLabel"]')
          .val();
        fieldInfo.type = "string";
      }

      // var moduleName = this.getModuleName();
      var moduleName = "Workflows";

      var fieldModel = Workflows_Field_Js.getInstance(fieldInfo, moduleName);
      this.fieldModelInstance = fieldModel;
      var fieldSpecificUi = this.getFieldSpecificUi(fieldSelect);

      //remove validation since we dont need validations for all eleements
      // Both filter and find is used since we dont know whether the element is enclosed in some conainer like currency
      var fieldName = fieldModel.getName();
      if (fieldModel.getType() == "multipicklist") {
        fieldName = fieldName + "[]";
      }

      if (fieldSpecificUi.find(".add-on").length > 0) {
        fieldSpecificUi.filter(".input-append").addClass("row-fluid");
        fieldSpecificUi.find(".input-append").addClass("row-fluid");
        fieldSpecificUi.filter(".input-prepend").addClass("row-fluid");
        fieldSpecificUi.find(".input-prepend").addClass("row-fluid");
        fieldSpecificUi.find('input[type="text"]').css("width", "79%");
      } else {
        fieldSpecificUi
          .filter('[name="' + fieldName + '"]')
          .addClass("row-fluid");
        fieldSpecificUi
          .find('[name="' + fieldName + '"]')
          .addClass("row-fluid");
      }

      fieldSpecificUi
        .filter('[name="' + fieldName + '"]')
        .attr("data-value", "value")
        .attr("data-workflow_columnname", fieldInfo.workflow_columnname);
      fieldSpecificUi
        .find('[name="' + fieldName + '"]')
        .attr("data-value", "value")
        .attr("data-workflow_columnname", fieldInfo.workflow_columnname);
      fieldSpecificUi
        .filter('[name="valuetype"]')
        .addClass("ignore-validation");
      fieldSpecificUi.find('[name="valuetype"]').addClass("ignore-validation");

      //If the workflowValueType is rawtext then only validation should happen
      var workflowValueType = fieldSpecificUi
        .filter('[name="valuetype"]')
        .val();
      if (
        workflowValueType != "rawtext" &&
        typeof workflowValueType != "undefined"
      ) {
        fieldSpecificUi
          .filter('[name="' + fieldName + '"]')
          .addClass("ignore-validation");
        fieldSpecificUi
          .find('[name="' + fieldName + '"]')
          .addClass("ignore-validation");
      }

      fieldUiHolder.html(fieldSpecificUi);
      fieldUiHolder.css({
        display: "inline-block",
      });
      fieldSpecificUi = jQuery(fieldSpecificUi[0]); // Add by Dien Nguyen on 2025-03-01 to avoid JS error

      if (fieldSpecificUi.is("input.select2")) {
        var tagElements = fieldSpecificUi.data("tags");
        var params = { tags: tagElements, tokenSeparators: [","] };
        vtUtils.showSelect2ElementView(fieldSpecificUi, params);
      } else if (fieldSpecificUi.is("select")) {
        if (fieldSpecificUi.hasClass("select2")) {
          vtUtils.showSelect2ElementView(fieldSpecificUi);
        } else {
          vtUtils.showSelect2ElementView(fieldSpecificUi);
        }
      } else if (fieldSpecificUi.is("input.dateField")) {
        var calendarType = fieldSpecificUi.data("calendarType");
        if (calendarType == "range") {
          var customParams = {
            calendars: 3,
            mode: "range",
            className: "rangeCalendar",
            onChange: function (formated) {
              fieldSpecificUi.val(formated.join(","));
            },
          };
          app.registerEventForDatePickerFields(
            fieldSpecificUi,
            false,
            customParams
          );
        } else {
          app.registerEventForDatePickerFields(fieldSpecificUi);
        }
      }

      // Added by Hieu Nguyen on 2019-06-30 to render owner and main owner field
      var singleOwner = false;

      if (
        fieldModel.getName() == "assigned_user_id" ||
        fieldModel.getName() == "main_owner_id"
      ) {
        // [Workflow] Added by Phu Vo on 2020.08.04 to support highlight main owner
        fieldUiHolder.addClass("fieldValue").addClass(fieldModel.getName());
        // End Phu Vo

        var selectedTags = fieldModel.getValue()
          ? JSON.stringify(fieldModel.getValue())
          : "";
        fieldSpecificUi =
          '<input type="text" name="' +
          fieldModel.getName() +
          '" value="" data-selected-tags=\'' +
          selectedTags +
          '\' data-value="value" data-rule-main-owner="true" class="form-control" style="display: inline-block">' +
          '<input type="hidden" name="valuetype" value="rawtext" />';

        // Added by Hieu Nguyen on 2020-10-26 to support assign new record to parent record owners
        if (
          fieldModel.getName() == "assigned_user_id" &&
          jQuery.inArray(taskType, [
            "VTCreateTodoTask",
            "VTCreateEventTask",
            "VTCreateEntityTask",
          ]) >= 0
        ) {
          var checked =
            fieldUiHolder.data("assignParentRecordOwners") == 1
              ? "checked"
              : "";
          fieldSpecificUi +=
            '<label><input type="checkbox" name="assign_parent_record_owners" ' +
            checked +
            " /> " +
            fieldUiHolder.data("assignParentRecordOwnersLabel") +
            "<label>";
        }
        // End Hieu Nguyen

        fieldUiHolder.html(fieldSpecificUi);
        fieldUiHolder.css({
          display: "inline-flex",
          flexDirection: "column",
        });

        // Init select2
        var input = fieldUiHolder.find(
          'input[name="' + fieldModel.getName() + '"]'
        );
        input.data("singleSelection", singleOwner);
        CustomOwnerField.initCustomOwnerFields(input);

        this.registerOwnerFieldEvent(fieldUiHolder);
      }
      // End Hieu Nguyen

      // Added by Hieu Nguyen on 2019-06-30 to render user reference field
      if (
        fieldModel.getName() == "createdby" ||
        fieldModel.getName() == "modifiedby" ||
        fieldInfo.column == "inventorymanager"
      ) {
        var selectedTags = "";

        if (fieldModel.get("selected_tags")) {
          selectedTags = JSON.stringify(fieldModel.get("selected_tags"));
        }

        fieldSpecificUi =
          '<input type="text" name="' +
          fieldModel.getName() +
          '" value="' +
          fieldModel.getValue() +
          '" data-selected-tags="' +
          selectedTags +
          '" data-value="value" data-rule-main-owner="true" class="form-control">' +
          '<input type="hidden" name="valuetype" value="rawtext" />';
        fieldUiHolder.html(fieldSpecificUi);

        // Init select2
        var input = fieldUiHolder.find(
          'input[name="' + fieldModel.getName() + '"]'
        );
        input.data("singleSelection", singleOwner);
        CustomOwnerField.initCustomOwnerFields(input);
      }
      // End Hieu Nguyen
      return this;
    },

    getPopUp: function (container) {
      var thisInstance = this;
      if (typeof container == "undefined") {
        // container = thisInstance.getContainer();
        return;
      }
      var isPopupShowing = false;
      container.on("click", ".getPopupUi", function (e) {
        console.log("CLICK .getPopupui");
        // Added to prevent multiple clicks event
        if (isPopupShowing) {
          return false;
        }
        var fieldValueElement = jQuery(e.currentTarget);
        var fieldValue = fieldValueElement.val();
        var fieldUiHolder = fieldValueElement.closest(".fieldUiHolder");
        var valueType = fieldUiHolder.find('[name="valuetype"]').val();
        if (valueType == "" || valueType == "null") {
          valueType = "rawtext";
        }
        var conditionsContainer = fieldValueElement.closest(
          ".conditionsContainer"
        );
        var conditionRow = fieldValueElement.closest(".conditionRow");

        var clonedPopupUi = conditionsContainer
          .find(".popupUi")
          .clone(true, true)
          .removeClass("hide")
          .removeClass("popupUi")
          .addClass("clonedPopupUi");
        console.log(clonedPopupUi);
        clonedPopupUi.find("select").addClass("select2");
        clonedPopupUi.find(".fieldValue").val(fieldValue);
        clonedPopupUi.find(".fieldValue").removeClass("hide");
        if (fieldValueElement.hasClass("date")) {
          clonedPopupUi
            .find(".textType")
            .find('option[value="rawtext"]')
            .attr("data-ui", "input");
          var dataFormat = fieldValueElement.data("date-format");
          if (valueType == "rawtext") {
            var value = fieldValueElement.val();
          } else {
            value = "";
          }
          var clonedDateElement =
            '<input type="text" style="width: 30%;" class="dateField fieldValue inputElement" value="' +
            value +
            '" data-date-format="' +
            dataFormat +
            '" data-input="true" >';
          clonedPopupUi
            .find(".fieldValueContainer div")
            .prepend(clonedDateElement);
        } else if (fieldValueElement.hasClass("time")) {
          clonedPopupUi
            .find(".textType")
            .find('option[value="rawtext"]')
            .attr("data-ui", "input");
          if (valueType == "rawtext") {
            var value = fieldValueElement.val();
          } else {
            value = "";
          }
          var clonedTimeElement =
            '<input type="text" style="width: 30%;" class="timepicker-default fieldValue inputElement" value="' +
            value +
            '" data-input="true" >';
          clonedPopupUi
            .find(".fieldValueContainer div")
            .prepend(clonedTimeElement);
        } else if (fieldValueElement.hasClass("boolean")) {
          clonedPopupUi
            .find(".textType")
            .find('option[value="rawtext"]')
            .attr("data-ui", "input");
          if (valueType == "rawtext") {
            var value = fieldValueElement.val();
          } else {
            value = "";
          }
          var clonedBooleanElement =
            '<input type="checkbox" style="width: 30%;" class="fieldValue inputElement" value="' +
            value +
            '" data-input="true" >';
          clonedPopupUi
            .find(".fieldValueContainer div")
            .prepend(clonedBooleanElement);

          var fieldValue = clonedPopupUi
            .find(".fieldValueContainer input")
            .val();
          if (value == "true:boolean" || value == "") {
            clonedPopupUi
              .find(".fieldValueContainer input")
              .attr("checked", "checked");
          } else {
            clonedPopupUi
              .find(".fieldValueContainer input")
              .removeAttr("checked");
          }
        }
        app.helper.hideModal();
        var preCallbackFunction = function (data) {
          console.log("PRECALLBACK");
          data.off("hidden.bs.modal");
        };
        var callBackFunction = function (data) {
          console.log("CALLBACKFUNC");
          data.css("display", "block");
          data.find("#modal-content").removeClass("hide");
          isPopupShowing = false;
          data.find(".clonedPopupUi").removeClass("hide");
          var moduleNameElement = conditionRow.find('[name="modulename"]');
          if (moduleNameElement.length > 0) {
            var moduleName = moduleNameElement.val();
            data.find(".useFieldElement").addClass("hide");
            jQuery(data.find('[name="' + moduleName + '"]').get(0)).removeClass(
              "hide"
            );
          }
          thisInstance.postShowModalAction(data, valueType);
          thisInstance.registerChangeFieldEvent(data);
          thisInstance.registerSelectOptionEvent(data);
          thisInstance.registerPopUpSaveEvent(data, fieldUiHolder);
          thisInstance.registerRemoveModalEvent(data);
          data.find(".fieldValue").filter(":visible").trigger("focus");
        };
        conditionsContainer.find(".clonedPopUp").html(clonedPopupUi);
        jQuery(".clonedPopupUi").on("shown", function () {
          if (typeof callBackFunction == "function") {
            callBackFunction(jQuery(".clonedPopupUi", conditionsContainer));
          }
        });
        isPopupShowing = true;
        app.helper.showModal(
          jQuery(".clonedPopUp", conditionsContainer).find(".clonedPopupUi"),
          {
            preShowCb: preCallbackFunction,
            cb: callBackFunction,
          }
        );
      });
    },

    registerRemoveModalEvent: function (data) {
      data.on("click", ".closeModal", function (e) {
        data.modal("hide");
      });
    },

    registerPopUpSaveEvent: function (data, fieldUiHolder) {
      jQuery('[name="saveButton"]', data).on("click", function (e) {
        var valueType = jQuery("select.textType", data).val();

        fieldUiHolder.find('[name="valuetype"]').val(valueType);
        var fieldValueElement = fieldUiHolder.find(".getPopupUi");
        if (valueType != "rawtext") {
          fieldValueElement.addClass("ignore-validation");
        } else {
          fieldValueElement.removeClass("ignore-validation");
        }
        var fieldType = data
          .find(".fieldValue")
          .filter(":visible")
          .attr("type");
        var fieldValue = data.find(".fieldValue").filter(":visible").val();
        //For checkbox field type, handling fieldValue
        if (fieldType == "checkbox") {
          if (data.find(".fieldValue").filter(":visible").is(":checked")) {
            fieldValue = "true:boolean";
          } else {
            fieldValue = "false:boolean";
          }
        }
        fieldValueElement.val(fieldValue);
        data.modal("hide");
      });
    },

    registerSelectOptionEvent: function (data) {
      jQuery(".useField,.useFunction", data).on("change", function (e) {
        var currentElement = jQuery(e.currentTarget);
        var newValue = currentElement.val();
        var oldValue = data.find(".fieldValue").filter(":visible").val();
        var textType = currentElement
          .closest(".clonedPopupUi")
          .find("select.textType")
          .val();
        if (currentElement.hasClass("useField")) {
          //If it is fieldname mode then we need to allow only one field
          if (oldValue != "" && textType != "fieldname") {
            var concatenatedValue = oldValue + " " + newValue;
          } else {
            concatenatedValue = newValue;
          }
        } else {
          concatenatedValue = oldValue + newValue;
        }
        data.find(".fieldValue").val(concatenatedValue);
        currentElement.val("").select2("val", "");
      });
    },

    registerChangeFieldEvent: function (data) {
      jQuery(".textType", data).on("change", function (e) {
        var valueType = jQuery(e.currentTarget).val();
        var useFieldContainer = jQuery(".useFieldContainer", data);
        var useFunctionContainer = jQuery(".useFunctionContainer", data);
        var uiType = jQuery(e.currentTarget).find("option:selected").data("ui");
        jQuery(".fieldValue", data).hide();
        jQuery("[data-" + uiType + "]", data).show();
        if (valueType == "fieldname") {
          useFieldContainer.removeClass("hide");
          useFunctionContainer.addClass("hide");
        } else if (valueType == "expression") {
          useFieldContainer.removeClass("hide");
          useFunctionContainer.removeClass("hide");
        } else {
          useFieldContainer.addClass("hide");
          useFunctionContainer.addClass("hide");
        }
        jQuery(".helpmessagebox", data).addClass("hide");
        jQuery("#" + valueType + "_help", data).removeClass("hide");
        data.find(".fieldValue").val("");
      });
    },

    postShowModalAction: function (data, valueType) {
      if (valueType == "fieldname") {
        jQuery(".useFieldContainer", data).removeClass("hide");
        jQuery(".textType", data).val(valueType).trigger("change");
      } else if (valueType == "expression") {
        jQuery(".useFieldContainer", data).removeClass("hide");
        jQuery(".useFunctionContainer", data).removeClass("hide");
        jQuery(".textType", data).val(valueType).trigger("change");
      }
      jQuery("#" + valueType + "_help", data).removeClass("hide");
      var uiType = jQuery(".textType", data).find("option:selected").data("ui");
      jQuery(".fieldValue", data).hide();
      jQuery("[data-" + uiType + "]", data).show();
    },

    registerSaveTaskSubmitEvent: function () {
      var self = this;
      var form = jQuery("#form-create-new-record");
      var params = {
        submitHandler: function (form) {
          var form = jQuery(form);
          // to Prevent submit if already submitted
          jQuery("button[name='saveButton']", form).attr(
            "disabled",
            "disabled"
          );
          var params = form.serializeFormData();
          console.log(params);
          self.action["createNewRecordInfo"] = params;
          self.action["action_name"] = params.action_name;
          self.action["action_type"] = "createNewRecord";
          self.action["time"] = parseInt(self.action["time"]);
          self.targetController.pushAction(self.action, self.isEdit);
          app.helper.hidePageContentOverlay();
          return false;
        },
        ignore: ".ignore-validation",
      };
      form.vtValidate(params);
    },
    // End Create new record Modal

    showDataFieldUpdateModal: function (targetBtn) {
      let self = this;

      // Show loading
      app.helper.showProgress();

      // Request modal content
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        view: "EditPipelineAjax",
        mode: "getUpdateDataFieldModal",
        currentNameModule: self.currentNameModule,
      };

      app.request.post({ data: params }).then((err, res) => {
        app.helper.hideProgress();
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }
        // Show modal
        app.helper.showModal(res, {
          preShowCb: function (modal) {
            modal.off("hidden.bs.modal");
            const form = modal.find("form#form-update-data-field");
            let addDataField = form.find("#addDataField");
            addDataField.on("click", function () {
              var basicElement = jQuery(".basic");
              var newRowElement = basicElement
                .find(".fieldRow")
                .clone(true, true);
              jQuery("select", newRowElement).addClass("select2");
              var newDataField = jQuery(".newDataField");
              newRowElement.addClass("op0");
              newRowElement.appendTo(newDataField);
              setTimeout(function () {
                newRowElement.addClass("fadeInx");
              }, 100);
              //change in to chosen elements

              newRowElement.find("select.select2").select2();
            });
            // Xóa phần tử khi nhấn vào icon thùng rác
            form.on("click", ".removeField", function () {
              $(this).closest(".form-group").remove();
            });
          },
          cb: function (modal) {
            modal.css("display", "block");
            modal.find("#modal-content").removeClass("hide");
            var form = modal.find("#form-update-data-field");
            var controller = Vtiger_Edit_Js.getInstance();
            controller.registerBasicEvents(form);
            vtUtils.applyFieldElementsView(form);
            // Form validation
            var params = {
              submitHandler: function (form) {
                var form = jQuery(form);
                let fieldRows = self.getValuesFromDataFieldUpdateModal();
                var params = form.serializeFormData();
                console.log(params);
                params["updateDataFields"] = fieldRows;
                self.action["updateDataFields"] = fieldRows;
                self.action["action_name"] = params.action_name;
                self.action["action_type"] = "updateDataField";
                self.action["time"] = parseInt(self.action["time"]);
                self.targetController.pushAction(self.action, self.isEdit);
                app.helper.hideModal();
                return false;
              },
            };
            form.vtValidate(params);
            form.find(".select2").each(function () {
              if (!jQuery(this).data("select2")) {
                jQuery(this).select2();
              }
            });
            self.initialize();
          },
        });
      });
    },

    showSendSMSModal: function (targetBtn) {
      var self = this;
      app.helper.showProgress();

      // Request modal content
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        view: "EditPipelineAjax",
        mode: "getSendModal",
        type: "VTSMSTask",
        module_name: self.currentNameModule,
      };

      app.request.post({ data: params }).then((err, res) => {
        app.helper.hideProgress();
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }

        // Show modal
        app.helper.showModal(res, {
          preShowCb: function (modal) {
            modal.off("hidden.bs.modal");

            const form = modal.find("form#form-send-sms");
            // var textAreaElement = modal.find("#content");

            // Register events
            self.registerFillContentEvent(
              "#form-send-sms",
              ".task-fields",
              'input[name="sms_recepient"]'
            );
            self.registerFillContentEvent(
              "#form-send-sms",
              "#task-fieldnames",
              'textarea[name="content"]'
            );
            self.validateContentSMSModal();
          },
          cb: function (modal) {
            modal.css("display", "block");

            // var form = modal.find(".sendSMSModal");
            const form = modal.find("form#form-send-sms");

            // Form validation
            var params = {
              submitHandler: function (form) {
                var form = jQuery(form);
                var params = form.serializeFormData();
                var smsValues = self.getValuesFromSendSMSModal();

                self.action["time"] = parseInt(self.action["time"]);
                self.action["action_name"] = params.titleSMS;
                self.action["action_type"] = "sendSMS";
                self.action.sendSMSData = smsValues;
                self.targetController.pushAction(self.action);

                app.helper.hideModal();

                return false;
              },
            };
            form.vtValidate(params);
          },
        });
      });
    },

    showSendZNSModal: function (targetBtn) {
      app.helper.showProgress();

      // Request modal content
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        view: "EditPipelineAjax",
        mode: "getSendZNSModal",
      };

      app.request.post({ data: params }).then((err, res) => {
        app.helper.hideProgress();
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }

        // Show modal
        app.helper.showModal(res, {
          preShowCb: function (modal) {
            modal.off("hidden.bs.modal");
          },
          cb: function (modal) {
            modal.css("display", "block");

            var form = modal.find(".sendZNSModal");
            var controller = Vtiger_Edit_Js.getInstance();

            controller.registerBasicEvents(form);
            vtUtils.applyFieldElementsView(form);

            // Form validation
            var params = {
              submitHandler: function (form) {
                var form = jQuery(form);
                var params = form.serializeFormData();

                return false;
              },
            };
            form.vtValidate(params);
            form.find(".select2").each(function () {
              if (!jQuery(this).data("select2")) {
                jQuery(this).select2();
              }
            });
          },
        });
      });
    },

    showSendEmailModal: function (targetBtn) {
      var self = this;
      app.helper.showProgress();

      // Request modal content
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        view: "EditPipelineAjax",
        mode: "getSendModal",
        type: "VTEmailTask",
        module_name: self.currentNameModule,
      };

      app.request.post({ data: params }).then((err, res) => {
        app.helper.hideProgress();
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }

        // Show modal
        app.helper.showModal(res, {
          preShowCb: function (modal) {
            modal.off("hidden.bs.modal");

            const form = modal.find("form#form-send-email");
            var textAreaElement = modal.find("#content");
            var ckEditorInstance = self.getckEditorInstance();
            var ccLink = modal.find("#ccLink");
            var bccLink = modal.find("#bccLink");
            var ccContainer = modal.find("#ccContainer");
            var bccContainer = modal.find("#bccContainer");

            // Register events
            ckEditorInstance.loadCkEditor(textAreaElement);
            self.registerFillContentEvent(
              "#form-send-email",
              ".task-fields",
              ".fields"
            );
            self.registerFillContentEvent(
              "#form-send-email",
              "#task-fieldnames,#task_timefields,#task-templates,#task-emailtemplates",
              "content",
              true
            );
            self.registerTooltipEventForSignatureField();
            self.registerFillTaskFromEmailFieldEvent();

            if (ccLink.length) {
              ccLink.on("click", function () {
                ccContainer.removeClass("hide"); // Show CC container
                ccLink.addClass("hide"); // Hide CC link
              });
            }

            if (bccLink.length) {
              bccLink.on("click", function () {
                bccContainer.removeClass("hide"); // Show BCC container
                bccLink.addClass("hide"); // Hide BCC link
              });
            }
          },
          cb: function (modal) {
            modal.css("display", "block");

            const form = modal.find("form#form-send-email");

            // Form validation
            var params = {
              submitHandler: function (form) {
                var form = jQuery(form);
                var params = form.serializeFormData();
                var emailValues = self.getValuesFromSendEmailModal();

                self.action["time"] = parseInt(self.action["time"]);
                self.action["action_name"] = params.titleEmail;
                self.action["action_type"] = "sendEmail";
                self.action.sendEmailData = emailValues;
                self.targetController.pushAction(self.action);

                app.helper.hideModal();

                return false;
              },
            };
            form.vtValidate(params);
          },
        });
      });
    },

    showNotificationModal: function (targetBtn) {
      var self = this;
      app.helper.showProgress();
      // Request modal content
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        view: "EditPipelineAjax",
        mode: "getAddNotificationModal",
      };
      app.request.post({ data: params }).then((err, res) => {
        app.helper.hideProgress();
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }
        // Show modal
        app.helper.showModal(res, {
          preShowCb: function (modal) {
            modal.off("hidden.bs.modal");
          },
          cb: function (modal) {
            modal.css("display", "block");
            modal.find(".modal-content").removeClass("hide");
            var form = modal.find("#form-notification");
            var controller = Vtiger_Edit_Js.getInstance();
            controller.registerBasicEvents(form);
            vtUtils.applyFieldElementsView(form);

            // Form validation
            var params = {
              submitHandler: function (form) {
                console.log("Submit notification modal");
                var form = jQuery(form);
                var params = form.serializeFormData();
                let notificationInfo = {
                  userList: params.userList
                    .split(",")
                    .map((item) => parseInt(item.split(":")[1])),
                  repetition: params.repetition,
                  description: params.description,
                };
                self.action["notificationInfo"] = notificationInfo;
                self.action["action_name"] = params.name;
                self.action["action_type"] = "notification";
                self.action["time"] = self.action["time"]
                  ? parseInt(self.action["time"])
                  : null;
                self.targetController.pushAction(self.action, self.isEdit);
                app.helper.hideModal();
                return false;
              },
            };
            CustomOwnerField.initCustomOwnerFields(
              form.find('input[name="userList"]')
            );
            form.vtValidate(params);
            form.find(".select2").each(function () {
              if (!jQuery(this).data("select2")) {
                jQuery(this).select2();
              }
            });
          },
        });
      });
    },
    // End Minh Hoang

    showActionSettingModal: function (targetBtn, targetController, action) {
      var self = this;
      let isEdit = targetBtn.dataset.isedit;
      if (isEdit !== undefined) {
        self.isEdit = isEdit;
      }
      self.setAction(action);
      self.setCurrentNameModule(targetController.getCurrentNameModule());
      self.targetController = targetController;
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        view: "EditPipelineAjax",
        mode: "getActionSettingModal",
        actionInfo: action,
      };
      var self = this;
      app.request.post({ data: params }).then((err, res) => {
        if (err) {
          app.helper.showErrorNotification({ message: err.message });
          return;
        }
        app.helper.showModal(res, {
          // Prevent hidden.bs.modal event from clearing dom
          preShowCb: function (modal) {
            modal.off("hidden.bs.modal");
          },
          cb: function (modal) {
            modal.css("display", "block");
            modal.find("#actionSettingModal").removeClass("hide");
            var form = modal.find(".actionSettingForm");
            var controller = Vtiger_Edit_Js.getInstance();
            controller.registerBasicEvents(form);
            vtUtils.applyFieldElementsView(form);
            form.find(".select2").each(function () {
              if (!jQuery(this).data("select2")) {
                jQuery(this).select2();
              }
            });
            self.registerEvents();
            modal.find("#back").on("click", function () {
              modal.modal("hide");
              var addActionSettingController =
                new Settings_PipelineConfig_AddActionSetting_Js();
              addActionSettingController.showAddActionSettingModal(
                targetBtn,
                targetController,
                action
              );
            });
          },
        });
      });
      return false;
    },

    setAction: function (action) {
      this.action = action;
    },

    getModal: function () {
      return $("#actionSettingModal");
    },
  }
);

CustomView_BaseController_Js(
  "Settings_PipelineConfig_AddCondition_Js",
  {},
  {
    filterContainer: false,
    //Hold the conditions for a particular field type
    fieldTypeConditionMapping: false,
    //Hold the condition and their label translations
    conditonOperatorLabelMapping: false,

    dateConditionInfo: false,

    fieldModelInstance: false,
    //Holds fields type and conditions for which it needs validation
    validationSupportedFieldConditionMap: {
      email: ["e", "n"],
    },
    //Hols field type for which there is validations always needed
    allConditionValidationNeededFieldList: ["double", "integer", "currency"],
    //used to eliminate mutiple times validation registrations
    validationForControlsRegistered: false,

    init: function (container) {
      if (typeof container == "undefined") {
        container = jQuery("#transitionConditionModal");
      }

      if (container.is("#transitionConditionModal")) {
        this.setFilterContainer(container);
      } else {
        this.setFilterContainer(jQuery("#transitionConditionModal", container));
      }
      this.initialize();
    },

    getFilterContainer: function () {
      return this.filterContainer;
    },

    setFilterContainer: function (element) {
      this.filterContainer = element;
      return this;
    },

    getDateSpecificConditionInfo: function () {
      return this.dateConditionInfo;
    },

    getModuleName: function () {
      return "AdvanceFilter";
    },

    getValues: function () {
      var thisInstance = this;
      var filterContainer = this.getFilterContainer();

      var fieldList = new Array(
        "columnname",
        "compareType",
        "value",
        "column_condition"
      );

      var values = {};
      var columnIndex = 0;
      var conditionGroups = jQuery(".conditionGroup", filterContainer);
      conditionGroups.each(function (index, domElement) {
        var groupElement = jQuery(domElement);
        values[index + 1] = {};
        var conditions = jQuery(".conditionRow", groupElement);
        values[index + 1]["columns"] = {};
        conditions.each(function (i, conditionDomElement) {
          var rowElement = jQuery(conditionDomElement);
          var fieldSelectElement = jQuery('[name="columnname"]', rowElement);
          var valueSelectElement = jQuery('[data-value="value"]', rowElement);
          //To not send empty fields to server
          if (thisInstance.isEmptyFieldSelected(fieldSelectElement)) {
            return true;
          }
          var fieldDataInfo = fieldSelectElement
            .find("option:selected")
            .data("fieldinfo");
          var fieldType = fieldDataInfo.type;
          var rowValues = {};
          if (
            fieldType == "owner" ||
            fieldType == "ownergroup" ||
            jQuery.inArray(fieldDataInfo.name, ["createdby", "modifiedby"]) >=
              0 ||
            fieldDataInfo.column == "inventorymanager"
          ) {
            for (var key in fieldList) {
              var field = fieldList[key];
              if (field == "value" && valueSelectElement.is("select")) {
                var selectedOptions =
                  valueSelectElement.find("option:selected");
                var newvaluesArr = [];
                jQuery.each(selectedOptions, function (i, e) {
                  newvaluesArr.push(jQuery.trim(jQuery(e).text()));
                });
                if (selectedOptions.length == 0) {
                  rowValues[field] = "";
                } else {
                  rowValues[field] = newvaluesArr.join(",");
                }
              } else if (field == "value" && valueSelectElement.is("input")) {
                rowValues[field] = valueSelectElement.val();
              } else {
                console.log("GAN VALUE");
                rowValues[field] = jQuery(
                  '[name="' + field + '"]',
                  rowElement
                ).val();
              }
            }
          } else if (fieldType == "picklist" || fieldType == "multipicklist") {
            for (var key in fieldList) {
              var field = fieldList[key];
              if (field == "value" && valueSelectElement.is("input")) {
                var commaSeperatedValues = valueSelectElement.val();
                var pickListValues = valueSelectElement.data("picklistvalues");
                var valuesArr = commaSeperatedValues.split(",");
                var newvaluesArr = [];
                for (i = 0; i < valuesArr.length; i++) {
                  if (typeof pickListValues[valuesArr[i]] != "undefined") {
                    newvaluesArr.push(pickListValues[valuesArr[i]]);
                  } else {
                    newvaluesArr.push(valuesArr[i]);
                  }
                }
                var reconstructedCommaSeperatedValues = newvaluesArr.join(",");
                rowValues[field] = reconstructedCommaSeperatedValues;
              } else if (
                field == "value" &&
                valueSelectElement.is("select") &&
                fieldType == "picklist"
              ) {
                var value = valueSelectElement.val();
                if (value == null) {
                  rowValues[field] = value;
                } else {
                  rowValues[field] = value.join(",");
                }
              } else if (
                field == "value" &&
                valueSelectElement.is("select") &&
                fieldType == "multipicklist"
              ) {
                var value = valueSelectElement.val();
                if (value == null) {
                  rowValues[field] = value;
                } else {
                  rowValues[field] = value.join(",");
                }
              } else {
                rowValues[field] = jQuery(
                  '[name="' + field + '"]',
                  rowElement
                ).val();
              }
            }
          }
          // Added by Hieu Nguyen on 2021-01-26 to support tags field (NOTE: Currently for tags field in MAIN MODULE ONLY!)
          else if (fieldType == "tags") {
            for (var key in fieldList) {
              var field = fieldList[key];

              if (field == "value") {
                var selectedTags = valueSelectElement.val();
                rowValues[field] = selectedTags ? selectedTags.join(",") : null;
              } else {
                rowValues[field] = jQuery(
                  '[name="' + field + '"]',
                  rowElement
                ).val();
              }
            }
          }
          // End Hieu Nguyen
          else {
            for (var key in fieldList) {
              var field = fieldList[key];

              // Modified by Hieu Nguyen on 2021-05-19 to get unformatted value of numeric fields
              if (field == "value") {
                var value = valueSelectElement.val();

                if (
                  fieldType == "integer" ||
                  field == "decimal" ||
                  fieldType == "currency" ||
                  fieldType == "double"
                ) {
                  // Modified by Phu Vo on 2021.06.14 to add type double
                  value = app.unformatCurrencyToUser(value);
                }

                rowValues[field] = value;
              }
              // End Hieu Nguyen
              else {
                rowValues[field] = jQuery(
                  '[name="' + field + '"]',
                  rowElement
                ).val();
              }
            }
          }

          if (
            rowElement.is(":last-child") ||
            groupElement.find(".conditionRow").length === 1
          ) {
            rowValues["column_condition"] = "";
            console.log("Length: " + groupElement.find(".conditionRow").length);
          }
          values[index + 1]["columns"][columnIndex] = rowValues;
          columnIndex++;
        });
        if (groupElement.find("div.groupCondition").length > 0) {
          values[index + 1]["condition"] = conditionGroups
            .find('div.groupCondition [name="condition"]')
            .val();
        }
      });
      if (
        values[2]["columns"] &&
        Object.keys(values[2]["columns"]).length === 0 &&
        values[1]["condition"] === "and"
      ) {
        values[1]["condition"] = "";
      }
      return values;
    },

    /**
     * Function to check if the field selected is empty field
     * @params : select element which represents the field
     * @return : boolean true/false
     */
    isEmptyFieldSelected: function (fieldSelect) {
      var selectedOption = fieldSelect.find("option:selected");
      //assumption that empty field will be having value none
      if (selectedOption.val() == "none") {
        return true;
      }
      return false;
    },

    initialize: function () {
      this.registerEvents();
      // this.changeFieldElementsView(this.getFilterContainer());
      this.initializeOperationMappingDetails();
      this.loadFieldSpecificUiForAll();
    },

    registerEvents: function () {
      // this.registerAddCondition();
      this.registerFieldChange();
      this.registerDeleteCondition();
      this.registerConditionChange();
    },

    /**
     * Function to regisgter delete condition event
     */
    registerDeleteCondition: function () {
      var thisInstance = this;
      var filterContainer = jQuery("#transitionConditionModal");
      filterContainer.on("click", ".fa-trash-alt", function (e) {
        thisInstance.deleteConditionHandler(e);
      });
    },

    /**
     * Event handle which will be triggred on deletion of a condition row
     */
    deleteConditionHandler: function (e) {
      var element = jQuery(e.currentTarget);
      var row = element.closest(".conditionRow");
      row.remove();
    },

    /**
     * Function which will save the field condition mapping condition label mapping
     */
    initializeOperationMappingDetails: function () {
      var filterContainer = this.getFilterContainer();
      this.fieldTypeConditionMapping = jQuery(
        'input[name="advanceFilterOpsByFieldType"]',
        filterContainer
      ).data("value");
      this.conditonOperatorLabelMapping = jQuery(
        'input[name="advanceFilterOptions"]',
        filterContainer
      ).data("value");
      this.dateConditionInfo = jQuery('[name="date_filters"]').data("value");
      return this;
    },

    /**
     * Function to load field specific ui for all the select elements - this is used on load
     * to show field specific ui for all the fields
     */
    loadFieldSpecificUiForAll: function () {
      var conditionsContainer = jQuery("#transitionConditionModal");
      var fieldSelectElement = jQuery(
        'select[name="columnname"]',
        conditionsContainer
      );
      jQuery.each(fieldSelectElement, function (i, elem) {
        var currentElement = jQuery(elem);
        if (currentElement.val() != "none") {
          currentElement.trigger("change", { _intialize: true });
        }
      });
      return this;
    },

    registerFieldChange: function () {
      var filterContainer = this.getFilterContainer();
      var thisInstance = this;
      filterContainer.on(
        "change",
        'select[name="columnname"]',
        function (e, data) {
          var currentElement = jQuery(e.currentTarget);
          if (typeof data == "undefined" || data._intialize != true) {
            var row = currentElement.closest("div.conditionRow");
            var conditionSelectElement = row.find('select[name="compareType"]');
            conditionSelectElement.empty();
          }
          thisInstance.loadConditions(currentElement);
          thisInstance.loadFieldSpecificUi(currentElement);
        }
      );
    },

    registerConditionChange: function () {
      var filterContainer = this.getFilterContainer();
      var thisInstance = this;
      filterContainer.on("change", 'select[name="compareType"]', function (e) {
        console.log("CONDITION CHANGE");
        var comparatorSelectElement = jQuery(e.currentTarget);
        var row = comparatorSelectElement.closest("div.conditionRow");
        var fieldSelectElement = row.find('select[name="columnname"]');
        var selectedOption = fieldSelectElement.find("option:selected");
        //To handle the validation depending on condtion
        thisInstance.loadFieldSpecificUi(fieldSelectElement);
        thisInstance.addValidationToFieldIfNeeded(fieldSelectElement);
      });
    },

    loadFieldSpecificUi: function (fieldSelect) {
      var selectedOption = fieldSelect.find("option:selected");
      var row = fieldSelect.closest("div.conditionRow");
      var fieldUiHolder = row.find(".fieldUiHolder");
      var conditionSelectElement = row.find('select[name="compareType"]');
      var fieldInfo = selectedOption.data("fieldinfo");

      var fieldType = "string";
      if (typeof fieldInfo != "undefined") {
        fieldType = fieldInfo.type;
      }
      var comparatorElementVal = (fieldInfo.comparatorElementVal =
        conditionSelectElement.val());
      if (fieldType == "date" || fieldType == "datetime") {
        fieldInfo.dateSpecificConditions = this.getDateSpecificConditionInfo();
      }
      var moduleName = this.getModuleName();
      console.log("FIELDINFO");
      console.log(fieldInfo);
      console.log(moduleName);
      var fieldModel = Vtiger_Field_Js.getInstance(fieldInfo, moduleName);

      this.fieldModelInstance = fieldModel;
      var fieldSpecificUi = this.getFieldSpecificUi(fieldSelect);

      //remove validation since we dont need validations for all eleements
      // Both filter and find is used since we dont know whether the element is enclosed in some conainer like currency
      var fieldName = fieldModel.getName();
      if (fieldModel.getType() == "multipicklist") {
        fieldName = fieldName + "[]";
      }

      if (
        (fieldModel.getType() == "picklist" ||
          fieldModel.getType() == "owner") &&
        fieldSpecificUi.is("select") &&
        (comparatorElementVal == "e" || comparatorElementVal == "n")
      ) {
        fieldName = fieldName + "[]";
      }

      if (fieldSpecificUi.find(".add-on").length > 0) {
        fieldSpecificUi.filter(".input-append").addClass("row-fluid");
        fieldSpecificUi.find(".input-append").addClass("row-fluid");
        fieldSpecificUi.filter(".input-prepend").addClass("row-fluid");
        fieldSpecificUi.find(".input-prepend").addClass("row-fluid");
        fieldSpecificUi.find('input[type="text"]').css("width", "79%");
      } else {
        fieldSpecificUi
          .filter('[name="' + fieldName + '"]')
          .addClass("row-fluid");
        fieldSpecificUi
          .find('[name="' + fieldName + '"]')
          .addClass("row-fluid");
      }

      fieldSpecificUi
        .filter('[name="' + fieldName + '"]')
        .attr("data-value", "value")
        .removeAttr("data-validation-engine")
        .addClass("ignore-validation");
      fieldSpecificUi
        .find('[name="' + fieldName + '"]')
        .attr("data-value", "value")
        .removeAttr("data-validation-engine")
        .addClass("ignore-validation");

      if (fieldModel.getType() == "currency") {
        fieldSpecificUi
          .filter('[name="' + fieldName + '"]')
          .attr("data-decimal-separator", fieldInfo.decimal_separator)
          .attr("data-group-separator", fieldInfo.group_separator);
        fieldSpecificUi
          .find('[name="' + fieldName + '"]')
          .attr("data-decimal-separator", fieldInfo.decimal_separator)
          .attr("data-group-separator", fieldInfo.group_separator);
      }

      fieldUiHolder.html(fieldSpecificUi);
      fieldSpecificUi = jQuery(fieldSpecificUi[0]); // Added by Hieu Nguyen on 2020-12-16 to fix bug multi-select field cause js error

      if (fieldSpecificUi.is("input.select2")) {
        var tagElements = fieldSpecificUi.data("tags");
        var params = { tags: tagElements, tokenSeparators: [","] };
        vtUtils.showSelect2ElementView(fieldSpecificUi, params);
      } else if (fieldSpecificUi.is("select")) {
        if (fieldSpecificUi.hasClass("chzn-select")) {
          app.changeSelectElementView(fieldSpecificUi);
        } else {
          vtUtils.showSelect2ElementView(fieldSpecificUi);
        }
      } else if (fieldSpecificUi.has("input.dateField").length > 0) {
        vtUtils.registerEventForDateFields(fieldSpecificUi);
      } else if (fieldSpecificUi.has("input.timepicker-default").length > 0) {
        vtUtils.registerEventForTimeFields(fieldSpecificUi);
      }
      this.addValidationToFieldIfNeeded(fieldSelect);

      var comparatorContainer = conditionSelectElement.closest(
        '[class^="conditionComparator"]'
      );
      //if it is check box then we need hide the comprator
      if (fieldModel.getType().toLowerCase() == "boolean") {
        //making the compator as equal for check box
        conditionSelectElement
          .find('option[value="e"]')
          .attr("selected", "selected");
        comparatorContainer.hide();
      } else {
        comparatorContainer.show();
      }

      // Is Empty, today, tomorrow, yesterday conditions does not need any field input value - hide the UI
      // re-enable if condition element is chosen.
      var specialConditions = ["y", "today", "tomorrow", "yesterday", "ny"];
      if (specialConditions.indexOf(conditionSelectElement.val()) != -1) {
        fieldUiHolder.hide();
      } else {
        fieldUiHolder.show();
      }

      // Added by Hieu Nguyen on 2019-06-13 to render owner and main owner field
      var singleOwner = moduleName == "Workflows" ? true : false;

      if (
        fieldModel.getName() == "assigned_user_id" ||
        fieldModel.getName() == "main_owner_id"
      ) {
        var selectedTags = "";

        if (fieldModel.get("selected_tags")) {
          selectedTags = JSON.stringify(fieldModel.get("selected_tags"));
        }

        if (moduleName == "Workflows") {
          selectedTags = fieldModel.getValue();
        }

        fieldSpecificUi =
          '<input type="text" name="' +
          fieldModel.getName() +
          '" value="' +
          fieldModel.getValue() +
          "\" data-selected-tags='" +
          selectedTags +
          '\' data-value="value" class="form-control">';
        fieldUiHolder.html(fieldSpecificUi);

        // Init select2
        var input = fieldUiHolder.find(
          'input[name="' + fieldModel.getName() + '"]'
        );
        input.data("singleSelection", singleOwner);
        CustomOwnerField.initCustomOwnerFields(input);
      }
      // End Hieu Nguyen

      // Added by Hieu Nguyen on 2019-10-22 to render user reference field
      if (
        fieldModel.getName() == "createdby" ||
        fieldModel.getName() == "modifiedby" ||
        fieldInfo.column == "inventorymanager"
      ) {
        var selectedTags = "";

        if (fieldModel.get("selected_tags")) {
          selectedTags = JSON.stringify(fieldModel.get("selected_tags"));
        }

        if (moduleName == "Workflows") {
          selectedTags = fieldModel.getValue();
        }

        fieldSpecificUi =
          '<input type="text" name="' +
          fieldModel.getName() +
          '" value="' +
          fieldModel.getValue() +
          "\" data-selected-tags='" +
          selectedTags +
          '\' data-value="value" class="form-control">';
        fieldUiHolder.html(fieldSpecificUi);

        // Init select2
        var input = fieldUiHolder.find(
          'input[name="' + fieldModel.getName() + '"]'
        );
        input.data("singleSelection", singleOwner);
        CustomOwnerField.initCustomOwnerFields(input);
      }
      // End Hieu Nguyen

      // Added by Hieu Nguyen on 2021-12-21 to update field info value to fix bug filter input re-rendered with the old value
      fieldUiHolder.find(":input").on("change", function () {
        fieldInfo.value = $(this).val();
      });
      // End Hieu Nguyen

      return this;
    },

    /**
     * Function to add the validation if required
     * @prarms : selectFieldElement - select element which will represents field list
     */
    addValidationToFieldIfNeeded: function (selectFieldElement) {
      var selectedOption = selectFieldElement.find("option:selected");
      var row = selectFieldElement.closest("div.conditionRow");
      var fieldSpecificElement = row.find('[data-value="value"]');
      var validator = selectedOption.attr("data-validator");

      if (this.isFieldSupportsValidation(selectFieldElement)) {
        //data attribute will not be present while attaching validation engine events . so we are
        //depending on the fallback option which is class
        //TODO : remove the hard coding and get it from field element data-validation-engine
        fieldSpecificElement
          .addClass(
            "validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
          )
          .attr(
            "data-validation-engine",
            "validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
          )
          .attr(
            "data-fieldinfo",
            JSON.stringify(selectedOption.data("fieldinfo"))
          );
        if (typeof validator != "undefined") {
          fieldSpecificElement.attr("data-validator", validator);
        }
        fieldSpecificElement.removeClass("ignore-validation");
      } else {
        fieldSpecificElement
          .removeClass(
            "validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
          )
          .removeAttr("data-validation-engine")
          .removeAttr("data-fieldinfo");
        fieldSpecificElement.addClass("ignore-validation");
      }
      return this;
    },

    /**
     * Check if field supports validation
     * @prarms : selectFieldElement - select element which will represents field list
     * @return - boolen true/false
     */
    isFieldSupportsValidation: function (fieldSelect) {
      var selectedOption = fieldSelect.find("option:selected");

      var fieldModel = this.fieldModelInstance;
      var type = fieldModel.getType();

      if (
        jQuery.inArray(type, this.allConditionValidationNeededFieldList) >= 0
      ) {
        return true;
      }

      var row = fieldSelect.closest("div.conditionRow");
      var conditionSelectElement = row.find('select[name="compareType"]');
      var selectedCondition = conditionSelectElement.find("option:selected");

      var conditionValue = conditionSelectElement.val();

      if (type in this.validationSupportedFieldConditionMap) {
        if (
          jQuery.inArray(
            conditionValue,
            this.validationSupportedFieldConditionMap[type]
          ) >= 0
        ) {
          return true;
        }
      }
      return false;
    },

    loadConditions: function (fieldSelect) {
      var row = fieldSelect.closest("div.conditionRow");
      var conditionSelectElement = row.find('select[name="compareType"]');
      var conditionSelected = conditionSelectElement.val();
      var fieldSelected = fieldSelect.find("option:selected");
      var fieldSpecificType = this.getFieldSpecificType(fieldSelected);
      var conditionList = this.getConditionListFromType(fieldSpecificType);

      // Added by Hieu Nguyen on 2018-06-13 to rebuild the condition options for assigned_user_id and main_owner_id fields
      let fieldInfo = fieldSelected.data("fieldinfo");

      if (
        fieldInfo.name == "assigned_user_id" ||
        fieldInfo.name == "main_owner_id"
      ) {
        conditionList = ["c", "k"]; // Support only 2 options: contains and not contains
      }
      // End Hieu Nguyen

      // Added by Hieu Nguyen on 2019-10-22 to rebuild the condition options for user reference field
      if (
        fieldInfo.name == "createdby" ||
        fieldInfo.name == "modifiedby" ||
        fieldInfo.column == "inventorymanager"
      ) {
        conditionList = ["c", "k"]; // Support only 2 options: contains and not contains
      }
      // End Hieu Nguyen

      // Added by Hieu Nguyen on 2021-01-26 to rebuild the condition options for tags field
      if (fieldInfo.name == "tags") {
        conditionList = ["c"]; // Support only option: contains
      }
      // End Hieu Nguyen

      // Added by Hieu Nguyen on 2021-06-25 to modify comparator for multipicklist field
      if (fieldInfo.type == "multipicklist") {
        conditionList = ["c", "k", "y", "ny"]; // Support only: contains, not contains, empty, not empty
      }
      // End Hieu Nguyen

      //for none in field name
      if (typeof conditionList == "undefined") {
        conditionList = {};
        conditionList["none"] = "None";
      }

      var options = "";
      for (var key in conditionList) {
        //IE Browser consider the prototype properties also, it should consider has own properties only.
        if (conditionList.hasOwnProperty(key)) {
          var conditionValue = conditionList[key];
          var conditionLabel = this.getConditionLabel(conditionValue);
          options += '<option value="' + conditionValue + '"';
          if (conditionValue == conditionSelected) {
            options += ' selected="selected" ';
          }
          options += ">" + conditionLabel + "</option>";
        }
      }
      conditionSelectElement.empty().html(options).trigger("change");
      return conditionSelectElement;
    },

    getFieldSpecificType: function (fieldSelected) {
      var fieldInfo = fieldSelected.data("fieldinfo");
      var type = fieldInfo.type;
      if (type == "reference" || type == "multireference") {
        return "V";
      }
      return fieldSelected.data("fieldtype");
    },

    /**
     * Function which will return set of condition for the given field type
     * @return array of conditions
     */
    getConditionListFromType: function (fieldType) {
      var fieldTypeConditions = this.fieldTypeConditionMapping[fieldType];
      if (fieldType == "D" || fieldType == "DT") {
        fieldTypeConditions = fieldTypeConditions.concat(
          this.getDateConditions(fieldType)
        );
      }
      return fieldTypeConditions;
    },

    getDateConditions: function (fieldType) {
      if (fieldType != "D" && fieldType != "DT") {
        return new Array();
      }
      var dateFilters = this.getDateSpecificConditionInfo();
      return Object.keys(dateFilters);
    },

    getConditionLabel: function (key) {
      if (key in this.conditonOperatorLabelMapping) {
        return this.conditonOperatorLabelMapping[key];
      }
      if (key in this.getDateSpecificConditionInfo()) {
        return this.getDateSpecificConditionInfo()[key]["label"];
      }
      return key;
    },

    /**
     * Functiont to get the field specific ui for the selected field
     * @prarms : fieldSelectElement - select element which will represents field list
     * @return : jquery object which represents the ui for the field
     */
    getFieldSpecificUi: function (fieldSelectElement) {
      var selectedOption = fieldSelectElement.find("option:selected");
      var fieldModel = this.fieldModelInstance;
      if (fieldModel.getType().toLowerCase() == "boolean") {
        var conditionRow = fieldSelectElement.closest(".conditionRow");
        var selectedValue = conditionRow.find('[data-value="value"]').val();
        var html =
          '<select class="select2 col-lg-12" name="' +
          fieldModel.getName() +
          '">';
        html += '<option value="0"';
        if (selectedValue == "0") {
          html += ' selected="selected" ';
        }
        html += ">" + app.vtranslate("JS_IS_DISABLED") + "</option>";

        html += '<option value="1"';
        if (selectedValue == "1") {
          html += ' selected="selected" ';
        }
        html += ">" + app.vtranslate("JS_IS_ENABLED") + "</option>";
        html += "</select>";
        return jQuery(html);
      } else if (fieldModel.getType().toLowerCase() == "reference") {
        var html =
          '<input class="inputElement" type="text" name="' +
          fieldModel.getName() +
          '" data-label="' +
          fieldModel.get("label") +
          '" data-rule-' +
          fieldModel.getType() +
          "=true />";
        html = jQuery(html).val(app.htmlDecode(fieldModel.getValue()));
        return jQuery(html);
      } else {
        console.log("FIELDMODEL: ");
        console.log(fieldModel);
        const fieldHtml = jQuery(fieldModel.getUiTypeSpecificHtml());

        if (fieldHtml.data("value") !== undefined) {
          console.log("FIELDMODEL VALUE: ", fieldModel.value);
          console.log("Có thuộc tính data-value:", fieldHtml.data("value"));
        } else {
          console.log("Không có thuộc tính data-value");
        }
        return jQuery(fieldModel.getUiTypeSpecificHtml());
      }
    },
  }
);

Vtiger_Field_Js(
  "AdvanceFilter_Field_Js",
  {},
  {
    getUiTypeSpecificHtml: function () {
      var uiTypeModel = this.getUiTypeModel();
      return uiTypeModel.getUi();
    },

    getModuleName: function () {
      var currentModule = app.getModuleName();

      var type = this.getType();
      if (
        type == "picklist" ||
        type == "multipicklist" ||
        type == "owner" ||
        type == "ownergroup" ||
        type == "date" ||
        type == "datetime" ||
        type == "currencyList"
      ) {
        currentModule = "AdvanceFilter";
      }
      return currentModule;
    },
  }
);
//End Tran Dien

// Add by Tran Dien on 2025-03-03 to override getModuleName function

//End Tran Dien

//http://stackoverflow.com/questions/946534/insert-text-into-textarea-with-jquery
jQuery.fn.extend({
  insertAtCaret: function (myValue) {
    return this.each(function (i) {
      if (document.selection) {
        //For browsers like Internet Explorer
        this.focus();
        var sel = document.selection.createRange();
        sel.text = myValue;
        this.focus();
      } else if (this.selectionStart || this.selectionStart == "0") {
        //For browsers like Firefox and Webkit based
        var startPos = this.selectionStart;
        var endPos = this.selectionEnd;
        var scrollTop = this.scrollTop;
        this.value =
          this.value.substring(0, startPos) +
          myValue +
          this.value.substring(endPos, this.value.length);
        this.focus();
        this.selectionStart = startPos + myValue.length;
        this.selectionEnd = startPos + myValue.length;
        this.scrollTop = scrollTop;
      } else {
        this.value += myValue;
        this.focus();
      }
    });
  },
});
