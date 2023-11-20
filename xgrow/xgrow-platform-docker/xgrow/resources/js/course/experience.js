import axios from "axios";
import Panel from "../../views/course/lessons/components/Panel";
import PanelItem from "../../views/course/lessons/components/PanelItem";
import PanelItemTimeline from "../../views/course/lessons/components/PanelItemTimeline";
import Diagram from "../../views/course/lessons/components/Diagram";
import 'vue-search-select/dist/VueSearchSelect.css'
import { ModelSelect } from 'vue-search-select'

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        contents: [],
        module: {
            id: null,
            title: null,
            steps: null,
        },
        authors: [],
        loadingFlow: true,
    },
    mutations: {
        ADD_CONTENT(state, obj) {
            state.contents = obj;
        },
        ADD_TITLE_FLOW(state, obj) {
            state.module.title = obj.title;
            state.module.steps = obj.steps;
            state.module.id = obj.id;
            state.module.diagram = obj.diagram;
        },
        ADD_AUTHOR(state, obj) {
            state.authors.push(obj);
        },
        TOGGLE_LOADING(state) {
            state.loadingFlow = !state.loadingFlow;
        },
    },
    actions: {
        add_contents({ commit }, obj) {
            commit("ADD_CONTENT", obj);
        },
        add_title_flow({ commit }, obj) {
            commit("ADD_TITLE_FLOW", obj);
        },
        add_authors({ commit }, arr) {
            arr.forEach(obj => {
                commit("ADD_AUTHOR", obj);
            });
        },
        toggle_loading({ commit }) {
            commit("TOGGLE_LOADING");
        },
    },
});

Vue.component("diagram", Diagram);
Vue.component("panel", Panel);
Vue.component("panel-item", PanelItem);
Vue.component("panel-item-timeline", PanelItemTimeline);
Vue.component("model-select", ModelSelect);

