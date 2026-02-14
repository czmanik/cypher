<div
    x-data="{
        init() {
            if ('Notification' in window && Notification.permission !== 'granted') {
                Notification.requestPermission();
            }
        }
    }"
    @browser-notification.window="
        if (Notification.permission === 'granted') {
            new Notification($event.detail.title, { body: $event.detail.body });
        }
    "
    @if($shouldPoll)
        wire:poll.30s="keepAlive"
    @endif
>
</div>
