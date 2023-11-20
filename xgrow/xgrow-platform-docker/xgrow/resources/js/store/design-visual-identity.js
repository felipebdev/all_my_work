import { defineStore } from "pinia";
import { useLoadingStore } from "./components/loading";
import axios from "axios";
import {
    emailRegex,
    urlRegex,
} from "../components/XgrowDesignSystem/Extras/functions";

export const useDesignVisualIdentity = defineStore("designVisualIdentity", {
    state: () => ({
        loadingStore: useLoadingStore(),
        theme: {
            primaryColor: "#FFC107",
            secondaryColor: "#E8C605",
            tertiaryColor: "#E89805",
            textColor: "#FFFFFF",
            inputColor: "#8F8F8F",
            backgroundType: "solid",
            backgroundColor: "#C4C4C4",
            backgroundGradientFirst: "#C4C4C4",
            backgroundGradientSecond: "#404040",
            backgroundGradientDegree: 45,
            backgroundImageUrl: "",
            borderRadius: 50,
            platformName: "",
            title: "",
            description: "",
            footer: "",
            keywords: "",
            useBanner: true,
            faviconUrl: null,
            logoUrl: null,
            supportType: "email",
            supportEmail: "",
            supportLink: "",
            supportPhone: "",
        },
        keywords: [],
    }),
    getters: {
        backgroundStyle(state) {
            let background = state.theme.backgroundColor;
            if (state.theme.backgroundType == "gradient") {
                background = `linear-gradient(${state.theme.backgroundGradientDegree}deg,`;
                background += `${state.theme.backgroundGradientFirst},`;
                background += `${state.theme.backgroundGradientSecond})`;
            }
            return { background };
        },
        buttonStyle: (state) => ({
            "border-radius": `${state.theme.borderRadius * 0.25}px`,
            background: state.theme.primaryColor,
            color: state.theme.textColor,
        }),
        inputStyle: (state) => ({
            "border-color": state.theme.primaryColor,
            "border-radius": `${state.theme.borderRadius * 0.25}px`,
            background: state.theme.inputColor,
            color: state.theme.textColor,
        }),
    },
    actions: {
        async getTheme() {
            const { fxUrl, fxHeader } = $cookies.get("fxToken");
            this.loadingStore.setLoading(true);

            try {
                const res = await axios.get(`${fxUrl}/theme`, fxHeader);
                const { data } = res.data;

                if(typeof data == "object") {
                    this.theme = data;
                    this.theme.useBanner = this.theme.backgroundImageUrl != null;
                    if (this.theme.keywords != "")
                        this.keywords = this.theme.keywords.split(";");
                }
            } catch(e) {
                console.log(e);
            }

            this.loadingStore.setLoading();
        },
        async saveTheme() {
            const { fxUrl, fxHeader } = $cookies.get("fxToken");
            this.loadingStore.setLoading(true);

            try {
                const requestBody = { ...this.theme, title: this.theme.platformName };
                delete requestBody.__v;
                delete requestBody.updatedAt;
                delete requestBody.createdAt;
                delete requestBody._id;
                delete requestBody.useBanner;

                await axios.post(`${fxUrl}/theme`, requestBody, fxHeader);
                successToast(
                    "Tema atualizado!",
                    "As opções do tema foram atualizadas com sucesso. As alterações podem levar até 5 minutos para refletir na Área de Aprendizado."
                );
            } catch(e) {
                errorToast(
                    "Atenção",
                    e.message ?? "Ocorreu um erro inesperado, por favor entre em contato com o suporte"
                );
            }
            this.loadingStore.setLoading();
        },
        async receiveImage(obj) {
            const { name } = obj;
            this.loadingStore.setLoading(true);
            try {
                const formData = new FormData();
                formData.append("image", obj.file.files[0]);
                this.isLoading = true;
                const res = await axios.post(uploadImageURL, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                });
                const url = res.data.response.file;

                if (name == "file-background")
                    this.theme.backgroundImageUrl = url;
                if (name == "file-logo") this.theme.logoUrl = url;
                if (name == "file-favicon") this.theme.faviconUrl = url;
            } catch (e) {
                errorToast("Algum erro aconteceu!", e.message);
            }
            this.loadingStore.setLoading(false);
        },
        validate() {
            if (this.theme.useBanner && !this.theme.backgroundImageUrl)
                throw new Error('Selecione a imagem para o background de login ou desmarque a opção')
            if (!this.theme.platformName)
                throw new Error('O título da plataforma é obrigatório');
            if (!this.theme.footer)
                throw new Error('O texto do rodapé da plataforma é obrigatório');
            if (!this.theme.description)
                throw new Error('A descrição da plataforma é obrigatória');
            if(!this.theme.supportEmail)
                throw new Error('Digite o email do suporte');

            if(!emailRegex(this.theme.supportEmail))
                throw new Error('Digite um formato de e-mail válido');
        },
        async updateKeywords() {
            this.theme.keywords = this.keywords.join(';');
        }
    },
});
