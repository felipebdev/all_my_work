@php
    use App\Http\Controllers\IntegracaoActionListController as actionList;
    use App\Http\Controllers\IntegracaoActionController as action;

    $actionsList = actionList::index()["actionsList"];
    $activeCampaingLists = action::getActiveCampaingList($webhook->id);
@endphp

<form class="mui-form" action="{{route('integracaoAction.create',['id'=>$webhook->id])}}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">

    </div>
</form>