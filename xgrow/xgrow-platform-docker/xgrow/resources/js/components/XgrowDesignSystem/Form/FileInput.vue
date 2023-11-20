<template>
    <input type="file" :id="id" hidden :ref="'file-' + refer" @change="sendFile" :accept="accept" />

    <div class="d-flex gap-3 align-items-center flex-wrap">
        <label :for="id">
            <i class="fa fa-upload"></i> {{ label }}
        </label>
        <span :id="`${id}-file-chosen`">Nenhum arquivo selecionado</span>
    </div>
</template>

<script>

export default {
    name: "FileInput",
    props: {
        id: {
            type: String,
            required: true
        },
        label: {
            type: String,
            default: ""
        },
        // Refer == Hack for ref
        refer: {
            type: String,
            required: true
        },
        icon: {
            type: String,
            default: null
        },
        iconColor: {
            type: String,
            default: '#ffffff'
        },
        disabled: {
            type: Boolean,
            default: false
        },
        accept: {
            type: String,
            default: 'image/*, file/*'
        },
    },
    emits: ['sendFile', 'sendPreview'],
    methods: {
        mountFileButton: function () {
            const actualBtn = document.getElementById(this.id);
            const fileChosen = document.getElementById(`${this.id}-file-chosen`);

            actualBtn.addEventListener('change', function () {
                fileChosen.textContent = this.files[0].name

            })
        },
        sendFile: function () {
            this.$emit('sendFile', {
                file: this.$refs['file-' + this.refer],
                name: 'file-' + this.refer,
            })
        },
        sendPreview: function (img) {
            this.$emit('sendPreview', img)
        }
    },
    mounted() {
        this.mountFileButton();
    }
};
</script>

<style scoped>
span {
    color: #FFFFFF;
}

label {
    background: #93BC1E;
    border-radius: 8px;
    color: #FFFFFF;
    padding: 8px 18px;
    cursor: pointer;
}

label:hover {
    background: #779a14;
}
</style>
