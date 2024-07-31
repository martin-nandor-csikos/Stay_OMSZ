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

@session('reportamount-update-success')
    <div class="alert alert-success" role="alert">
        {{ session('reportamount-update-success') }}
    </div>
@endsession

@session('reportamount-update-failed')
    <div class="alert alert-danger" role="alert">
        {{ session('reportamount-update-failed') }}
    </div>
@endsession

@session('dutytime-update-success')
    <div class="alert alert-success" role="alert">
        {{ session('dutytime-update-success') }}
    </div>
@endsession

@session('dutytime-update-failed')
    <div class="alert alert-danger" role="alert">
        {{ session('dutytime-update-failed') }}
    </div>
@endsession