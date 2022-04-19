var lp = "./img/";
var plp = "//placehold.it/350x250/";
/* var images = [
  plp + "78c5d6/fff/image1.jpg",
  plp + "459ba8/fff/image2.jpg",
  plp + "79c267/fff/image3.jpg",
  plp + "c5d647/fff/image4.jpg",
  plp + "f28c33/fff/image5.jpg",
  plp + "e868a2/fff/image6.jpg",
  plp + "cc4360/fff/image7.jpg",
]; */
var postid = document.getElementById("postid").value;

var editor = grapesjs.init({
  avoidInlineStyle: 1,
  height: "100%",
  container: "#gjs",
  fromElement: 1,
  showOffsets: 1,
  assetManager: {
    embedAsBase64: 1,
    // assets: images,
  },
  selectorManager: {
    componentFirst: true,
  },
  styleManager: {
    clearProperties: 1,
  },
  storageManager: {
    type: "remote",
    stepsBeforeSave: 1,
    autosave: true, // Store data automatically
    autoload: true,
    urlStore: "http://localhost/inovacards/index.php/save-card/?id=" + postid,
    urlLoad: "http://localhost/inovacards/index.php/load-card/?id=" + postid,
    contentTypeJson: true,
    storeComponents: true,
    storeStyles: true,
    storeHtml: true,
    storeCss: true,
    headers: {
      "Content-Type": "application/json",
    },
    json_encode: {
      "gjs-html": [],
      "gjs-css": [],
      "gjs-assets": [],
      "gjs-components": [],
      "gjs-styles": [],
    },
  },
  i18n: {
    locale: "vi",
  },
  canvas: {
    styles: [
      "https://fonts.googleapis.com/css2?family=Alex+Brush&family=Dancing+Script:wght@400;500;600;700&family=Italianno&family=Qwigley&family=Lobster&family=Sansita+Swashed:wght@300;400;500;600;700;800;900&family=Srisakdi:wght@400;700&display=swap",
      "http://fonts.cdnfonts.com/css/uvf-aphrodite-pro",
    ],
  },
  plugins: [
    "grapesjs-lory-slider",
    "grapesjs-custom-code",
    "grapesjs-touch",
    "grapesjs-parser-postcss",
    "grapesjs-tui-image-editor",
    "grapesjs-style-bg",
    "gjs-preset-webpage",
    "gjs-plugin-ckeditor",
  ],
  pluginsOpts: {
    "grapesjs-lory-slider": {
      sliderBlock: {
        category: "Basic",
      },
    },
    "gjs-preset-webpage": {
      filestackOpts: null, //{ key: 'AYmqZc2e8RLGLE7TGkX3Hz' },
      aviaryOpts: false,
      blocksBasicOpts: {
        flexGrid: 1,
      },
      customStyleManager: [
        {
          name: "Thông số chung",
          open: false,
          buildProps: [
            "float",
            "display",
            "position",
            "top",
            "right",
            "left",
            "bottom",
          ],
          properties: [
            {
              name: "Căn chỉnh",
              property: "float",
              type: "radio",
              defaults: "none",
              list: [
                {
                  value: "none",
                  className: "fa fa-times",
                },
                {
                  value: "left",
                  className: "fa fa-align-left",
                },
                {
                  value: "right",
                  className: "fa fa-align-right",
                },
              ],
            },
            {
              property: "position",
              type: "select",
            },
          ],
        },
        {
          name: "Kích thước",
          open: true,
          buildProps: [
            "width",
            "flex-width",
            "height",
            "max-width",
            "min-height",
            "margin",
            "padding",
          ],
          properties: [
            {
              id: "flex-width",
              type: "integer",
              name: "Width",
              units: ["px", "%"],
              property: "flex-basis",
              toRequire: 1,
            },
            {
              property: "margin",
              properties: [
                {
                  name: "Trên",
                  property: "margin-top",
                },
                {
                  name: "Phải",
                  property: "margin-right",
                },
                {
                  name: "Dưới",
                  property: "margin-bottom",
                },
                {
                  name: "Trái",
                  property: "margin-left",
                },
              ],
            },
            {
              property: "padding",
              properties: [
                {
                  name: "Trên",
                  property: "padding-top",
                },
                {
                  name: "Phải",
                  property: "padding-right",
                },
                {
                  name: "Dưới",
                  property: "padding-bottom",
                },
                {
                  name: "Trái",
                  property: "padding-left",
                },
              ],
            },
          ],
        },
        {
          name: "Typography",
          open: false,
          buildProps: [
            "font-family",
            "font-size",
            "font-weight",
            "letter-spacing",
            "color",
            "line-height",
            "text-align",
            "text-decoration",
            "text-shadow",
          ],
          properties: [
            {
              name: "Font",
              property: "font-family",
              list: [
                { value: "Arial", name: "Arial" },
                { value: "Tahoma", name: "Tahoma" },
                { value: "Comic Sans MS", name: "Comic Sans MS" },
                { value: "Times New Roman", name: "Times New Roman" },
                { value: "Alex Brush", name: "Alex Brush" },
                { value: "Dancing Script", name: "Dancing Script" },
                { value: "Italianno", name: "Italianno" },
                { value: "Lobster", name: "Lobster" },
                { value: "Qwigley", name: "Qwigley" },
                { value: "UVF Aphrodite Pro", name: "UVF Aphrodite Pro" },
                { value: "Sansita Swashed", name: "Sansita Swashed" },
                { value: "Srisakdi", name: "Srisakdi" },
              ],
            },
            {
              name: "Đậm nhạt",
              property: "font-weight",
            },
            {
              name: "Màu sắc",
              property: "color",
            },
            {
              property: "text-align",
              type: "radio",
              defaults: "left",
              list: [
                {
                  value: "left",
                  name: "Trái",
                  className: "fa fa-align-left",
                },
                {
                  value: "center",
                  name: "Giữa",
                  className: "fa fa-align-center",
                },
                {
                  value: "right",
                  name: "Phải",
                  className: "fa fa-align-right",
                },
                {
                  value: "justify",
                  name: "Đều 2 bên",
                  className: "fa fa-align-justify",
                },
              ],
            },
            {
              property: "text-decoration",
              type: "radio",
              defaults: "none",
              list: [
                {
                  value: "none",
                  name: "None",
                  className: "fa fa-times",
                },
                {
                  value: "underline",
                  name: "underline",
                  className: "fa fa-underline",
                },
                {
                  value: "line-through",
                  name: "Line-through",
                  className: "fa fa-strikethrough",
                },
              ],
            },
            {
              property: "text-shadow",
              properties: [
                {
                  name: "X position",
                  property: "text-shadow-h",
                },
                {
                  name: "Y position",
                  property: "text-shadow-v",
                },
                {
                  name: "Blur",
                  property: "text-shadow-blur",
                },
                {
                  name: "Color",
                  property: "text-shadow-color",
                },
              ],
            },
          ],
        },
        {
          name: "Nền và viền",
          open: false,
          buildProps: [
            "opacity",
            "border-radius",
            "border",
            "box-shadow",
            "background-bg",
          ],
          properties: [
            {
              type: "slider",
              property: "opacity",
              defaults: 1,
              step: 0.01,
              max: 1,
              min: 0,
            },
            {
              property: "border-radius",
              properties: [
                {
                  name: "Trên",
                  property: "border-top-left-radius",
                },
                {
                  name: "Phải",
                  property: "border-top-right-radius",
                },
                {
                  name: "Dưới",
                  property: "border-bottom-left-radius",
                },
                {
                  name: "Trái",
                  property: "border-bottom-right-radius",
                },
              ],
            },
            {
              property: "box-shadow",
              properties: [
                {
                  name: "X position",
                  property: "box-shadow-h",
                },
                {
                  name: "Y position",
                  property: "box-shadow-v",
                },
                {
                  name: "Blur",
                  property: "box-shadow-blur",
                },
                {
                  name: "Spread",
                  property: "box-shadow-spread",
                },
                {
                  name: "Color",
                  property: "box-shadow-color",
                },
                {
                  name: "Shadow type",
                  property: "box-shadow-type",
                },
              ],
            },
            {
              id: "background-bg",
              property: "background",
              type: "bg",
            },
          ],
        },
        {
          name: "Bổ sung",
          open: false,
          buildProps: ["transition", "perspective", "transform"],
          properties: [
            {
              property: "transition",
              properties: [
                {
                  name: "Property",
                  property: "transition-property",
                },
                {
                  name: "Duration",
                  property: "transition-duration",
                },
                {
                  name: "Easing",
                  property: "transition-timing-function",
                },
              ],
            },
            {
              property: "transform",
              properties: [
                {
                  name: "Rotate X",
                  property: "transform-rotate-x",
                },
                {
                  name: "Rotate Y",
                  property: "transform-rotate-y",
                },
                {
                  name: "Rotate Z",
                  property: "transform-rotate-z",
                },
                {
                  name: "Scale X",
                  property: "transform-scale-x",
                },
                {
                  name: "Scale Y",
                  property: "transform-scale-y",
                },
                {
                  name: "Scale Z",
                  property: "transform-scale-z",
                },
              ],
            },
          ],
        },
        {
          name: "Flex",
          open: false,
          properties: [
            {
              name: "Flex Container",
              property: "display",
              type: "select",
              defaults: "block",
              list: [
                {
                  value: "block",
                  name: "Disable",
                },
                {
                  value: "flex",
                  name: "Enable",
                },
              ],
            },
            {
              name: "Flex Parent",
              property: "label-parent-flex",
              type: "integer",
            },
            {
              name: "Direction",
              property: "flex-direction",
              type: "radio",
              defaults: "row",
              list: [
                {
                  value: "row",
                  name: "Row",
                  className: "icons-flex icon-dir-row",
                  title: "Row",
                },
                {
                  value: "row-reverse",
                  name: "Row reverse",
                  className: "icons-flex icon-dir-row-rev",
                  title: "Row reverse",
                },
                {
                  value: "column",
                  name: "Column",
                  title: "Column",
                  className: "icons-flex icon-dir-col",
                },
                {
                  value: "column-reverse",
                  name: "Column reverse",
                  title: "Column reverse",
                  className: "icons-flex icon-dir-col-rev",
                },
              ],
            },
            {
              name: "Justify",
              property: "justify-content",
              type: "radio",
              defaults: "flex-start",
              list: [
                {
                  value: "flex-start",
                  className: "icons-flex icon-just-start",
                  title: "Start",
                },
                {
                  value: "flex-end",
                  title: "End",
                  className: "icons-flex icon-just-end",
                },
                {
                  value: "space-between",
                  title: "Space between",
                  className: "icons-flex icon-just-sp-bet",
                },
                {
                  value: "space-around",
                  title: "Space around",
                  className: "icons-flex icon-just-sp-ar",
                },
                {
                  value: "center",
                  title: "Center",
                  className: "icons-flex icon-just-sp-cent",
                },
              ],
            },
            {
              name: "Align",
              property: "align-items",
              type: "radio",
              defaults: "center",
              list: [
                {
                  value: "flex-start",
                  title: "Start",
                  className: "icons-flex icon-al-start",
                },
                {
                  value: "flex-end",
                  title: "End",
                  className: "icons-flex icon-al-end",
                },
                {
                  value: "stretch",
                  title: "Stretch",
                  className: "icons-flex icon-al-str",
                },
                {
                  value: "center",
                  title: "Center",
                  className: "icons-flex icon-al-center",
                },
              ],
            },
            {
              name: "Flex Children",
              property: "label-parent-flex",
              type: "integer",
            },
            {
              name: "Order",
              property: "order",
              type: "integer",
              defaults: 0,
              min: 0,
            },
            {
              name: "Flex",
              property: "flex",
              type: "composite",
              properties: [
                {
                  name: "Grow",
                  property: "flex-grow",
                  type: "integer",
                  defaults: 0,
                  min: 0,
                },
                {
                  name: "Shrink",
                  property: "flex-shrink",
                  type: "integer",
                  defaults: 0,
                  min: 0,
                },
                {
                  name: "Basis",
                  property: "flex-basis",
                  type: "integer",
                  units: ["px", "%", ""],
                  unit: "",
                  defaults: "auto",
                },
              ],
            },
            {
              name: "Align",
              property: "align-self",
              type: "radio",
              defaults: "auto",
              list: [
                {
                  value: "auto",
                  name: "Auto",
                },
                {
                  value: "flex-start",
                  title: "Start",
                  className: "icons-flex icon-al-start",
                },
                {
                  value: "flex-end",
                  title: "End",
                  className: "icons-flex icon-al-end",
                },
                {
                  value: "stretch",
                  title: "Stretch",
                  className: "icons-flex icon-al-str",
                },
                {
                  value: "center",
                  title: "Center",
                  className: "icons-flex icon-al-center",
                },
              ],
            },
          ],
        },
      ],
    },
    "gjs-plugin-ckeditor": {
      position: "center",
      options: {
        startupFocus: true,
        extraAllowedContent: "*(*);*{*}", // Allows any class and any inline style
        allowedContent: true, // Disable auto-formatting, class removing, etc.
        enterMode: CKEDITOR.ENTER_BR,
        extraPlugins: "sharedspace,justify,colorbutton,panelbutton,font",
        toolbar: [
          {
            name: "styles",
            items: ["Font", "FontSize"],
          },
          ["Bold", "Italic", "Underline", "Strike"],
          {
            name: "paragraph",
            items: ["NumberedList", "BulletedList"],
          },
          {
            name: "links",
            items: ["Link", "Unlink"],
          },
          {
            name: "colors",
            items: ["TextColor", "BGColor"],
          },
        ],
      },
    },
  },
});

