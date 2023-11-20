<template>
    <div class="gamification-card">
        <div class="gamification-card-header">
            <p>Fase {{ level }}</p>
            <div>
                <span>{{ resumeDetail(name, 12) }}</span>
                <i
                    class="fa fa-ellipsis-v"
                    :id="'dropdownMenuButton' + id"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                ></i>
                <ul class="dropdown-menu table-menu level-dropdown-menu" :aria-labelledby="'dropdownMenuButton' + id">
                    <li>
                        <a class="dropdown-item table-menu-item" href="javascript:void(0)" @click="edit(id)">
                            Editar
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item table-menu-item" href="javascript:void(0)" @click="remove(id)">
                            Excluir
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="gamification-card-image">
            <img :src="getImage" alt="Ícone da fase"/>
        </div>
        <div class="gamification-card-details">
            <p class="text-uppercase" style="color: #2a2e39">detalhes</p>
            <div style="color: #e7e7e7; padding-top: 6px">
                <p>Pontuação:
                    <img src="/xgrow-vendor/assets/img/gamification/coin.svg" alt="Xcoin"/>
                    {{ score }} Xcoins
                </p>
                <div class="d-flex align-items-center gap-2">
                    <p>Cor:</p>
                    <span
                        class="gamification-card-details-color"
                        :style="'background-color: ' + color + ';'"
                    ></span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "LevelCard",
    props: {
        id: {required: true, type: [String, Number]},
        level: {required: true, type: [String, Number]},
        name: {required: true, type: String},
        score: {required: true, type: [String, Number]},
        color: {required: true, type: String},
        cover: {required: false, type: String}
    },
    data() {
        return {};
    },
    emits: ["edit", "remove"],
    computed: {
        /**
         * Default image
         */
        getImage() {
            const blankImage = "/xgrow-vendor/assets/img/gamification/blank.svg";
            return this.cover ? this.cover : blankImage;
        }
    },
    methods: {
        // Edit level
        edit: function (id) {
            this.$emit("edit", id);
        },
        // Delete level
        remove: function (id) {
            this.$emit("remove", id);
        },
        // Resume the description for short sentence
        resumeDetail: function (resume, length = 14) {
            const short = resume.substring(0, length);
            return resume.length > short.length ? short + "..." : resume;
        }
    }
};
</script>

<style scoped>
.gamification-card {
    background: linear-gradient(#2a2e39, #2a2e39) padding-box,
    linear-gradient(130deg, #9dd940 0%, #9dd94040 100%) border-box;
    border: solid 5px transparent;
    border-radius: 8px;
    width: 252px;
    height: 279px;
    padding: 10px;
}

.gamification-card-header {
    background: #4e5569;
    border-radius: 4px;
    align-items: center;
    display: flex;
    padding: 5px 10px;
    flex-wrap: wrap;
}

.gamification-card-header > p {
    flex-grow: 1;
    font-weight: bold;
    max-width: 60px;
}

.gamification-card-header > div {
    flex-grow: 5;
    align-items: center;
    display: flex;
    justify-content: space-between;
    border-left: solid 1px #43495a;
    padding-left: 10px;
    font-weight: 300;
}

.gamification-card-header > div > i {
    cursor: pointer;
    padding: 2px 10px;
}

.gamification-card-image {
    background: white;
    border-radius: 4px;
    margin: 10px 0;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100px;
}

.gamification-card-image > img {
    width: 100%;
    height: 100%;
    object-fit: none;
}

.gamification-card-details {
    background-color: #4e5569;
    border-radius: 4px;
    height: 95px;
    padding: 10px;
}

.gamification-card-details-color {
    height: 16px;
    width: 16px;
    border-radius: 50%;
    border: solid 1px;
    display: inline-block;
}

.table-menu,
.table-menu-item {
    background-color: #383d4e !important;
}

.level-dropdown-menu.show {
    left: -122px !important;
    top: 3px !important;
}
</style>
