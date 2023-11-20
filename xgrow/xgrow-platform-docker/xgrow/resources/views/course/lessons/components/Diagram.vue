<script>
let $ = go.GraphObject.make;

function makeImagePath(icon) {
  return "/images/gojs/" + icon;
}

export default {
  template: "<div></div>",
  props: ["modelData"],
  mounted: function () {
    let self = this;
    go.Diagram.licenseKey = gjs;
    let myDiagram = $(go.Diagram, this.$el, {
      // layout: $(go.TreeLayout, {angle: 90, arrangement: go.TreeLayout.ArrangementHorizontal}),
      "undoManager.isEnabled": true,
      // Model ChangedEvents get passed up to component users
      ModelChanged: function (e) {
        self.$emit("model-changed", e);
      },
      ChangedSelection: function (e) {
        self.$emit("changed-selection", e);
      },
    });

    function textStyle() {
      return {
        font: "bold 11pt Lato, Helvetica, Arial, sans-serif",
        stroke: "#F8F8F8",
      };
    }

    function makePortSquare(name, align, spot, output, input) {
      var horizontal =
        align.equals(go.Spot.Top) || align.equals(go.Spot.Bottom);
      return $(go.Shape, {
        fill: "transparent",
        strokeWidth: 0,
        width: horizontal ? NaN : 8,
        height: !horizontal ? NaN : 8,
        alignment: align,
        stretch: horizontal
          ? go.GraphObject.Horizontal
          : go.GraphObject.Vertical,
        portId: name,
        fromSpot: spot,
        fromLinkable: output,
        toSpot: spot,
        toLinkable: input,
        cursor: "pointer",
        mouseEnter: function (e, port) {
          if (!e.diagram.isReadOnly) port.fill = "rgba(255,0,255,0.5)";
        },
        mouseLeave: function (e, port) {
          port.fill = "transparent";
        },
      });
    }

    function makePort(name, align, spot, output, input) {
      let horizontal =
        align.equals(go.Spot.Top) || align.equals(go.Spot.Bottom);
      return $(go.Shape, {
        fill: "transparent", // changed to a color in the mouseEnter event handler
        strokeWidth: 0, // no stroke
        width: horizontal ? NaN : 8, // if not stretching horizontally, just 8 wide
        height: !horizontal ? NaN : 8, // if not stretching vertically, just 8 tall
        alignment: align, // align the port on the main Shape
        stretch: horizontal
          ? go.GraphObject.Horizontal
          : go.GraphObject.Vertical,
        portId: name, // declare this object to be a "port"
        fromSpot: spot, // declare where links may connect at this port
        fromLinkable: output, // declare whether the user may draw links from here
        toSpot: spot, // declare where links may connect at this port
        toLinkable: input, // declare whether the user may draw links to here
        cursor: "pointer", // show a different cursor to indicate potential link point
        mouseEnter: function (e, port) {
          // the PORT argument will be this Shape
          if (!e.diagram.isReadOnly) port.fill = "rgba(255,0,255,0.5)";
        },
        mouseLeave: function (e, port) {
          port.fill = "transparent";
        },
      });
    }

    // myDiagram.nodeTemplate = $(go.Node, 'Auto',
    //   $(go.Shape,
    //     {
    //       fill: 'white', strokeWidth: 0,
    //       portId: '', fromLinkable: true, toLinkable: true, cursor: 'pointer'
    //     }, new go.Binding('fill', 'color')),
    //   $(go.TextBlock, {margin: 8, editable: true}, new go.Binding('text').makeTwoWay())
    // );

    // TODO Mudar para ao soltar
    function modelUpdate() {
      myDiagram.startTransaction();
      myDiagram.updateAllRelationshipsFromData();
      myDiagram.updateAllTargetBindings();
      myDiagram.commitTransaction("updated");
    }

    // Default Node Template
    myDiagram.nodeTemplate = $(
      go.Node,
      "Table",
      {
        linkValidation: function (fromnode, fromport, tonode, toport) {
          return (
            fromnode.linksConnected.count <= 1 &&
            tonode.linksConnected.count <= 1
          );
        },
        deletable: false,
        cursor: "move",
      },
      new go.Binding("location", "loc", go.Point.parse).makeTwoWay(
        go.Point.stringify
      ),
      $(
        go.Panel,
        "Auto",
        { background: "transparent" },
        new go.Binding("fromLinkable", "from"),
        $(go.Shape, "RoundedRectangle", {
          fill: "#343945",
          width: 192,
          height: 82,
          strokeWidth: 0,
          fromLinkable: true,
          toLinkable: true,
          cursor: "pointer",
          portId: "",
          fromLinkableDuplicates: false,
          toLinkableDuplicates: false,
        }),
        $(
          go.Panel,
          "Vertical",
          { cursor: "default" },
          $(
            go.Panel,
            "Horizontal",
            {
              margin: new go.Margin(20, 0, 10, 0),
              stretch: go.GraphObject.Horizontal,
            },
            $(
              go.Picture,
              {
                width: 16,
                height: 15,
                margin: new go.Margin(0, 10, 0, 8),
                click: self.openModal,
              },
              new go.Binding("source", "icon", makeImagePath)
            ),
            $(
              go.TextBlock,
              {
                stroke: "#FFF",
                editable: false,
                font: "normal normal 500 16px Open Sans",
                textAlign: "left",
                click: self.openModal,
                cursor: "pointer",
              },
              new go.Binding("text")
            )
          ),
          $(go.Shape, "RoundedRectangle", {
            fill: "#3C414E",
            strokeWidth: 0,
            width: 192,
            height: 2,
            cursor: "default",
          }),
          $(
            go.TextBlock,
            {
              margin: 12,
              stroke: "#DBDADA",
              editable: false,
              font: "normal normal 400 12px Open Sans",
              textAlign: "left",
              stretch: go.GraphObject.Horizontal,
              click: self.openModal,
              cursor: "pointer",
            },
            new go.Binding("text", "title")
          )
        )
      ),
      $(go.Shape, {
        fill: "#93BC1E",
        stroke: "#93BC1E",
        cursor: "grab",
        strokeWidth: 0,
        width: 192,
        height: 20,
        margin: new go.Margin(-82, 0, 0, 0),
      }),
      makePortSquare("L", go.Spot.Left, go.Spot.LeftSide, true, true),
      makePortSquare("R", go.Spot.Right, go.Spot.RightSide, true, true),
      makePortSquare("B", go.Spot.Bottom, go.Spot.BottomSide, true, false)
    );

    // Custom Palette (Models)
    let myPalette = $(go.Palette, "goJsPalette");

    // the Palette's node template is different from the main Diagram's
    myPalette.nodeTemplate = $(
      go.Node,
      "Vertical",
      $(
        go.Panel,
        "Auto",
        { background: "#343945" },
        { portId: "" },
        $(go.Shape, "RoundedRectangle", {
          fill: "#343945",
          stroke: "#343945",
          width: 141,
          height: 64,
        }),
        $(
          go.Panel,
          "Vertical",
          $(
            go.Picture,
            { width: 20, height: 22, margin: 4 },
            new go.Binding("source", "icon", makeImagePath)
          ),
          $(
            go.TextBlock,
            { textAlign: "center", stroke: "white", margin: 4 },
            new go.Binding("text")
          )
        )
      )
    );

    // Start node Template
    myDiagram.nodeTemplateMap.add(
      "start",
      $(
        go.Node,
        "Table",
        {
          linkValidation: function (fromnode, fromport, tonode, toport) {
            return fromnode.linksConnected.count < 1;
          },
          deletable: false,
        },
        new go.Binding("location", "loc", go.Point.parse).makeTwoWay(
          go.Point.stringify
        ),
        { locationSpot: go.Spot.Center },
        $(
          go.Panel,
          "Spot",
          $(go.Shape, "Circle", {
            desiredSize: new go.Size(70, 70),
            fill: "#93BC1E",
            strokeWidth: 0,
          }),
          $(go.TextBlock, "Start", textStyle(), new go.Binding("text"))
        ),
        makePort("L", go.Spot.Left, go.Spot.Left, true, false),
        makePort("R", go.Spot.Right, go.Spot.Right, true, false),
        makePort("B", go.Spot.Bottom, go.Spot.Bottom, true, false)
      )
    );

    myDiagram.nodeTemplateMap.add(
      "end",
      $(
        go.Node,
        "Table",
        {
          linkValidation: function (fromnode, fromport, tonode, toport) {
            return tonode.linksConnected.count < 1;
          },
          deletable: false,
        },
        new go.Binding("location", "loc", go.Point.parse).makeTwoWay(
          go.Point.stringify
        ),
        { locationSpot: go.Spot.Center },
        $(
          go.Panel,
          "Spot",
          $(go.Shape, "Circle", {
            desiredSize: new go.Size(60, 60),
            fill: "#EB5757",
            strokeWidth: 0,
          }),
          $(go.TextBlock, "End", textStyle(), new go.Binding("text"))
        ),
        makePort("T", go.Spot.Top, go.Spot.Top, false, true),
        makePort("L", go.Spot.Left, go.Spot.Left, false, true),
        makePort("R", go.Spot.Right, go.Spot.Right, false, true)
      )
    );

    myPalette.model.nodeDataArray = [
      {
        key: 1,
        loc: "0 0",
        text: "Conteúdo",
        icon: "file.png",
        category: "content",
        title: "",
        id: "",
      },
      {
        key: 2,
        loc: "0 0",
        text: "Vídeo",
        icon: "video.png",
        category: "video",
        title: "",
        id: "",
      },
      {
        key: 3,
        loc: "0 0",
        text: "Link",
        icon: "link.png",
        category: "link",
        title: "",
        id: "",
      },
      {
        key: 5,
        loc: "0 0",
        text: "Texto",
        icon: "text.png",
        category: "text",
        title: "",
        id: "",
      },
      {
        key: 4,
        loc: "0 0",
        text: "Arquivo",
        icon: "attach.png",
        category: "archive",
        title: "",
        id: "",
      },
      // {key: 'DECISÃO', color: '#343945'}
    ];

    // End Custom Palette (Models)

    myDiagram.linkTemplate = $(
      go.Link,
      {
        relinkableFrom: false,
        relinkableTo: true,
        routing: go.Link.Orthogonal,
        curve: go.Link.JumpOver,
      },
      $(go.Shape, { stroke: "#9B9B9B" }),
      $(go.Shape, { toArrow: "Line", stroke: "#9B9B9B", fill: "#9B9B9B" })
    );

    this.diagram = myDiagram;

    this.updateModel(this.modelData);
  },
  watch: {
    modelData: function (val) {
      this.updateModel(val);
    },
  },
  methods: {
    model: function () {
      return this.diagram.model;
    },
    updateModel: function (val) {
      if (val instanceof go.Model) {
        this.diagram.model = val;
      } else {
        let m = new go.GraphLinksModel();
        if (val) {
          for (let p in val) {
            m[p] = val[p];
          }
        }
        this.diagram.model = m;
      }
    },
    updateDiagramFromData: function () {
      this.diagram.startTransaction();
      this.diagram.updateAllRelationshipsFromData();
      this.diagram.updateAllTargetBindings();
      this.diagram.commitTransaction("updated");
    },
    openModal: function (e, obj) {
      this.$emit("obj", obj.part.data);
    },
    loadModel: function (e) {
      this.diagram.model = go.Model.fromJson(JSON.parse(e));
    },
    zoomIn: function () {
      this.diagram.commandHandler.increaseZoom();
      this.displayZoom();
    },
    zoomOut: function () {
      this.diagram.commandHandler.decreaseZoom();
      this.displayZoom();
    },
    displayZoom: function () {
      const zoom = Math.round(this.diagram.scale * 100);
      if (zoom <= 50) {
        this.diagram.scale = 0.5;
      }
      this.$emit("zoom", zoom);
    },
  },
};
</script>
