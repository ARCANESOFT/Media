@extends(arcanesoft\foundation()->template())

@section('page-title')
    <i class="far fa-fw fa-images"></i> @lang('Media')
@endsection

@section('content')
    <v-media-manager></v-media-manager>
@endsection

@push('scripts')
@endpush