const app = new Vue({
    delimiters: ["[[", "]]"],
    el: "#courseExperience",
    store,
    data: {
        loading: true,
        diagramData: {  // passed to <diagram> as its modelData
            nodeDataArray: [
                { key: -1, loc: "-250 -330", text: "INÍCIO", category: "start" },
                { key: -2, loc: "360 170", text: "FIM", category: "end" },
            ],
            linkDataArray: [
                // {from: 1, to: 2},
            ],
        },
        activeIndex: 0,
        currentNode: null,
        savedModelText: "",
        counter: 1,
        counter2: 4,
        course: {
            id: null,
        },
        modules: [],
        allContents: [],
        screen: "noFlow",
        linkModal: {
            id: "",
            title: "",
            link: "https://",
            authorId: "",
            description: "",
        },
        contentModal: {
            id: "",
            title: "",
            contentId: "",
            authorId: "",
            description: "",
        },
        videoModal: {
            id: "",
            title: "",
            link: "https://",
            authorId: "",
            description: "",
        },
        archiveModal: {
            id: "",
            title: "",
            authorId: "",
            file: null,
        },
        textModal: {
            id: "",
            title: "",
            authorId: "",
            text: "",
        },
        authorModal: {
            name: "",
            email: "",
            curriculum: "",
        },
        stepList: [], // All contents flow;
        previousList: [],
        flow: {
            id: null,
            name: "",
        },
        diagramNode: {
            category: null,
            icon: null,
            key: 0,
            text: "",
            title: "",
        },
        confirmationModal: {
            symbol: "fa-question-circle",
            title: "Tem certeza que deseja sair?",
            text: "Caso você saia agora, todas as alterações serão perdidas.",
            cancelFunction: () => {
            },
            confirmFunction: () => {
            },
        },
        ignoreAlert: true,
        zoom: 100,
        contentOptionSelected: {
            value: '',
            text: ''
        },
    },
    computed: {
        contents: function () {
            return this.$store.state.contents;
        },
        moduleTitleFlow: function () {
            return this.$store.state.module;
        },
        getCurrentNodeObj: function () {
            return this.$store.state.diagramNode;
        },
        authors: function () {
            return this.$store.state.authors;
        },
        loadingFlow: function () {
            return this.$store.state.loadingFlow;
        },
        currentNodeText: {
            get: function () {
                let node = this.currentNode;
                if (node instanceof go.Node) {
                    return node.data.text;
                } else {
                    return "";
                }
            },
            set: function (val) {
                let node = this.currentNode;
                if (node instanceof go.Node) {
                    let model = this.model();
                    model.startTransaction();
                    model.setDataProperty(node.data, "text", val);
                    model.commitTransaction("edited text");
                }
            },
        },
        contentOption: function () {
            let arr = [];
            this.allContents.forEach(content => {
                let obj = {
                    value: content.id,
                    text: content.title
                };
                arr.push(obj);
            });
            return arr;
        }
    },
    watch: {
        stepList: {
            handler(val, oldVal) {
                if (val.length != 0 && oldVal.length != 0) {
                    setTimeout(() => {
                        this.ignoreAlert = false;
                    }, 200);
                }
            },
            deep: true,
        },
        previousList: {
            handler(val, oldVal) {
                if (JSON.stringify(val) == JSON.stringify(this.stepList)) {
                    this.ignoreAlert = true;
                }
            },
            deep: true,
        },
        contentOptionSelected: {
            handler(val, oldVal) {
                this.contentModal.contentId = val.value;
            }
        },
        'contentModal.contentId': function () {
            this.changeContentAuthor();
        }
    },
    methods: {
        saveCurrentNodeObj: function (obj) {
            this.diagramNode.category = obj.category;
            this.diagramNode.icon = obj.icon;
            this.diagramNode.key = obj.key;
            this.diagramNode.text = obj.text;
            this.diagramNode.title = obj.title;
        },
        // get access to the GoJS Model of the GoJS Diagram
        model: function () {
            return this.$refs.diag.model();
        },
        // tell the GoJS Diagram to update based on the arbitrarily modified model data
        updateDiagramFromData: function () {
            this.$refs.diag.updateDiagramFromData();
        },
        // this event listener is declared on the <diagram>
        modelChanged: function (e) {
            // show the model data in the page's TextArea
            if (e.isTransactionFinished) {
                this.savedModelText = e.model.toJson();
            }
        },
        // this event listener select current node
        changedSelection: function (e) {
            let node = e.diagram.selection.first();
            if (node instanceof go.Node) {
                this.currentNode = node;
                this.currentNodeText = node.data.text;
            } else {
                this.currentNode = null;
                this.currentNodeText = "";
            }
        },
        // Verify if module exists and set first
        hasModule: async function (module) {
            if (this.modules.length > 0) {
                this.screen = "flow";
                const contents = await axios.get(getContents, { params: { module: module.id } });
                this.$store.dispatch("add_contents", contents.data.response.contents);
                this.$store.dispatch("add_title_flow", { id: module.id, title: module.name, steps: module.contents.length, diagram: module.diagram });
                this.$store.dispatch("toggle_loading");
            } else {
                this.screen = "noFlow";
            }
        },
        // Open diagram screen
        addFlow: async function () {
            this.screen = "diagram";
            this.stepList = [];
        },
        // Edit diagram screenloadDiagram (Load content onclick)
        editFlow: async function (moduleId) {
            let req;
            try {
                req = await axios.get(getModules, { params: { module: moduleId } });
            } catch (e) {
                errorToast("Ocorreu um erro.", e.response.data.message.toString());
            }
            const module = req.data.response.module;
            this.screen = "diagram";
            this.savedModelText = module.diagram;

            if (module.diagram !== null) {
                const pl = JSON.parse(module.diagram);
                this.diagramData.nodeDataArray = pl.nodeDataArray ?? [];
                this.diagramData.linkDataArray = pl.linkDataArray ?? [];

                if (pl.nodeDataArray.length > 2) {
                    let arrPromise = pl.nodeDataArray.map(async item => {
                        if (item.id !== "") {
                            let content;
                            try {
                                content = await axios.get(getContents, { params: { content: item.id } });
                            } catch (e) {
                                errorToast("Ocorreu um erro.", e.response.data.message.toString());
                            }

                            if (item.category === "link") {
                                this.stepList.push({
                                    title: content.data.response.content.title,
                                    link: content.data.response.content.external_link,
                                    authorId: content.data.response.content.author_id,
                                    description: content.data.response.content.description,
                                    category: content.data.response.content.category,
                                    key: item.key,
                                    id: content.data.response.content.id,
                                });
                            }

                            if (item.category === "video") {
                                this.stepList.push({
                                    title: content.data.response.content.title,
                                    link: content.data.response.content.video_link,
                                    authorId: content.data.response.content.author_id,
                                    description: content.data.response.content.description,
                                    category: content.data.response.content.category,
                                    key: item.key,
                                    id: content.data.response.content.id,
                                });
                            }

                            if (item.category === "content") {
                                this.stepList.push({
                                    title: content.data.response.content.title,
                                    authorId: content.data.response.content.author_id,
                                    contentId: content.data.response.content.content_id,
                                    description: content.data.response.content.description,
                                    category: content.data.response.content.category,
                                    key: item.key,
                                    id: content.data.response.content.id,
                                });
                            }

                            if (item.category === "text") {
                                this.stepList.push({
                                    title: content.data.response.content.title,
                                    authorId: content.data.response.content.author_id,
                                    text: content.data.response.content.content_html,
                                    category: content.data.response.content.category,
                                    key: item.key,
                                    id: content.data.response.content.id,
                                });
                            }

                            if (item.category === "archive") {
                                this.stepList.push({
                                    title: content.data.response.content.title,
                                    authorId: content.data.response.content.author_id,
                                    file: content.data.response.content.file_url,
                                    key: item.key,
                                    id: content.data.response.content.id,
                                });
                            }
                        }
                    });
                    Promise.all(arrPromise).then(() => {
                        this.previousList = [...this.stepList];
                    });
                }
            }
            this.flow.name = module.name;
            this.flow.id = moduleId;
        },
        // Save flow
        saveFlow: async function () {
            if (this.checkFlowIntegrity() || this.checkBlankItem()) {
                return true;
            }

            this.ignoreAlert = true;

            if (this.flow.name === "") {
                errorToast("Algum erro aconteceu!", "Nome do fluxo não pode ficar em branco!");
                return true;
            }

            if (this.diagramData.linkDataArray.length === 0) {
                errorToast("Algum erro aconteceu!", "O fluxo não pode ficar em branco!");
                return true;
            }

            if (this.stepList.length === 0) {
                errorToast("Algum erro aconteceu!", "1 ou mais itens estão em branco ou não estão conectados corretamente.");
                return true;
            }

            if (this.diagramData.linkDataArray.length === 1) {
                if (this.diagramData.linkDataArray[0].from === -1 && this.diagramData.linkDataArray[0].to === -2) {
                    errorToast("Algum erro aconteceu!", "Fluxo inválido!");
                    return true;
                }
            }

            // Create a FormData to send files data
            let formData = new FormData();
            formData.append('flowName', this.flow.name);
            formData.append('course', this.course.id);
            formData.append('diagram', this.savedModelText);

            if (this.flow.id) {
                formData.append('flowId', this.flow.id);
            }

            // Run through the stepList and create formData entries
            for (let i = 0; i < this.stepList.length; i++) {
                for (let [key, value] of Object.entries(this.stepList[i])) {
                    formData.append(`content[${i}][${key}]`, value);
                }
            }

            const req = await axios.post(
                postModule,
                formData,
                {
                    headers: {
                        "Content-Type": `multipart/form-data; boundary=${formData._boundary}`
                    }
                }
            );

            if (req.status === 200 || req.status === 201) {
                successToast("Ação realizada com sucesso.", req.data.message.toString());
                this.flow = { name: "", id: null };
                this.stepList = [];
                this.savedModelText = "";
                this.diagramNode = { category: null, icon: null, key: 0, text: "", title: "" };
                this.clearCurrentModal();
                this.$store.dispatch("toggle_loading");
                const modules = await axios.get(getModules, { params: { course: this.course.id } });
                this.modules = modules.data.response.modules;
                await this.hasModule(this.modules[0]);
                this.activeIndex = 0;
            } else {
                errorToast("Erro ao executar ação.", req.response.data.message.toString());
            }
        },
        // Save current modal data
        saveCurrentModal: async function () {
            let data = this.diagramData;
            let modalId = null;
            let nodeContext = this.diagramNode;
            let node = data.nodeDataArray.find(item => item.key === nodeContext.key);
            const index = this.stepList.findIndex(item => item.key === nodeContext.key);

            if (nodeContext.category === "link") {
                const ev = this.addLink(index);
                if (ev) return true;
                node.title = this.linkModal.title;
                modalId = "modalLink";
            }
            if (nodeContext.category === "video") {
                const ev = this.addVideo(index);
                if (ev) return true;
                node.title = this.videoModal.title;
                modalId = "modalVideo";
            }
            if (nodeContext.category === "content") {
                const ev = this.addContent(index);
                if (ev) return true;
                node.title = this.contentModal.title;
                modalId = "modalContent";
            }
            if (nodeContext.category === "archive") {
                const ev = this.addArchive(index);
                if (ev) return true;
                node.title = this.archiveModal.title;
                modalId = "modalArchive";
            }
            if (nodeContext.category === "text") {
                const ev = this.addText(index);
                if (ev) return true;
                node.title = this.textModal.title;
                modalId = "modalText";
            }
            this.updateDiagramFromData();
            this.closeModal(modalId);
        },
        // Save the author in backend and add in all dropdowns
        saveAuthorModal: function () {
            if (this.authorModal.name.trim() === "") {
                errorToast("Algum erro aconteceu!", "O nome não pode ficar em branco.");
                return true;
            }
            if (this.authorModal.email.trim() === "") {
                errorToast("Algum erro aconteceu!", "O email não pode ficar em branco.");
                return true;
            }
            const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if (!re.test(this.authorModal.email)) {
                errorToast("Algum erro aconteceu!", "O email precisa ser um email válido.");
                return true;
            }

            let formData = new FormData();
            formData.append("name", this.authorModal.name);
            formData.append("email", this.authorModal.email);
            formData.append("desc", this.authorModal.curriculum);

            axios({
                method: "post",
                url: createAuthor,
                params: { course: this.course.id },
                data: formData,
            })
                .then(response => {
                    if (!response.data.error) {
                        this.$store.dispatch(
                            "add_authors",
                            [{
                                id: response.data.response.author.id,
                                name: response.data.response.author.name_author,
                            }],
                        );
                        successToast("Sucesso!", response.data.message);
                        this.closeModal("modalAuthors");
                        this.authorModal.name = "";
                        this.authorModal.email = "";
                        this.authorModal.curriculum = "";
                    } else {
                        errorToast("Algum erro aconteceu!", response.data.message);
                    }
                })
                .catch(error => {
                    errorToast("Algum erro aconteceu!", "Ocorreu um erro interno, por favor contate o suporte");
                });
        },
        // Clear all data on current modal
        clearCurrentModal: function () {
            this.linkModal.id = "";
            this.linkModal.title = "";
            this.linkModal.link = "https://";
            this.linkModal.authorId = "";
            this.linkModal.description = "";

            this.contentModal.id = "";
            this.contentModal.title = "";
            this.contentModal.contentId = "";
            this.contentModal.authorId = "";
            this.contentModal.description = "";

            this.videoModal.id = "";
            this.videoModal.title = "";
            this.videoModal.link = "https://";
            this.videoModal.authorId = "";
            this.videoModal.description = "";

            this.archiveModal.id = "";
            this.archiveModal.title = "";
            this.archiveModal.authorId = "";
            this.archiveModal.file = null;

            this.textModal.id = "";
            this.textModal.title = "";
            this.textModal.authorId = "";
            this.textModal.text = "";

            this.authorModal.id = "";
            this.authorModal.name = "";
            this.authorModal.email = "";
            this.authorModal.curriculum = "";
        },
        // Close the modal by ID
        closeModal: function (modalId) {
            const modalEl = document.getElementById(modalId);
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
            this.clearCurrentModal();
        },
        // Retrieve obj by GoJs Obj
        objReceive: async function (obj) {
            let modalId = "#";
            let index = this.stepList.findIndex(item => item.key === obj.key);

            if (obj.category === "link") {
                if (index > -1) {
                    this.linkModal.id = this.stepList[index].id;
                    this.linkModal.title = this.stepList[index].title;
                    this.linkModal.link = this.stepList[index].link;
                    this.linkModal.authorId = this.stepList[index].authorId;
                    this.linkModal.description = this.stepList[index].description;
                }
                modalId = "modalLink";
            }

            if (obj.category === "content") {
                if (index > -1) {
                    this.contentModal.id = this.stepList[index].id;
                    this.contentModal.title = this.stepList[index].title;
                    this.contentModal.contentId = this.stepList[index].contentId;
                    this.contentModal.authorId = this.stepList[index].authorId;
                    this.contentModal.description = this.stepList[index].description;
                }
                modalId = "modalContent";
            }

            if (obj.category === "video") {
                if (index > -1) {
                    this.videoModal.id = this.stepList[index].id;
                    this.videoModal.title = this.stepList[index].title;
                    this.videoModal.link = this.stepList[index].link;
                    this.videoModal.authorId = this.stepList[index].authorId;
                    this.videoModal.description = this.stepList[index].description;
                }
                modalId = "modalVideo";
            }

            if (obj.category === "archive") {
                if (index > -1) {
                    this.archiveModal.id = this.stepList[index].id;
                    this.archiveModal.title = this.stepList[index].title;
                    this.archiveModal.authorId = this.stepList[index].authorId;
                    this.archiveModal.file = this.stepList[index].file;
                }
                modalId = "modalArchive";
            }

            if (obj.category === "text") {
                if (index > -1) {
                    this.textModal.id = this.stepList[index].id;
                    this.textModal.title = this.stepList[index].title;
                    this.textModal.authorId = this.stepList[index].authorId;
                    this.textModal.text = this.stepList[index].text;
                }
                modalId = "modalText";
            }

            if (modalId !== "#") {
                const linkModal = new bootstrap.Modal(document.getElementById(modalId), {});
                linkModal.show();
            }

            this.saveCurrentNodeObj(obj);
        },
        // Delete Node
        deleteObj: async function (modalId, objId = null) {
            let nodeContext = this.diagramNode;
            let node = this.$refs.diag.diagram.findNodeForKey(nodeContext.key);

            if (objId) {
                const content = await axios.delete(deleteContent, { data: { content: objId } });
                if (content.status === 201) {
                    successToast("Ação realizada com sucesso", content.data.message);
                }
            }
            this.stepList = this.stepList.filter(item => item.key !== nodeContext.key);
            this.$refs.diag.diagram.remove(node);
            this.updateDiagramFromData();
            this.closeModal(modalId);
        },
        readFile: function (evt) {
            this.archiveModal.file = evt.target.files[0];
        },
        // add*: Add new item to this.stepList array
        addLink: function (index) {
            if (this.linkModal.title.trim() === "") {
                errorToast("Algum erro aconteceu!", "O título não pode ficar em branco.");
                return true;
            }
            if (this.linkModal.link.trim() === "" || this.linkModal.link.trim() === "https://") {
                errorToast("Algum erro aconteceu!", "O link não pode ficar em branco.");
                return true;
            }
            if (!this.linkRegex(this.linkModal.link.trim())) {
                errorToast("Algum erro aconteceu!", "Link inválido.");
                return true;
            }
            if (this.linkModal.authorId === "" || this.linkModal.authorId === 0) {
                errorToast("Algum erro aconteceu!", "O autor não pode ficar em branco.");
                return true;
            }
            if (this.linkModal.description.trim() === "") {
                errorToast("Algum erro aconteceu!", "A descrição não pode ficar em branco.");
                return true;
            }

            if (index > -1) {
                this.stepList[index].title = this.linkModal.title;
                this.stepList[index].link = this.linkModal.link;
                this.stepList[index].authorId = this.linkModal.authorId;
                this.stepList[index].description = this.linkModal.description;
            } else {
                this.stepList.push({
                    title: this.linkModal.title,
                    link: this.linkModal.link,
                    authorId: this.linkModal.authorId,
                    description: this.linkModal.description,
                    category: "link",
                    key: this.diagramNode.key,
                    id: "",
                });
            }
        },
        // Regex for valid link
        linkRegex: function (val) {
            const expression = /(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/gi;
            const regex = new RegExp(expression);
            return val.match(regex);
        },
        // add*: Add new item to this.stepList array
        addVideo: function (index) {
            if (this.videoModal.title.trim() === "") {
                errorToast("Algum erro aconteceu!", "O título não pode ficar em branco.");
                return true;
            }
            if (this.videoModal.link.trim() === "" || this.videoModal.link.trim() === "https://") {
                errorToast("Algum erro aconteceu!", "O link não pode ficar em branco.");
                return true;
            }
            if (!this.linkRegex(this.videoModal.link.trim())) {
                errorToast("Algum erro aconteceu!", "Link inválido.");
                return true;
            }
            if (this.videoModal.authorId === "" || this.videoModal.authorId === 0) {
                errorToast("Algum erro aconteceu!", "O autor não pode ficar em branco.");
                return true;
            }
            if (this.videoModal.description.trim() === "") {
                errorToast("Algum erro aconteceu!", "A descrição não pode ficar em branco.");
                return true;
            }

            if (index > -1) {
                this.stepList[index].title = this.videoModal.title;
                this.stepList[index].link = this.videoModal.link;
                this.stepList[index].authorId = this.videoModal.authorId;
                this.stepList[index].description = this.videoModal.description;
            } else {
                this.stepList.push({
                    title: this.videoModal.title,
                    link: this.videoModal.link,
                    authorId: this.videoModal.authorId,
                    description: this.videoModal.description,
                    category: "video",
                    key: this.diagramNode.key,
                    id: "",
                });
            }
        },
        // add*: Add new item to this.stepList array
        addContent: function (index) {
            if (this.contentModal.title.trim() === "") {
                errorToast("Algum erro aconteceu!", "O título não pode ficar em branco.");
                return true;
            }
            if (this.contentModal.authorId === "" || this.contentModal.authorId === 0) {
                errorToast("Algum erro aconteceu!", "O autor não pode ficar em branco.");
                return true;
            }
            if (this.contentModal.contentId === "" || this.contentModal.contentId === 0) {
                errorToast("Algum erro aconteceu!", "O conteúdo não pode ficar em branco.");
                return true;
            }
            if (this.contentModal.description.trim() === "") {
                errorToast("Algum erro aconteceu!", "A descrição não pode ficar em branco.");
                return true;
            }

            if (index > -1) {
                this.stepList[index].title = this.contentModal.title;
                this.stepList[index].contentId = this.contentModal.contentId;
                this.stepList[index].authorId = this.contentModal.authorId;
                this.stepList[index].description = this.contentModal.description;
            } else {
                this.stepList.push({
                    title: this.contentModal.title,
                    contentId: this.contentModal.contentId,
                    authorId: this.contentModal.authorId,
                    description: this.contentModal.description,
                    category: "content",
                    key: this.diagramNode.key,
                    id: "",
                });
            }
        },
        // add*: Add new item to this.stepList array
        addArchive: function (index) {
            if (this.archiveModal.title.trim() === "") {
                errorToast("Algum erro aconteceu!", "O título não pode ficar em branco.");
                return true;
            }
            if (this.archiveModal.authorId === "" || this.archiveModal.authorId === 0) {
                errorToast("Algum erro aconteceu!", "O autor não pode ficar em branco.");
                return true;
            }
            if (this.archiveModal.file === null) {
                errorToast("Algum erro aconteceu!", "O arquivo não pode ficar em branco.");
                return true;
            }

            if (index > -1) {
                this.stepList[index].title = this.archiveModal.title;
                this.stepList[index].authorId = this.archiveModal.authorId;
                this.stepList[index].file = this.archiveModal.file;
            } else {
                this.stepList.push({
                    title: this.archiveModal.title,
                    authorId: this.archiveModal.authorId,
                    file: this.archiveModal.file,
                    category: "archive",
                    key: this.diagramNode.key,
                    id: "",
                });
            }
        },
        // add*: Add new item to this.stepList array
        addText: function (index) {
            if (this.textModal.title.trim() === "") {
                errorToast("Algum erro aconteceu!", "O título não pode ficar em branco.");
                return true;
            }
            if (this.textModal.authorId === "" || this.textModal.authorId === 0) {
                errorToast("Algum erro aconteceu!", "O autor não pode ficar em branco.");
                return true;
            }
            if (this.textModal.text.trim() === "") {
                errorToast("Algum erro aconteceu!", "O texto não pode ficar em branco.");
                return true;
            }

            if (index > -1) {
                this.stepList[index].title = this.textModal.title;
                this.stepList[index].authorId = this.textModal.authorId;
                this.stepList[index].text = this.textModal.text;
            } else {
                this.stepList.push({
                    title: this.textModal.title,
                    authorId: this.textModal.authorId,
                    text: this.textModal.text,
                    category: "text",
                    key: this.diagramNode.key,
                    id: "",
                });
            }
        },
        // New author
        callAuthorModal: function (evt) {
            if (evt.target.value === "__newauthor__") {
                const authorModal = new bootstrap.Modal(document.getElementById("modalAuthors"), {});
                authorModal.show();
            }
        },
        /**
         * Call this function passing an object as parameter, requiring only 2 attibutes:
         * 'cancelFunction' and 'confirmFunction'
         *
         * Example of the object parameter:
         *
         *    {
         *      symbol: 'fa-question-circle',
         *      title: 'Title of your alert',
         *      text: 'Description text of your alert',
         *      cancelText: 'cancel',
         *      cancelFunction: () => yourFunction(withOrWithoutParameters),
         *      confirmText: 'confirmation',
         *      confirmFunction: () => yourFunction(withOrWithoutParameters)
         *    }
         *
         * The attribute function needs to be passed with '() =>' before for right execution
         * The symbol, cancelText and cancelFunction has a default value
         */
        callConfirmationModal: function ({ symbol, title, text, cancelText, cancelFunction, confirmText, confirmFunction }) {
            this.confirmationModal.symbol = symbol || "fa-question-circle";
            this.confirmationModal.title = title;
            this.confirmationModal.text = text;
            this.confirmationModal.cancelText = cancelText || "Cancelar";
            this.confirmationModal.cancelFunction = cancelFunction || (() => {
            });
            this.confirmationModal.confirmText = confirmText;
            this.confirmationModal.confirmFunction = confirmFunction;

            const confirmationModal = new bootstrap.Modal(document.getElementById("modalConfirmation"), {});
            confirmationModal.show();
        },
        zoomIn: function () {
            this.$refs.diag.zoomIn();
        },
        zoomOut: function () {
            this.$refs.diag.zoomOut();
        },
        displayZoom: function (zoom) {
            this.zoom = zoom;
        },
        verifyModifiedData: async function () {
            const modalIsActived = document.getElementById("modalConfirmation").classList.contains("show");
            const element = document.getElementById("courseExperience");
            let mouseIsHovering = false;

            // Verify if mouse still hovering the vue div
            await new Promise(resolve => {
                setTimeout(() => {
                    if (element.parentNode.querySelector(":hover") == element) {
                        mouseIsHovering = true;
                    }
                    resolve();
                }, 700);
            });

            if (!this.ignoreAlert && !modalIsActived && !mouseIsHovering) {
                this.callConfirmationModal({
                    symbol: "fa-exclamation-triangle",
                    title: "Há modificações não salvas!",
                    text: "Antes que você saia, salve as modificações para não perder nenhum progresso feito.",
                    cancelText: "Sair mesmo assim",
                    cancelFunction: () => {
                        this.ignoreAlert = true;
                    },
                    confirmText: "Salvar modificações",
                    confirmFunction: this.saveFlow,
                });
            }
        },
        /** Check flow Integrity */
        checkFlowIntegrity: function () {
            let hasError = false;
            const nodes = this.diagramData.linkDataArray;
            const start = -1;
            const end = -2;

            let newNodes = [];
            nodes.forEach(node => newNodes.push([node.from, node.to]));

            /** Check if Start and End is connected */
            const startIndex = newNodes.findIndex(item => item[0] === start);
            const endIndex = newNodes.findIndex(item => item[1] === end);

            if (startIndex < 0) {
                errorToast("Diagrama inválido!", "Não há ligação no Início.");
                hasError = true;
                return true;
            }

            if (endIndex < 0) {
                errorToast("Diagrama inválido!", "Não há uma ligação no Fim.");
                hasError = true;
                return true;
            }
            /** END Check if Start and End is connected */

            /** Check flow Integrity */
            let countNodes = {};
            let dualNodes = [];
            newNodes.forEach(node => dualNodes.push(node[0], node[1]));
            dualNodes = dualNodes.filter(item => item !== start && item !== end);
            dualNodes.forEach((i) => countNodes[i] = (countNodes[i] || 0) + 1);

            Object.entries(countNodes).forEach(node => {
                if (node[1] < 2) {
                    errorToast("Diagrama inválido!", `Verifique se todos os itens foram ligados corretamente no fluxo.`);
                    hasError = true;
                    return false;
                }
            });
            /** END Check flow Integrity */

            this.updateDiagramFromData();

            return hasError;
        },
        checkBlankItem: function () {
            let getFlowElements = this.diagramData.nodeDataArray
                .filter(item => item.key !== -1 && item.key !== -2)
                .map(item => item.key);

            let hasError = false;
            getFlowElements.forEach(key => {
                const stepList = this.stepList.filter(item => key === item.key);
                if (stepList.length === 0 || this.categoryValidation(stepList)) {
                    errorToast("Não é possível salvar o fluxo.", "Verifique os itens se todos foram preenchidos corretamente.");
                    hasError = true;
                }
            });
            return hasError;
        },
        categoryValidation: function (item) {
            item = item[0];

            if (item.title.trim() === "") return true;
            if (item.authorId === "" || item.authorId === 0) return true;
            if (item.category === "content") {
                if (item.contentId === "" || item.contentId === 0) return true;
                if (item.description.trim() === "") return true;
            }
            if (item.category === "video") {
                if (item.link.trim() === "" || item.link.trim() === "https://") return true;
                if (!this.linkRegex(item.link.trim())) return true;
                if (item.description.trim() === "") return true;
            }
            if (item.category === "link") {
                if (item.link.trim() === "" || item.link.trim() === "https://") return true;
                if (!this.linkRegex(item.link.trim())) return true;
                if (item.description.trim() === "") return true;
            }
            if (item.category === "text") {
                if (item.text.trim() === "") return true;
            }
            if (item.category === "archive") {
                if (item.file === null) return true;
            }
            return false;
        },
        changeContentAuthor: function () {
            this.allContents.forEach(content => {
                if (content.id == this.contentModal.contentId) {
                    this.contentModal.authorId = content.author_id;
                }
            });
        }
    },
    async created() {
        this.course.id = course;
        const modules = await axios.get(getModules, { params: { course: this.course.id } });
        this.modules = modules.data.response.modules || [];

        await this.hasModule(this.modules[0]);

        const allContents = await axios.get(getContents, { params: { noCategory: true } });
        this.allContents = allContents.data.response.contents;

        const authors = await axios.get(getAuthors, { params: { course: this.course.id } });
        this.$store.dispatch("add_authors", authors.data.response.authors);
        this.loading = false;
    },
});
