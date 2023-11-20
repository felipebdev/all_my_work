import Index from "../../views/learning-area/courses/Index.vue";

const routes = [
    /** Route  default */
    {
        path: '/learning-area/',
        redirect: {
            name: 'course-index',
        }
    },

    /** Sections */
    {
        path: '/learning-area/sections',
        name: 'section-index',
        component: () => import(/* webpackChunkName: "section-edit" */ "../../views/learning-area/sections/Index.vue")
    }, {
        path: '/learning-area/sections/:id/edit',
        name: 'section-edit',
        component: () => import(/* webpackChunkName: "section-edit" */ "../../views/learning-area/sections/Edit.vue")
    },

    /** Course */
    {
        path: '/learning-area/courses',
        name: 'course-index',
        component: Index //! No use lazy loading here because this component is the init
    }, {
        path: '/learning-area/courses/new',
        name: 'course-new',
        component: () => import(/* webpackChunkName: "course-create" */ "../../views/learning-area/courses/Create.vue")
    }, {
        path: '/learning-area/courses/:id/edit',
        name: 'course-edit',
        component: () => import(/* webpackChunkName: "course-edit" */ "../../views/learning-area/courses/Edit.vue")
    },

    /** Content */
    {
        path: '/learning-area/content',
        name: 'content-index',
        component: () => import(/* webpackChunkName: "content-index" */ "../../views/learning-area/contents/Index.vue")
    }, {
        path: '/learning-area/content/new',
        name: 'content-new',
        component: () => import(/* webpackChunkName: "content-create" */ "../../views/learning-area/contents/Create.vue")
    }, {
        path: '/learning-area/content/:content_id/edit',
        name: 'content-edit',
        component: () => import(/* webpackChunkName: "content-edit" */ "../../views/learning-area/contents/Edit.vue")
    },

    /** Authors */
    {
        path: '/learning-area/authors',
        name: 'author-index',
        component: () => import(/* webpackChunkName: "author-edit" */ "../../views/learning-area/authors/Index.vue")
    }, {
        path: '/learning-area/authors/new',
        name: 'author-new',
        component: () => import(/* webpackChunkName: "author-edit" */ "../../views/learning-area/authors/Create.vue")
    }, {
        path: '/learning-area/authors/:id/edit',
        name: 'author-edit',
        component: () => import(/* webpackChunkName: "author-edit" */ "../../views/learning-area/authors/Edit.vue")
    },

    /** Lives */
    {
        path: '/learning-area/lives',
        name: 'lives-index',
        component: () => import(/* webpackChunkName: "live-index" */ "../../views/learning-area/lives/Index.vue")
    }, {
        path: '/learning-area/lives/new',
        name: 'lives-new',
        component: () => import(/* webpackChunkName: "live-create" */ "../../views/learning-area/lives/Create.vue")
    }, {
        path: '/learning-area/lives/:id/edit',
        name: 'lives-edit',
        component: () => import(/* webpackChunkName: "live-edit" */ "../../views/learning-area/lives/Edit.vue")
    },

    /** Design */
    {
        path: '/learning-area/design',
        name: 'design-index',
        component: () => import(/* webpackChunkName: "design-index" */ "../../views/learning-area/design/Index.vue")
    }, {
        path: '/learning-area/design/onboarding',
        name: 'design-onboarding',
        component: () => import(/* webpackChunkName: "live-onboarding" */ "../../views/learning-area/design/onboarding/Index.vue")
    }, {
        path: '/learning-area/design/start-page',
        name: 'design-start-page',
        component: () => import(/* webpackChunkName: "live-start-page" */ "../../views/learning-area/design/start-page/Index.vue")
    }, {
        path: '/learning-area/design/config-menu',
        name: 'design-config-menu',
        component: () => import(/* webpackChunkName: "live-menu" */ "../../views/learning-area/design/menu/Index.vue")
    }, {
        path: '/learning-area/design/visual-identity',
        name: 'design-visual-identity',
        component: () => import(/* webpackChunkName: "live-visual-identity" */ "../../views/learning-area/design/visual-identity/Index.vue")
    },

    /** Comments */
    {
        path: '/learning-area/comments',
        name: 'comments-index',
        component: () => import(/* webpackChunkName: "comment-index" */ "../../views/learning-area/comments/Index.vue")
    },

    /** TODO New Rotes */
    {
        path: '/learning-area/gamification',
        name: 'gamification-index',
        component: Index
    }, {
        path: '/learning-area/reports',
        name: 'reports-index',
        component: Index
    }, {
        path: '/learning-area/progress',
        name: 'progress-index',
        component: Index
    },
    // EXAMPLES
    // {
    //     path: '/route',
    //     name: 'name-route',
    //     component: () => import(/* webpackChunkName: "name-component" */ "../../views/learning-area/../Component.vue")
    // },
    // {
    //     path: '/route/:id',
    //     name: 'view',
    //     component: () => import(/* webpackChunkName: "name-component" */ "../../views/learning-area/../Component.vue")
    // }
];

export default routes;