editor.I18n.addMessages({
  en: {
    styleManager: {
      properties: {
        "background-repeat": "Repeat",
        "background-position": "Position",
        "background-attachment": "Attachment",
        "background-size": "Size",
      },
    },
  },
});

var pn = editor.Panels;
var modal = editor.Modal;
var cmdm = editor.Commands;
cmdm.add("canvas-clear", function () {
  if (confirm("Bạn có chắc muốn xoá hết chứ?")) {
    var comps = editor.DomComponents.clear();
    setTimeout(function () {
      localStorage.clear();
    }, 0);
  }
});
cmdm.add("set-device-desktop", {
  run: function (ed) {
    ed.setDevice("Desktop");
  },
  stop: function () {},
});
cmdm.add("set-device-tablet", {
  run: function (ed) {
    ed.setDevice("Tablet");
  },
  stop: function () {},
});
cmdm.add("set-device-mobile", {
  run: function (ed) {
    ed.setDevice("Mobile portrait");
  },
  stop: function () {},
});

// Simple warn notifier
var origWarn = console.warn;
toastr.options = {
  closeButton: true,
  preventDuplicates: true,
  showDuration: 250,
  hideDuration: 150,
};
console.warn = function (msg) {
  if (msg.indexOf("[undefined]") == -1) {
    toastr.warning(msg);
  }
  origWarn(msg);
};

