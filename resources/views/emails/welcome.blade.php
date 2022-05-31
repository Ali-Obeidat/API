@component('mail::message')
# Welcome To hulul


Thank you and your login is:{{ $data['login'] }}.


@component('mail::button', ['url' => 'https://hululmfx.com/demos'])
check it
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
