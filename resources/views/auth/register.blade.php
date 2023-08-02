@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form id="form-register" method="POST" action="{{ route('register') }}">
                        @csrf
                        @php
                            $bIsCompany = old('type') === 'company';
                        @endphp
                        @foreach($arFields as $field)
                            <div class="row mb-3{{(!$bIsCompany && ($field['company_only'] ?? false)) ? ' d-none' : '' }}">
                                <label for="{{$field['field_slug']}}" class="col-md-4 col-form-label text-md-end">{{ __($field['field_label']) }}</label>

                                <div class="col-md-6">
                                    @if($field['field_type'] === 'radio')
                                        @foreach($field['values'] as $valueRadio => $labelRadio)
                                            <div class="form-check form-check-inline pt-2">
                                                <input class="form-check-input" type="radio" id="{{$field['field_slug'].'_'.$valueRadio}}" name="{{$field['field_slug']}}" value="{{$valueRadio}}" {!! (old($field['field_slug']) === $valueRadio || (!$bIsCompany && $valueRadio === 'personal')) ? ' checked="checked"' : '' !!} >
                                                <label style="cursor: pointer;" class="form-check-label" for="{{$field['field_slug'].'_'.$valueRadio}}">{{$labelRadio}}</label>
                                            </div>
                                        @endforeach
                                    @else
                                        <input id="{{$field['field_slug']}}" type="{{$field['field_type']}}" class="form-control @error($field['field_slug']) is-invalid @enderror" name="{{$field['field_slug']}}" value="{{ old($field['field_slug']) }}" autocomplete="{{$field['field_label']}}" >
                                    @endif
                                    @error($field['field_slug'])
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        @endforeach

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script type="module">
            $('#form-register input[name="type"]').change(function(){
                let fields = $('#form-register input[name="company_name"], #form-register input[name="company_position"], #form-register input[name="cif"]');
                console.log($('#form-register input[name="type"]:checked').val());
                if($('#form-register input[name="type"]:checked').val() === 'company'){
                    fields.closest('.row').removeClass('d-none').show();
                }else{
                    fields.closest('.row').hide();
                }
            });
    </script>
@endsection
