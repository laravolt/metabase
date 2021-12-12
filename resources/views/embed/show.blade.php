@extends(config('laravolt.metabase.view.layout'))
@section('content')
    <x-metabase :dashboard="$id" :params="$params"></x-metabase>
@endsection
