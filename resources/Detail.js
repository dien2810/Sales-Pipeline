/*
    File: Config.js
    Author: The Vi
    Date: 22/1/2025
    Purpose: Handle events for Pipeline configuration interface
*/
CustomView_BaseController_Js(
  "Settings_PipelineConfig_Detail_Js",
  {},
  {
    registerEvents: function () {
      // $(".custom-popover").customPopover();
      this._super();
      this.registerEventFormInit();
    },
    registerEventFormInit: function () {
      let self = this;
      // alert("Register event form init");
      // Wait for DOM to be fully loaded before initializing popovers
      $(document).ready(function () {
        // Initialize popovers for existing elements
        self.initializePopovers();
        // Set up observer for dynamically added elements
        self.observeDOM();
      });
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
  }
);
