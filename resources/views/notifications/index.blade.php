<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="container mx-auto p-6">
        @if(Auth::user()->userType == 'admin')
        <h2 class="text-center text-2xl font-bold mb-6">🔔 Notifications</h2>
        @else
        <h2 class="text-center text-2xl font-bold mb-6">🚩 Issue History</h2>
        @endif
        
        <div class="text-center mb-4 flex">
            <!-- ปุ่มแจ้งปัญหา -->
            @if (Auth::user()->userType != 'admin')
            <a href="{{ route('report.create') }}" 
               class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition m-2">
                Report +
            </a>
            @endif
            <!-- ปุ่มกลับ -->
            <a href="{{ route(str_replace(' ', '', Auth::user()->userType) . '.dashboard') }}" 
               class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition m-2">
                Back
            </a>
        </div>
        
        @if ($notifications->isEmpty())
        <div class="text-center bg-yellow-100 text-yellow-800 p-4 rounded-lg">
            ❌ No notifications occurred.
        </div>
        @else
        @foreach ($notifications as $notification)
            @if (Auth::user()->userType == 'admin')
                @if($notification->issue_id)
                    <a href="{{ route('admin.issue.replyPage', ['id' => $notification->issue_id]) }}" class="block">
                        <div class="bg-white p-6 rounded-lg shadow-md mb-4">
                            <h4 class="text-lg font-semibold"> {{ e($notification->title) }} </h4>
                            <p><strong>Report At:</strong> {{ e($notification->created_at) }}</p>
                            <p><strong>Description:</strong> {{ Str::limit(e($notification->description),100) }}</p>
                        </div>
                    </a>
                @else
                    <p>ข้อมูลไม่ถูกต้อง</p>
                @endif

            @else
            <a href="{{ route('issue.reported', ['id' => $notification->id]) }}" class="block">
                <div class="bg-white p-6 rounded-lg shadow-md mb-4">
                    <h4 class="text-lg font-semibold"><strong>Title:</strong> {{ e($notification->title) }} </h4>
                    <p><strong>Report ID:</strong> {{ e($notification->id) }}</p>
                    <p><strong>Description:</strong> {{ Str::limit(e($notification->description),100) }}</p>
                </div>
            </a>
            @endif
        @endforeach
        @endif
    </div>
</body>
</html>
