@component('mail::message')
# Hi  

you referred to {{ $data['f_name'] }} {{ $data['f_name'] }} to sign up in our website



Thanks,<br>
{{ config('app.name') }}
@endcomponent
