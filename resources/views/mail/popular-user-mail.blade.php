<x-mail::message>
Hi Admin,

The following user has received over 50 likes:

|  |  |  |
| :--- | :--- | :--- |
| **User Name** | : | {{ $user->name }} |
| **User ID** | : | {{ $user->id }} |
| **Current Likes**  | :| {{ $user->received_swipes_count }} |
| **Age** | : | {{ $user->age }} |
| **Location** | : | {{ $user->location }} |

<br>
Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
