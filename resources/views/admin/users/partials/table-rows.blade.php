@foreach ($users as $user)
<tr data-user-id="{{ $user->user_id }}">
    <td>{{ $user->user_id }}</td>
    <td>
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div class="font-medium">{{ $user->name }}</div>
            </div>
        </div>
    </td>
    <td>
        <div class="flex items-center">
            <i class="fas fa-envelope text-gray-400 mr-2"></i>
            {{ $user->email }}
        </div>
    </td>
    <td>
        <div class="flex items-center">
            <i class="fas fa-phone text-gray-400 mr-2"></i>
            {{ $user->phone }}
        </div>
    </td>
    <td>{{ $user->username }}</td>
    <td>
        @php
            $roleClass = '';
            $roleIcon = 'user';
            
            switch($user->userType) {
                case 'admin':
                    $roleClass = 'badge-role-admin';
                    $roleIcon = 'user-shield';
                    break;
                case 'customer':
                    $roleClass = 'badge-role-customer';
                    $roleIcon = 'user';
                    break;
                case 'shop owner':
                    $roleClass = 'badge-role-shopowner';
                    $roleIcon = 'store';
                    break;
                case 'photographer':
                    $roleClass = 'badge-role-photographer';
                    $roleIcon = 'camera';
                    break;
                case 'make-up artist':
                    $roleClass = 'badge-role-makeup';
                    $roleIcon = 'paint-brush';
                    break;
            }
        @endphp
        
        <span class="badge {{ $roleClass }}">
            <i class="fas fa-{{ $roleIcon }} mr-1"></i>
            {{ $user->userType }}
        </span>
    </td>
    <td>
        <span class="badge {{ $user->status == 'active' ? 'badge-status-active' : 'badge-status-inactive' }}">
            <i class="fas fa-{{ $user->status == 'active' ? 'check-circle' : 'times-circle' }} mr-1"></i>
            {{ $user->status }}
        </span>
    </td>
    <td>
        <div class="action-btns">
            <a href="{{ route('admin.users.edit', $user->user_id) }}" class="btn btn-info action-btn">
                <i class="fas fa-edit"></i> Edit
            </a>
            <button type="button" onclick="toggleStatus('{{ $user->user_id }}', '{{ $user->status == 'active' ? 'inactive' : 'active' }}')" class="btn {{ $user->status == 'active' ? 'btn-danger' : 'btn-success' }} action-btn">
                <i class="fas fa-{{ $user->status == 'active' ? 'ban' : 'check' }}"></i>
                {{ $user->status == 'active' ? 'Deactivate' : 'Activate' }}
            </button>
        </div>
    </td>
</tr>
@endforeach
