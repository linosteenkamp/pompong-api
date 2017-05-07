@component('mail::message')
# Access Request

Hi Lino,

{{$request->name}} at {{$request->email}} has requested access to Pompong.

@component('mail::button', ['url' => $urlYes])
    Accept User
@endcomponent

@component('mail::button', ['url' => $urlNo])
    Decline User
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
