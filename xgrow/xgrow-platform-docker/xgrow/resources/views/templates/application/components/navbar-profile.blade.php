
<li class="nav-item dropdown">
    <a id="img-profile-link" class="nav-link  text-muted waves-effect waves-dark" href="#"  >
        @if(isset(Auth::user()->thumb->filename))
            <img src="{{ Auth::user()->thumb->filename }}" alt="user" style="height:30px;" class="profile-pic" />
        @else
            <img src="{{ asset('images/profile.png') }}" class="profile-pic" />
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-right"  id="dropdown-user-profile">
        <ul class="dropdown-user">
            
            <li><a href="/user"><i class="ti-user"></i> Meu perfil</a></li>
            <li><a href="#"><i class="ti-wallet"></i> Financeiro</a></li>
            <li><a href="#" data-toggle="modal" data-target="#suportModal"><i class="ti-email"></i> Suporte</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/platform-config"><i class="ti-settings"></i> Configurações Plataforma</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/logout"><i class="fa fa-power-off"></i> Sair</a></li>
        </ul>
    </div>
</li>


<!-- <div class="dropdown profile-pic-container" id="userProfileIcon" style="padding: 10px;">
        <a id="img-profile-link" class="profile-pic-link" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
     
            <img id="img-profile" src="https://fandone.us-east-1.linodeobjects.com/profile.jpg" alt="user" style="height: 40px;
        width: 40px;" class="border-default-2px profile-pic img-fluid" />
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="img-profile-link">
                <a href="/edit-user.html" class="dropdown-item"><i class="fa fa-user"></i> Meu perfil</a>
                <a href="#" class="dropdown-item"><i class="fa fa-cog"></i> Suporte</a>
                <a href="javascript:void[0]" onclick="logOut()" class="dropdown-item"><i class="fa fa-power-off"></i> Sair</a>
        </div>
</div> -->


















<!-- Button trigger modal -->

{{--<div id="myModal" class="modal" style="z-index:9999" role="dialog">--}}
{{--    <div class="modal-dialog modal-lg" role="document">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <h5 class="modal-title">Existem conteúdos na seção <span style="color:#ff0000" id="spanSectionName"></span>, deseja transferir para outra?</h5>--}}
{{--                <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                    <span aria-hidden="true">&times;</span>--}}
{{--                </button>--}}
{{--            </div>--}}
{{--            <div class="modal-body">--}}
{{--                <input type="hidden" value="" id="originSection">--}}
{{--                <input type="hidden" value="" id="originSectionName">--}}
{{--                <div id="content">--}}
{{--                    <form>--}}
{{--                        @csrf--}}
{{--                        <div class="input-group">--}}
{{--                            <select class="custom-select" id="inputGroupContents">--}}
{{--                                <option selected>Transferir para...</option>--}}
{{--                            </select>--}}
{{--                            <div class="input-group-append">--}}
{{--                                <button class="btn  btn-success" type="button" onclick="tranferContent()">Transferir</button>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="modal-footer">--}}
{{--                <button type="button" class="btn btn-danger" onclick="deleteSection()" data-dismiss="modal">Excluir seção</button>--}}
{{--                <button type="button" class="btn btn-dark" data-dismiss="modal">Cancelar</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
@push('after-scripts')
    <script type="text/javascript">
        function authors()
        {

            $('#myModal').modal('show');


            // let origin = $("#authorOrigin option:selected").val();
            // let destination = $("#authorDestination option:selected").val();
            //
            // if (origin === destination) {
            //     toastr["warning"]("Não é possível transferir para o mesmo autor!");
            //     return false
            // }else{
            //     $("#myModal").modal();
            // }
        }

    </script>

@endpush
