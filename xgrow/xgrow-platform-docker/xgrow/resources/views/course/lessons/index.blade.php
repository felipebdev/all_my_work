@extends('templates.xgrow.main')

@if (env('APP_ENV') != 'production')
    @push('after-styles')
        <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/course_experience.css') }}">
        <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/vue-select2.css') }}">
    @endpush

    @push('after-scripts')
        <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/gojs/release/go.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
        <script src="https://cdn.jsdelivr.net/npm/vuex@3.6.2/dist/vuex.js"></script>
        <script>
            const course = @json($course->id);
            const getModules = @json(route('course.experience.get.modules', $course->id));
            const getContents = @json(route('course.experience.get.contents', $course->id));
            const postContent = @json(route('course.experience.post.content', $course->id));
            const deleteContent = @json(route('course.experience.delete.content', $course->id));
            const postModule = @json(route('course.experience.post.module', $course->id));
            const getAuthors = @json(route('course.experience.get.authors', $course->id));
            const createAuthor = @json(route('course.experience.create.author', $course->id));
            const syncModule = @json(route('course.experience.sync', $course->id));
            const gjs = @json(isset($gjs) ? $gjs : '');
            const uploadUrl = '{{ env('LINODE_URL') }}';
            const pId = '{{ Auth::user()->platform_id }}';
        </script>
        <script src="{{ asset('js/bundle/experience.js') }}"></script>
    @endpush

    @section('content')
        <div class="d-flex my-3 mb-0">
            <nav class="xgrow-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Início</a></li>
                    <li class="breadcrumb-item"><a href="/course">Cursos</a></li>
                    <li class="breadcrumb-item active"><a href="/course">Experience</a></li>
                </ol>
            </nav>
        </div>

        <div id="courseExperience" @mouseleave.prevent="verifyModifiedData()">
            <div class="tab-pane fade show active" id="nav-journey" role="tabpanel" aria-labelledby="nav-journey-tab">
                <div class="xgrow-card card-dark mt-4">
                    <div class="xgrow-vi-loading" v-if="loading == true">
                        <div class="loader"></div>
                    </div>
                    <template v-else>
                        <div class="xgrow-card-header flex-wrap gap-3">
                            <div>
                                <p class="xgrow-card-title py-2" style="font-size: 1.5rem; line-height: inherit;">
                                    Xgrow experience
                                </p>
                                <p class="xgrow-card-body-title mb-1 ">
                                    Veja os detalhes de seus fluxos de conteúdo ou crie um novo.
                                </p>
                            </div>
                            <button class="xgrow-button border-light" type="button" @click="addFlow"
                                v-if="screen.toString() !== 'diagram'">
                                <i class="fa fa-plus"></i> Adicionar novo fluxo
                            </button>
                            <button class="xgrow-button border-light" type="button" @click="saveFlow"
                                v-if="screen.toString() === 'diagram'">
                                <i class="fa fa-save"></i> Salvar fluxo
                            </button>
                        </div>
                        <div class="xgrow-card-body mb-2">
                            @include('course.lessons.flow')
                            @include('course.lessons.no-flow')
                            <!--Tela do Flow-->
                            @include('course.lessons.diagram-flow')
                        </div>
                    </template>
                </div>
            </div>
            @include('course.lessons.elements.modal-video')
            @include('course.lessons.elements.modal-content')
            @include('course.lessons.elements.modal-link')
            @include('course.lessons.elements.modal-archive')
            @include('course.lessons.elements.modal-text')
            @include('course.lessons.elements.modal-authors')
            @include('course.lessons.elements.modal-confirmation')
        </div>
        @include('elements.toast')
    @endsection
@endif
