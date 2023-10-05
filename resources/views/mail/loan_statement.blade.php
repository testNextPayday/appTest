@component('mail::message')

![{{ config('app.name') }}][logo]

[logo]: {{asset('logo_pack/logo/colored/64.png')}} "{{ config('app.name') }}"

#Hello!,

This is a loan statement of the loan <b>{{$data}}</b>

Find Attached is your loan statement

<br><br>
Thank you for choosing {{ config('app.name') }}.
<br>

@endcomponent