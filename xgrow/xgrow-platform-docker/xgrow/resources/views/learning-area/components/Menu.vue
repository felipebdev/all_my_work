<template>
    <div class="learningAreaMenu__header">
        <h1><i class="fas fa-graduation-cap"></i> Área de Aprendizagem</h1>
        <h3>{{ platformName }}</h3>
    </div>
    <div class="learningAreaMenu__menu">
        <ul>
            <router-link :to="{ name: 'section-index' }" tag="li" active-class="active" class="learningAreaMenu__menu__link"
                :class="setActive('sections')">
                <img src="/xgrow-vendor/assets/img/icons/fa-list.svg" alt="Seções">
                Seções
            </router-link>
            <router-link :to="{ name: 'course-index' }" tag="li" active-class="active" class="learningAreaMenu__menu__link"
                :class="setActive('courses')">
                <img src="/xgrow-vendor/assets/img/icons/menu-book.svg" alt="Cursos">
                Cursos
            </router-link>
            <router-link :to="{ name: 'content-index' }" tag="li" active-class="active" class="learningAreaMenu__menu__link"
                :class="setActive('content')">
                <img src="/xgrow-vendor/assets/img/icons/fa-list.svg" alt="Conteúdo">
                Conteúdos
            </router-link>
            <router-link :to="{ name: 'comments-index' }" tag="li" active-class="active"
                class="learningAreaMenu__menu__link" :class="setActive('comments')">
                <img src="/xgrow-vendor/assets/img/icons/fa-comments.svg" alt="Comentários">
                Comentários
            </router-link>
            <router-link :to="{ name: 'author-index' }" tag="li" active-class="active" class="learningAreaMenu__menu__link"
                :class="setActive('authors')">
                <img src="/xgrow-vendor/assets/img/icons/user.svg" alt="Autores">
                Autores
            </router-link>
            <router-link :to="{ name: 'lives-index' }" tag="li" active-class="active" class="learningAreaMenu__menu__link"
                :class="setActive('lives')">
                <img src="/xgrow-vendor/assets/img/icons/cam.svg" alt="Lives">
                Lives
            </router-link>
            <router-link :to="{ name: 'design-index' }" tag="li" active-class="active" class="learningAreaMenu__menu__link"
                :class="setActive('design')">
                <img src=" /xgrow-vendor/assets/img/icons/paint.svg" alt="Design">
                Design
            </router-link>
            <router-link :to="{ name: 'gamification-index' }" tag="li" active-class="active"
                class="learningAreaMenu__menu__link d-none" :class="setActive('gamification')">
                <img src="/xgrow-vendor/assets/img/icons/game.svg" alt="Gamificação">
                Gameficação
            </router-link>
            <router-link :to="{ name: 'reports-index' }" tag="li" active-class="active"
                class="learningAreaMenu__menu__link d-none" :class="setActive('reports')">
                <img src="/xgrow-vendor/assets/img/icons/metric.svg" alt="Relatórios">
                Relatórios
            </router-link>
            <router-link :to="{ name: 'progress-index' }" tag="li" active-class="active"
                class="learningAreaMenu__menu__link d-none" :class="setActive('progress')">
                <img src="/xgrow-vendor/assets/img/icons/graph.svg" alt="Progresso">
                Progresso
            </router-link>
        </ul>
    </div>
</template>

<script>
export default {
    name: "Menu",
    props: { platformName: { type: String, default: '' } },
    methods: {
        /** Get the 2nd path param to match with route */
        setActive: function (val) {
            return this.$route.fullPath.split("/")[2] === val ? 'active' : '';
        }
    }
}
</script>

<style lang="scss" scoped>
.learningAreaMenu__header {
    width: 100%;
    text-align: center;
    padding: 2rem 0;

    h1 {
        font-family: 'Open Sans', serif;
        font-style: normal;
        font-weight: 700;
        color: #FFFFFF;
        font-size: 0.875rem;
        line-height: 1.25rem;
    }

    h3 {
        font-family: 'Open Sans', serif;
        font-style: normal;
        font-weight: 600;
        color: #C1C5CF;
        font-size: 0.75rem;
        line-height: 1.25rem;
        text-transform: uppercase;
    }
}

.learningAreaMenu__menu ul {
    padding-left: 0;
}

.learningAreaMenu__menu__link {
    color: white;
    text-decoration: none;
    cursor: pointer;
    font-family: 'Open Sans', serif;
    font-style: normal;
    font-weight: 400;
    font-size: 1rem;
    line-height: 1.5rem;
    padding: 10px 0 10px 1rem;
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.learningAreaMenu__menu__link:hover,
.learningAreaMenu__menu__link.active {
    color: #93BC1E;
    background: #222329;
    text-decoration: none;
}

.learningAreaMenu__menu__link:hover img,
.learningAreaMenu__menu__link.active img {
    filter: invert(59%) sepia(77%) saturate(455%) hue-rotate(35deg) brightness(100%) contrast(87%);
}
</style>
