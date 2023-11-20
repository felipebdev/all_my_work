<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">

                <li class="nav-small-cap">PERSONAL</li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <!-- <i class="mdi mdi-gauge"></i> -->
                        <span class="hide-menu">Dashboard </span>
                    </a>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <!-- <i class="mdi mdi-gauge"></i> -->
                        <span class="hide-menu"><a href="{{ url('/client') }}">Clientes</a></span>
                    </a>
                </li>
                <li>
                    <a class="has-arrow" href="{{ url('/platforms') }}" aria-expanded="false">
                        <!-- <i class="mdi mdi-gauge"></i> -->
                        <span class="hide-menu" onclick="window.location.href='{{ url('/platforms') }}'">
                            Plataformas
                        </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ url('/platforms/users') }}">Usuários</a></li>
                        <li><a href="{{ url('/platforms/indicators') }}">Indicadores</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="{{ url('/platforms') }}" aria-expanded="false">
                        <!-- <i class="mdi mdi-gauge"></i> -->
                        <span class="hide-menu" onclick="window.location.href='{{ url('/platforms') }}'">
                            Templates
                        </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ url('/template') }}">Seção</a></li>
                        <li><a href="{{ url('/templatePlatform') }}">Plataforma</a></li>
                        <li><a href="{{ url('/templateContent') }}">Conteúdo</a></li>
                        <li><a href="{{ url('/templateCourse') }}">Curso</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <!-- <i class="mdi mdi-gauge"></i> -->
                        <span class="hide-menu">Relatórios </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="#">Log</a></li>
                        <li><a href="/client-transactions">Alunos e transações</a></li>
                        <li><a href="/audit">Auditoria</a></li>
                        <li><a href="/client-dsr">Requisição DSR - LGPD</a></li>
                        <li><a href="/chargeback">Estorno</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <!-- <i class="mdi mdi-gauge"></i> -->
                        <span class="hide-menu"><a href="{{ route('gallery.index') }}">Galeria</a></span>
                    </a>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <!-- <i class="mdi mdi-gauge"></i> -->
                        <span class="hide-menu">Configurações </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ url('/admin') }}">Administradores</a></li>
                        <li><a href="{{ url('/emails') }}">E-mails</a></li>
                        <li><a href="{{ route('email-provider.index') }}">Provedores de email</a></li>
                        <li><a href="{{ url('/configs') }}">Configurações</a></li>
                        <li><a href="{{ route('services.index') }}">Assinaturas</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <!-- <i class="mdi mdi-gauge"></i> -->
                        <span class="hide-menu"><a href="{{ route('products.index') }}">Produtos</a></span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
<!-- ============================================================== -->
<!-- End Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
