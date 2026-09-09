@session('successful-deletion')
    <div class="alert alert-success" role="alert">
        {{ session('successful-deletion') }}
    </div>
@endsession

@session('unsuccessful-deletion')
    <div class="alert alert-danger" role="alert">
        {{ session('unsuccessful-deletion') }}
    </div>
@endsession

@session('successful-creation')
    <div class="alert alert-success" role="alert">
        {{ session('successful-creation') }}
    </div>
@endsession

@session('successful-update')
    <div class="alert alert-success" role="alert">
        {{ session('successful-update') }}
    </div>
@endsession

@session('no-changes')
    <div class="alert alert-danger" role="alert">
        {{ session('no-changes') }}
    </div>
@endsession