// Add and beautify tooltips
[
  ["sw-visibility", "Borders"],
  ["preview", "Preview"],
  ["fullscreen", "Fullscreen"],
  ["export-template", "Export"],
  ["undo", "Undo"],
  ["redo", "Redo"],
  ["gjs-open-import-webpage", "Import"],
  ["canvas-clear", "Clear"],
].forEach(function (item) {
  pn.getButton("options", item[0]).set("attributes", {
    title: item[1],
    "data-tooltip-pos": "bottom",
  });
});
[
  ["open-sm", "Style Manager"],
  ["open-layers", "Layers"],
  ["open-blocks", "Blocks"],
].forEach(function (item) {
  pn.getButton("views", item[0]).set("attributes", {
    title: item[1],
    "data-tooltip-pos": "bottom",
  });
});
var titles = document.querySelectorAll("*[title]");

for (var i = 0; i < titles.length; i++) {
  var el = titles[i];
  var title = el.getAttribute("title");
  title = title ? title.trim() : "";
  if (!title) break;
  el.setAttribute("data-tooltip", title);
  el.setAttribute("title", "");
}

// Show borders by default
pn.getButton("options", "sw-visibility").set("active", 1);

// Store and load events
editor.on("storage:load", function (e) {
  // console.log('Loaded ', e)
});
editor.on("storage:store", function (e) {
  // console.log('Stored ', e.components);
});

