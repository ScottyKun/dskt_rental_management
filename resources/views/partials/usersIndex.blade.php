<div id="users-table">
    <div class="overflow-x-auto mt-4 bg-white rounded-xl shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 cursor-pointer">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(0)">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(1)">Prénom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(2)">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(3)">Rôle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(4)">Téléphone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(5)">Adresse</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(6)">Gestionnaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                <tr>
                    <td class="px-6 py-4">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->surname }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4">{{ ucfirst($user->role) }}</td>
                    <td class="px-6 py-4">{{ $user->phone }}</td>
                    <td class="px-6 py-4">{{ $user->address }}</td>
                    <td class="px-6 py-4">{{ $user->manager?->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('users.edit', $user) }}" class="text-blue-500 hover:text-blue-700"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 ml-2"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
