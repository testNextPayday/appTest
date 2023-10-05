@component('mail::message')

![{{ config('app.name') }}][logo]

[logo]: {{asset('logo_pack/logo/colored/64.png')}} "{{ config('app.name') }}"

#Hello {{ $user->name }},

You have a payment due in exactly 2 days.

Find the details of this in the attached invoice.

You can also follow the link to see the details of this payment on your dashboard

@component('mail::button', ['url' => $invoiceUrl, 'color' => 'blue'])
    Check Invoice on Dashboard
@endcomponent

<br><br>
Thank you for choosing {{ config('app.name') }}.
<br>

@endcomponent