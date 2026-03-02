@props(['user', 'index'])

<tr data-id="{{ $user->id }}" data-user='@json($user)'>
    <td class="px-6 py-4 text-center text-sm font-medium text-gray-900">
        {{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}
    </td>
    <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $user->name }}</td>
    <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $user->username }}</td>
    <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $user->role }}</td>
    <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $user->remarks }}</td>
    <td class="px-6 py-4 text-center text-sm text-gray-500">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white {{ $user->status === 'active' ? 'bg-green-500' : 'bg-red-500' }}">
            {{ ucfirst($user->status) }}
        </span>
    </td>
    <td class="px-6 py-4 text-center text-sm text-gray-500">
        @if(auth()->user()->role === 'Admin')
        <button class="edit-btn text-blue-600 hover:text-blue-900" data-id="{{ $user->id }}">Edit</button>
        @endif
    </td>
</tr>
