<x-mail::message>
# Your Submission On Assignment {{ $submission->assignment->subject }} Has Been Graded!
<br>
<hr>
Your grade is: 100/{{ $submission->grade }}

<x-mail::button :url="''">
Go To Assignments
</x-mail::button>

Thanks,<br>
Learning Management System (LMS)
</x-mail::message>
