@extends('panel.layout.app')

@section('title',  __('Team'))

@push('css')
    <style>
        .invite-a-friend ul {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: -3.6rem;
        }
        .invite-a-friend ul li {
            list-style: none;
        }
        .invite-a-friend ul li:nth-child(2) {
            margin-bottom: 120px;
        }
        .invite-a-friend ul li:nth-child(3) {
            margin-bottom: 175px;
        }
        .invite-a-friend ul li:nth-child(4) {
            margin-bottom: 120px;
        }
        .invite-a-friend ul li a img {
            width: 53px;
            height: 53px;
            border-radius: 50%;
            -o-object-fit: cover;
            object-fit: cover;
        }/*# sourceMappingURL=style.css.map */
    </style>
@endpush

@section('content')
    <div class="page-header">
        <div class="container-xl">
            @include('panel.user.team.partials.header')
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
            @include('panel.user.team.partials.overview')
            @include('panel.user.team.partials.invite-a-friend')
            @include('panel.user.team.partials.team-members')
        </div>
    </div>
@endsection

@section('script')

@endsection
