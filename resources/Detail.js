const urlParams = new URLSearchParams(window.location.search);
const record = urlParams.get("record");
CustomView_BaseController_Js(
  "Settings_PipelineConfig_Detail_Js",
  {},
  {
    stagesList: [],
    record: record,
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
          app.vtranslate("JS_ONCE_ACTION")
      );
      let actionTypeCondition = Array.from(
        actionBox.querySelectorAll(".action-type")
      ).find(
        (el) =>
          el.querySelector("h5").textContent.trim() ===
          app.vtranslate("JS_ACTION_WITH_CONDITION")
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
            <i class="fal fa-times removeAction"></i>
        `;
      if (action.frequency === "onceAction") {
        if (!actionTypeOnce) {
          let newActionType = document.createElement("div");
          newActionType.classList.add("action-type");
          newActionType.innerHTML = `
                <h5 class="action-title">${app.vtranslate(
                  "JS_ONCE_ACTION"
                )}</h5>
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
                <h5 class="action-title">${app.vtranslate(
                  "JS_ACTION_WITH_CONDITION"
                )}</h5>
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
            // Make sure the stage has an object conditions property before pushing
            stage.conditions = condition.filterInfo || {};
          }
          console.log(this.stagesList);
          // Find all tags with class "condition-box" and check the condition
          let conditionBox = document.querySelector(
            `.condition-box[data-stageid='${condition["stageid"]}']`
          );
          // If a matching tag exists, update its contents.
          jQuery(conditionBox).html(`
            <div class="action-item btnAddCondition" data-stageid="${
              condition.stageid
            }" data-conditions='${JSON.stringify(condition)}'> 
                <i class="fal fa-cogs ml-2"></i>
                <p class="text-primary pt-3">${app.vtranslate(
                  "JS_STEP_TRANSITION_CONDITIONS"
                )}</p>
                <i class="fal fa-times removeCondition"></i>
            </div>
        `);
        }
      }
    },
    registerEvents: function () {
      this._super();
      this.registerEventFormInit();
    },
    registerEventFormInit: function () {
      let self = this;
      $(document).ready(function () {
        self.initializePopovers();
        self.observeDOM();
      });
      self.initialDataStage();
      self.renderStageCrumbs(self.stagesList);
      self.renderStagesInfo(self.stagesList);
    },
    initialDataStage: function () {
      let self = this;
      let params = {
        module: "PipelineConfig",
        parent: "Settings",
        action: "Detail",
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
            message: err.message,
          });
          return;
        }
        console.log("RESPonse", response);
        const decodedResponse = decodeAllStrings(response);
        if (decodedResponse) {
          let self = this;
          self.stagesList = decodedResponse.stagesList.map((stage) => {
            let decodedActions = stage.actions;
            try {
              if (typeof stage.actions === "string") {
                decodedActions = JSON.parse(decodeHTMLEntities(stage.actions));
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
        }
        self.renderStageCrumbs(self.stagesList);
        self.renderStagesInfo(self.stagesList);
      });
    },
    initializePopovers: function () {
      $(".custom-popover").each(function () {
        let $trigger = $(this);
        let $wrapper = $trigger.closest(".custom-popover-wrapper");
        // Lấy nội dung danh sách từ phần tử ẩn trong wrapper
        let contentHtml = $wrapper.find(".custom-popover-content").html();
        // Gán thuộc tính data-content để đảm bảo popover có nội dung
        $trigger.attr("data-content", contentHtml);
        // Khởi tạo popover với option content được override
        $trigger.customPopover({
          content: contentHtml,
        });
      });
    },
    observeDOM: function () {
      const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
          $(mutation.addedNodes).each((i, node) => {
            if (node.nodeType === 1) {
              let popovers = $(node).find(".custom-popover");
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
        actionBox.innerHTML = `<button type="button" class="btn text-primary btnAddAction" data-stageid="${
          stage.id
        }">+ ${app.vtranslate("JS_ADD_ACTION_SETTINGS")}</button>`;

        const conditionBox = document.createElement("div");
        conditionBox.classList.add("condition-box");
        conditionBox.dataset.stageid = stage.id;
        conditionBox.innerHTML = `<button type="button" class="btn text-primary btnAddCondition" data-stageid="${
          stage.id
        }">+ ${app.vtranslate("JS_ADD_CONDITION")}</button>`;

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
  }
);
