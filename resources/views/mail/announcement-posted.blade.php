<x-mail::message>
# {{ $announcement->title }}

{{ $announcement->description }}

<x-mail::button :url="''">
Go To Announcement
</x-mail::button>

Thanks,<br>
Learning Management System (LMS)
</x-mail::message>
