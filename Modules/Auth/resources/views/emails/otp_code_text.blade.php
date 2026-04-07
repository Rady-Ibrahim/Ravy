@php
    $purposeText = $purpose === 'password_reset'
        ? __('password reset')
        : __('email verification');
@endphp

{{ __('Your :purpose code is: :code', ['purpose' => $purposeText, 'code' => $code]) }}
{{ __('This code expires in :minutes minutes.', ['minutes' => 15]) }}
