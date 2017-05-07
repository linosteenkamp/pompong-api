@component('mail::message')
# Welcome

{{ $request->name }}, Welcome to Pompong

@component('mail::button', ['url' => $url])
Enter my Pleasure Dome
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
