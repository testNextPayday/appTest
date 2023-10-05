@component('mail::message')

![{{ config('app.name') }}][logo]

[logo]: {{asset('logo_pack/logo/colored/64.png')}} "{{ config('app.name') }}"

#Hello!,



<br><br>
Thank you for choosing {{ config('app.name') }}.
<br>

@endcomponent