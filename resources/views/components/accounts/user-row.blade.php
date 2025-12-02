@props(['user', 'index'])

<tr data-id="{{ $user->id }}" data-user='@json($user)'>
    <td class="px-6 py-4 text-center text-sm font-medium text-gray-900">
        {{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}
    </td>
    <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $user->name }}</td>
    <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $user->username }}</td>
    <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $user->role }}</td>
    <td class="px-6 py-4 text-center text-sm text-gray-500">{{ ucfirst($user->status) }}</td>
    <td class="px-6 py-4 text-center text-sm text-gray-500">
        @if ($user->status === 'active')
            <form class="inline" action="{{ route('users.archive', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button class="text-red-600 hover:text-red-900" type="submit">Archive</button>
            </form>
        @else
            <form class="inline" action="{{ route('users.unarchive', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button class="text-green-600 hover:text-green-900" type="submit">Unarchive</button>
            </form>
        @endif
    </td>
    <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $user->remarks }}</td>
    <td class="px-6 py-4 text-center text-sm text-gray-500">
        <button class="edit-btn text-blue-600 hover:text-blue-900" data-id="{{ $user->id }}">Edit</button>
    </td>
</tr>
