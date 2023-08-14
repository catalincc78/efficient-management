@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Profile') }}</div>

                    <div class="card-body">
                        <form id="form-register" method="POST" action="{{ route('register') }}">
                            @csrf
                            @foreach($arFields as $field)
                                @if ($user->type === 'company' || !isset($field['company_only']))
                                    <div class="row mb-3{{ $user->type === 'personal' && ($field['company_only'] ?? false) ? ' d-none' : '' }}">
                                        <label for="{{$field['field_slug']}}" class="col-md-4 col-form-label text-md-end">{{ __($field['field_label']) }}</label>
                                        <div class="col-md-6">
                                            <input id="{{$field['field_slug']}}" type="{{$field['field_type']}}" class="form-control @error($field['field_slug']) is-invalid @enderror" name="{{$field['field_slug']}}" value="{{ old($field['field_slug'], $user->{$field['field_slug']}) }}" autocomplete="{{$field['field_label']}}" disabled>
                                            @error($field['field_slug'])
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
