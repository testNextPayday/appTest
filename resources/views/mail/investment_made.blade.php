@component('mail::message')

![{{ config('app.name') }}][logo]

[logo]: {{asset('logo_pack/logo/colored/64.png')}} "{{ config('app.name') }}"

#Hello!,

Your investment has been confirmed.

Find Attached your investment certificate

<br><br>
Thank you for choosing {{ config('app.name') }}.
<br>

@endcomponent