// Do stuff on load
editor.on("load", function () {
  var $ = grapesjs.$;

  // Show logo with the version
  var logoCont = document.querySelector(".gjs-logo-cont");
  var logoPanel = document.querySelector(".gjs-pn-commands");
  logoPanel.appendChild(logoCont);

  // Load and show settings and style manager
  var openTmBtn = pn.getButton("views", "open-tm");
  openTmBtn && openTmBtn.set("active", 1);
  var openSm = pn.getButton("views", "open-sm");
  openSm && openSm.set("active", 1);

  // Add Settings Sector
  var traitsSector = $(
    '<div class="gjs-sm-sector no-select">' +
      '<div class="gjs-sm-title"><span class="icon-settings fa fa-cog"></span> Settings</div>' +
      '<div class="gjs-sm-properties" style="display: none;"></div></div>'
  );
  var traitsProps = traitsSector.find(".gjs-sm-properties");
  traitsProps.append($(".gjs-trt-traits"));
  $(".gjs-sm-sectors").before(traitsSector);
  traitsSector.find(".gjs-sm-title").on("click", function () {
    var traitStyle = traitsProps.get(0).style;
    var hidden = traitStyle.display == "none";
    if (hidden) {
      traitStyle.display = "block";
    } else {
      traitStyle.display = "none";
    }
  });

  // Open block manager
  var openBlocksBtn = editor.Panels.getButton("views", "open-blocks");
  openBlocksBtn && openBlocksBtn.set("active", 1);

});

editor.Panels.addButton("options", [
  {
    id: "save-db",
    className: "fa fa-floppy-o",
    command: "save-db",
    attributes: { title: "Lưu" },
  },
]);

// Add the command
editor.Commands.add("save-db", {
  run: function (editor, sender) {
    sender && sender.set("active", 0); // turn off the button
    editor.store();

    alert("Đã lưu!");
  },
});

const block = editor.BlockManager;

block.add('inovacards-wrapper-block', {
    label: 'Vỏ thiệp',
    content: {
        draggable: true,
        components: [{
            type: 'text',
            content: "Thêm thiệp mời",
            attributes:{
                id: 'inovacards-wrapper',
                className: 'column-type',
            }
        }]
    },
    category: 'Basic',
    attributes: {
        title: 'Insert wrapper block',
        class: "gjs-block fa fa-slideshare",
    }
});