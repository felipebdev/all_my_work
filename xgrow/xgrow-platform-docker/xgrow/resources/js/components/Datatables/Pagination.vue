<template>
    <div class="xgrow-paginator gap-3 mt-3 justify-content-center justify-content-sm-between">
        <div class="xgrow-paginator-itens">
            <select v-model="limitItens" @change="onChangeLimit" v-show="showChangeLimit">
                <option value="10">10 itens por página</option>
                <option value="25">25 itens por página</option>
                <option value="50">50 itens por página</option>
                <option value="100">100 itens por página</option>
            </select>
        </div>

        <ul class="pagination">
            <li class="paginator-number d-none d-md-block">
                <button type="button" @click="onClickFirstPage" :disabled="isInFirstPage"
                    class="paginator-number__item-outline" aria-label="Ir para primeira página">
                    <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M0.1999 6.99999C0.1999 6.76103 0.291141 6.5221 0.473239 6.33991L6.20634 0.606866C6.57104 0.242169 7.16233 0.242169 7.52688 0.606866C7.89143 0.971415 7.89143 1.56259 7.52688 1.92732L2.45391 6.99999L7.5267 12.0727C7.89125 12.4374 7.89125 13.0285 7.5267 13.393C7.16215 13.7579 6.57086 13.7579 6.20617 13.393L0.473061 7.66007C0.290934 7.47779 0.1999 7.23886 0.1999 6.99999Z"
                            fill="#ADDF45" />
                    </svg>
                </button>
            </li>

            <li v-for="(page, index) in pages" class="paginator-number" :key="index">
                <button type="button" @click="onClickPage(page.name)" :disabled="page.isDisabled"
                    class="paginator-number__item" :class="{ active: isPageActive(page.name) }"
                    :aria-label="`Ir para ${page.name}`">
                    {{ page.name }}
                </button>
            </li>

            <li class="paginator-number d-none d-md-block">
                <button type="button" @click="onClickLastPage" :disabled="isInLastPage" aria-label="Ir para última página"
                    class="paginator-number__item-outline">
                    <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M7.8001 6.99998C7.8001 7.23894 7.70886 7.47787 7.52676 7.66006L1.79366 13.3931C1.42896 13.7578 0.837668 13.7578 0.473119 13.3931C0.10857 13.0286 0.10857 12.4374 0.473119 12.0727L5.54609 6.99998L0.473295 1.92728C0.108746 1.56258 0.108746 0.971468 0.473295 0.606949C0.837844 0.242075 1.42914 0.242075 1.79383 0.606949L7.52694 6.3399C7.70907 6.52218 7.8001 6.76111 7.8001 6.99998Z"
                            fill="#ADDF45" />
                    </svg>
                </button>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: "Pagination",
    emits: ["pageChanged", "limitChanged"],
    props: {
        maxVisibleButtons: {
            type: Number,
            required: false,
            default: 3,
        },
        offset: {
            type: [String, Number],
            default: 0,
        },
        totalPages: {
            type: Number,
            required: true,
        },
        total: {
            type: Number,
            required: true,
        },
        currentPage: {
            type: Number,
            required: true,
        },
        limitItens: {
            type: [String, Number],
            required: false,
            default: 25,
        },
        showChangeLimit: {
            type: Boolean,
            default: true,
        }
    },
    computed: {
        startPage() {
            if (this.currentPage === 1) {
                return 1;
            }

            if (this.currentPage === this.totalPages) {
                return this.totalPages - this.maxVisibleButtons + 1;
            }

            return this.currentPage - 1;
        },
        endPage() {
            return Math.min(
                this.startPage + this.maxVisibleButtons - 1,
                this.totalPages
            );
        },
        pages() {
            const range = [];

            for (let i = this.startPage; i <= this.endPage; i += 1) {
                if (i > 0) {
                    range.push({
                        name: i,
                        isDisabled: i === this.currentPage,
                    });
                }
            }

            return range;
        },
        isInFirstPage() {
            return this.currentPage === 1;
        },
        isInLastPage() {
            return this.currentPage === this.totalPages;
        },
    },
    methods: {
        onChangeLimit() {
            this.$emit("limitChanged", parseInt(this.limitItens));
        },
        onClickFirstPage() {
            this.$emit("pageChanged", 1);
        },
        onClickPreviousPage() {
            this.$emit("pageChanged", this.currentPage - 1);
        },
        onClickPage(page) {
            this.$emit("pageChanged", page);
        },
        onClickNextPage() {
            this.$emit("pageChanged", this.currentPage + 1);
        },
        onClickLastPage() {
            this.$emit("pageChanged", this.totalPages);
        },
        isPageActive(page) {
            return this.currentPage === page;
        },
    },
};
</script>

<style lang="scss" scoped>
.pagination {
    list-style-type: none;
    flex-wrap: wrap;
}

.xgrow-paginator {
    display: flex;
    flex-wrap: wrap;
}

.xgrow-paginator-itens>select {
    border: 1px solid #646D85;
    padding: 9px 14px;
    border-radius: 8px;
    background: #252932;
    color: white;
    appearance: none;
    -moz-appearance: none;
    -webkit-appearance: none;
    padding-right: 32px;
    background-image: url('/xgrow-vendor/assets/img/icons/arrow-down.svg');
    background-repeat: no-repeat;
    background-position: calc(100% - 10px) center;
    background-size: 10px;
    font-size: 14px;
}

.paginator-number {
    margin: 0 4px;
    display: inline-block;

    &__item {
        color: #ADDF45 !important;
        font-weight: 700;
        font-size: 0.85rem;
        width: 32px;
        height: 32px;
        background: transparent;
        border: 1px solid #CECACA;
        border-radius: 6px;

        &:disabled {
            color: #FFFFFF !important;
            border: 1px solid #ADDF45;
            background-color: #ADDF45;
            cursor: not-allowed;
        }

        &-outline,
        &-outline:disabled {
            color: #ADDF45 !important;
            font-weight: 700;
            font-size: 0.85rem;
            width: 32px;
            height: 32px;
            background: transparent;
            border: 1px solid #CECACA;
            border-radius: 6px;
        }
    }
}
</style>
