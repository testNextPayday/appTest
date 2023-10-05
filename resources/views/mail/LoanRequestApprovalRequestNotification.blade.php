@component('mail::message')

![{{ config('app.name') }}][logo]

[logo]: {{asset('logo_pack/logo/colored/64.png')}} "{{ config('app.name') }}"

#Hello!,

An employee of yours, {{$loanRequest->user->name}}, has applied for a loan.
<br>
Payroll ID: 
@if ($loanRequest->employment)
<b>{{$loanRequest->employment->payroll_id}}</b>
@else
<b>{{$loanRequest->user->employments()->latest()->first()->payroll_id}}</b>
@endif
<br/>
<br>
Monthly Repayment: <b>{{$loanRequest->emi()}}</b><br/>

<br><br>
Please confirm if we can go ahead and process this request.

@component('mail::button', ['url' => $urlApproveRequest, 'color' => 'green'])
Approve Request
@endcomponent

@component('mail::button', ['url' => $urlDeclineRequest, 'color' => 'red'])
Decline Request
@endcomponent

<br><br>
Thank you for choosing {{ config('app.name') }}.
<br>
@component('mail::subcopy')
If you’re having trouble clicking the "Approve Request" button, copy and paste the URL below
into your web browser: {{ $urlApproveRequest }}
@endcomponent

@endcomponent