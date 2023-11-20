<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<aside class="left-sidebar overflow-auto" style="position:fixed;">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- User profile -->
        @include('templates.application.components.sidebar-profile')
        <!-- End User profile text-->
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                @can('dashboard')
                <li>
                    <a href="/dashboard" aria-expanded="false">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span class="hide-menu">Dashboard </span>
                    </a>
                </li>
                @endcan
                @can('subscriber')
                <li>
                    <a class="has-arrow" href="#assinantes" aria-expanded="false">
                        <i class="mdi mdi-account"></i>
                        <span class="hide-menu">Alunos </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="/subscribers">Assinantes</a></li>
                        <li><a href="/leads">Leads</a></li>
                        <li><a href="/plans">Planos</a></li>
                        <li><a href="/subscribers/import">Importar</a></li>
                        <li><a href="{{ route('subscribers.export.create') }}">Exportar</a></li>
                    </ul>
                </li>
                @endcan
                @can('section')
                <li>
                    <a href="{{ url('section') }}" aria-expanded="false">
                        <i class="mdi mdi-view-quilt"></i>
                        <span class="hide-menu">Seções</span>
                    </a>
                </li>
                @endcan
                @can('course')
                <li>
                    <a href="{{ url('course') }}" aria-expanded="false">
                        <i class="mdi mdi-school"></i>
                        <span class="hide-menu">Cursos</span>
                    </a>
                </li>
                @endcan
                @can('content')
                <li>
                    <a href="{{ url('content') }}" aria-expanded="false">
                        <i class="mdi mdi-book-open-variant"></i>
                        <span class="hide-menu">Conteúdo</span>
                    </a>
                </li>
                @endcan
                @can('comment')
                <li>
                    <a href="{{ url('comments') }}" aria-expanded="false">
                        <i class="mdi mdi-comment"></i>
                        <span class="hide-menu">Comentários</span>
                    </a>
                </li>
                @endcan
                @can('forum')
                <li>
                    <a href="{{ url('forum') }}" aria-expanded="false">
                        <i class="mdi mdi-forum"></i>
                        <span class="hide-menu">Fórum </span>
                    </a>
                </li>
                @endcan
                @can('author')
                <li>
                    <a class="has-arrow" href="#autores" aria-expanded="false">
                        <i class="mdi mdi-account"></i>
                        <span class="hide-menu">Autores </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ url('authors') }}">Autores</a></li>
                        <li><a href="{{ url('authors/transfer-content') }}">Transferir conteúdo</a></li>
                    </ul>
                </li>
                @endcan
                @can('report')
                <li>
                    <a class="has-arrow" href="#reports" aria-expanded="false">
                        <i class="mdi mdi-currency-usd"></i>
                        <span class="hide-menu">Financeiro</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ url('reports/sales') }}">Relatório de Vendas</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#reports" aria-expanded="false">
                        <i class="mdi mdi-account"></i>
                        <span class="hide-menu">Relatórios</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ url('reports/access') }}">Acessos</a></li>
                        <li><a href="{{ url('reports/content') }}">Conteúdos</a></li>
                        <li><a href="{{ url('reports/content-search') }}">Pesquisa</a></li>
                        <li><a href="{{ url('reports/course-search') }}">Cursos</a></li>
                    </ul>
                </li>
                @endcan
                @can('config')
                <li>
                    <a class="has-arrow" href="#configuracoes" id="item_menu" aria-expanded="false">
                        <i class="mdi mdi-account"></i>
                        <span class="hide-menu">Configurações</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('platform-profile.edit') }}">Perfil Plataforma</a></li>
                        <li><a href="{{ route('platforms-users.index') }}">Usuários</a></li>
                        <li><a href="{{ route('permission.index') }}">Permissões</a></li>
                        <li><a href="/platform-config">Design</a></li>
                        <li>
                            <a class="has-arrow" href="#integracoes" aria-expanded="false">
                                <span class="hide-menu">Integrações</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="/integracao">Integrações</a></li>

                                {{-- <li><a href="/integracao-logs">Logs das Integrações</a></li>
                                <li><a href="/payments">Pagamentos</a></li>
                                <li><a href="/getnet">Getnet</a></li>
                                <li><a href="/getnet/plans/links">Links de Planos Getnet</a></li> --}}
                            </ul>
                        </li>
                        <li>
                            <a class="has-arrow" href="#integracoes" aria-expanded="false">

                                <span class="hide-menu">E-mails</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="/emails">Mensagens</a></li>
                                <li><a href="/emails/conf">Configurações</a></li>
                            </ul>
                        </li>
                        <li><a href="{{ route('category.index') }}">Agrupamentos</a></li>
                    </ul>
                </li>
                @endcan

                <!-- <li>
                    <a class="has-arrow" href="#configuracores" aria-expanded="false">
                        <i class="mdi mdi-settings"></i>
                        <span class="hide-menu">Configurações </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="#perfil-plataforma">Perfil Plataforma</a></li>
                        <li><a href="/integracao">Integrações</a></li>
                        <li><a href="#rodape">Rodapé</a></li>
                        <li><a href="#cabecalho">Cabeçalho</a></li>
                        <li><a href="/platform-config">Página de Login</a></li>
                    </ul>
                </li> -->

                <!--
                    <li>
                        <a class="has-arrow" href="#templates" aria-expanded="false">
                            <i class="mdi mdi-account"></i>
                            <span class="hide-menu">Testes Templates </span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="{{ url('/templates/three-columns') }}">Three columns</a></li>
                            <li><a href="{{ url('/templates/lateral-description') }}">Lateral description</a></li>
                            <li><a href="{{ url('/templates/horizontal-highlight') }}">Horizontal highlight</a></li>

                        </ul>
                    </li>
                -->
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
    <!-- Bottom points-->
    <!-- <div class="sidebar-footer">
        <a href="" class="link" data-toggle="tooltip" title="Settings"><i class="ti-settings"></i></a>
        <a href="" class="link" data-toggle="tooltip" title="Email"><i class="mdi mdi-gmail"></i></a>
        <a href="" class="link" data-toggle="tooltip" title="Logout"><i class="mdi mdi-power"></i></a>
    </div> -->
    <!-- End Bottom points-->
</aside>
<!-- ============================================================== -->
<!-- End Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
