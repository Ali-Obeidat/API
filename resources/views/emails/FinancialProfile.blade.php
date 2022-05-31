@component('mail::message')
# Welcome To hulul

FinancialProfile

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
