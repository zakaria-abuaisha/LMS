<x-mail::message>
# {{ $assignment->subject }} In Course ({{ $assignment->course->course_name }})
<br>
{{ $assignment->content }}
<hr>
Due Date: {{ $assignment->due_date }}

<x-mail::button :url="''">
Go To Assignments
</x-mail::button>

Thanks,<br>
Learning Management System (LMS)
</x-mail::message>
