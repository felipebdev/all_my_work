<template>
    <div class="learningAreaMenu" :class="{ 'open': isOpen }" @click.self="isOpen = false">
        <ul class="pt-4">
            <li class="learningAreaMenu__link" @click="isOpen = !isOpen">
                <img src="/xgrow-vendor/assets/img/icons/bars-solid.svg">
                <span class="text">Menu</span>
            </li>
            <li class="learningAreaMenu__header">
                <img src="/xgrow-vendor/assets/img/icons/graduation-cap-solid.svg">
                <h1>Área de Aprendizagem</h1>
                <h3>{{ platformName }}</h3>
            </li>
            <router-link :class="setActive(item.activeClass)" :key="i" :to="{ name: item.path }"
                class="learningAreaMenu__link" tag="li" active-class="active" v-for="(item, i) in menuItems"
                @click="isOpen = false">
                <img :src="item.icon" :alt="item.text">
                <span class="text">{{ item.text }}</span>
            </router-link>
        </ul>
    </div>
</template>

<script>
export default {
    name: "Mobile-Menu",
    props: { platformName: { type: String, default: '' } },
    data() {
        return {
            isOpen: false,
            menuItems: [
                { path: 'section-index', icon: '/xgrow-vendor/assets/img/icons/fa-list.svg', text: 'Seções', activeClass: 'sections' },
                { path: 'course-index', icon: '/xgrow-vendor/assets/img/icons/menu-book.svg', text: 'Cursos', activeClass: 'courses' },
                { path: 'content-index', icon: '/xgrow-vendor/assets/img/icons/fa-list.svg', text: 'Conteúdos', activeClass: 'content' },
                // { path: 'comments-index', icon: '/xgrow-vendor/assets/img/icons/fa-comments.svg', text: 'Comentários', activeClass: 'comments' },
                { path: 'author-index', icon: '/xgrow-vendor/assets/img/icons/user.svg', text: 'Autores', activeClass: 'authors' },
                { path: 'lives-index', icon: '/xgrow-vendor/assets/img/icons/cam.svg', text: 'Lives', activeClass: 'lives' },
                { path: 'design-index', icon: '/xgrow-vendor/assets/img/icons/paint.svg', text: 'Design', activeClass: 'design' },
                // { path: 'gamification-index', icon: '/xgrow-vendor/assets/img/icons/game.svg', text: 'Gameficação', activeClass: 'gamification' },
                // { path: 'reports-index', icon: '/xgrow-vendor/assets/img/icons/metric.svg', text: 'Relatórios', activeClass: 'reports' },
                // { path: 'progress-index', icon: '/xgrow-vendor/assets/img/icons/graph.svg', text: 'Progresso', activeClass: 'progress' },
            ]
        }
    },
    methods: {
        /** Get the 2nd path param to match with route */
        setActive: function (val) {
            return this.$route.fullPath.split("/")[2] === val ? 'active' : '';
        }
    }
}
</script>

<style lang="scss" scoped>
.learningAreaMenu {
    overflow: hidden;
    position: absolute;
    top: 0;
    z-index: 3;
    height: 100%;

    ul {
        padding-left: 0;
        min-width: 48px;
        height: 100%;
        width: 48px;
        transition: all .5s;
        background: #252932;
        box-shadow: 10px 0 20px rgba(0, 0, 0, 0.3);
    }

    &__header {
        width: 0;
        height: 0;
        overflow: hidden;
        text-align: center;

        h1 {
            font-family: 'Open Sans', serif;
            font-style: normal;
            font-weight: 700;
            color: #FFFFFF;
            font-size: 0.875rem;
            line-height: 1.25rem;
            display: none;
        }

        h3 {
            display: none;
            font-family: 'Open Sans', serif;
            font-style: normal;
            font-weight: 600;
            color: #C1C5CF;
            font-size: 0.75rem;
            line-height: 1.25rem;
            text-transform: uppercase;
        }
    }

    &__link {
        color: white;
        text-decoration: none;
        cursor: pointer;
        font-family: 'Open Sans', serif;
        font-style: normal;
        font-weight: 400;
        font-size: 1rem;
        line-height: 1.5rem;
        padding: 10px;
        display: flex;
        gap: 0.5rem;
        align-items: center;
        justify-content: center;

        .text {
            display: none;
        }

        &:hover,
        &.active {
            color: #93BC1E;
            background: #222329;
            text-decoration: none;

            img {
                filter: invert(59%) sepia(77%) saturate(455%) hue-rotate(35deg) brightness(100%) contrast(87%);
            }
        }
    }

    &.open {
        width: 100%;

        ul {
            width: 280px;
        }

        .learningAreaMenu {
            &__header {
                width: 100%;
                height: initial;
                padding: 2rem 0;

                h1,
                h3 {
                    display: block;
                }
            }

            &__link {
                padding: 10px 1.5rem;
                justify-content: flex-start;

                .text {
                    display: inline-block;
                }
            }
        }
    }
}
</style>
