@session('successful-user-deletion')
    <div class="alert alert-success" role="alert">
        {{ session('successful-user-deletion') }}
    </div>
@endsession

@session('user-created')
    <div class="alert alert-success" role="alert">
        {{ session('user-created') }}
    </div>
@endsession

@session('user-not-created')
    <div class="alert alert-danger" role="alert">
        {{ session('user-not-created') }}
    </div>
@endsession

@session('unsuccessful-user-deletion')
    <div class="alert alert-danger" role="alert">
        {{ session('unsuccessful-user-deletion') }}
    </div>
@endsession

@session('user-updated')
    <div class="alert alert-success" role="alert">
        {{ session('user-updated') }}
    </div>
@endsession

@session('user-not-updated')
    <div class="alert alert-danger" role="alert">
        {{ session('user-not-updated') }}
    </div>
@endsession

@session('close-success')
    <div class="alert alert-success" role="alert">
        {{ session('close-success') }}
    </div>
@endsession

@session('close-failed')
    <div class="alert alert-danger" role="alert">
        {{ session('close-failed') }}
    </div>
@endsession

@session('updateinactivity-success')
    <div class="alert alert-success" role="alert">
        {{ session('updateinactivity-success') }}
    </div>
@endsession

@session('updateinactivity-failed')
    <div class="alert alert-danger" role="alert">
        {{ session('updateinactivity-failed') }}
    </div>
@endsession

@session('destroyinactivity-success')
    <div class="alert alert-success" role="alert">
        {{ session('destroyinactivity-success') }}
    </div>
@endsession

@session('destroyinactivity-failed')
    <div class="alert alert-danger" role="alert">
        {{ session('destroyinactivity-failed') }}
    </div>
@endsession

@session('settings-updated')
    <div class="alert alert-success" role="alert">
        {{ session('settings-updated') }}
    </div>
@endsession

@session('settings-not-updated')
    <div class="alert alert-danger" role="alert">
        {{ session('settings-not-updated') }}
    </div>
@endsession

@session('vehicles-updated')
    <div class="alert alert-success" role="alert">
        {{ session('vehicles-updated') }}
    </div>
@endsession

@session('vehicles-not-updated')
    <div class="alert alert-danger" role="alert">
        {{ session('vehicles-not-updated') }}
    </div>
@endsession

@session('promotions-updated')
    <div class="alert alert-success" role="alert">
        {{ session('promotions-updated') }}
    </div>
@endsession

@session('promotion-failed')
    <div class="alert alert-danger" role="alert">
        {{ session('promotion-failed') }}
    </div>
@endsession
