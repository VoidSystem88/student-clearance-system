@forelse($visitors as $visitor)
<tr class="hover:bg-gray-50">
    <td class="px-4 py-3 text-sm font-mono">{{ $visitor->ip_address ?? 'N/A' }}</td>
    <td class="px-4 py-3 text-sm">
        @if($visitor->city)
            <div class="font-medium">{{ $visitor->city }}</div>
        @endif
        <div class="text-xs text-gray-500">{{ $visitor->country ?? 'Unknown' }}</div>
    </td>
    <td class="px-4 py-3 text-sm">
        <div class="flex items-center gap-1">
            @if($visitor->device_type == 'mobile')
                <span>📱</span>
            @elseif($visitor->device_type == 'tablet')
                <span>📟</span>
            @else
                <span>💻</span>
            @endif
            <span>{{ ucfirst($visitor->device_type ?? 'Unknown') }}</span>
        </div>
        <div class="text-xs text-gray-500">{{ $visitor->os ?? '' }} {{ $visitor->browser ?? '' }}</div>
    </td>
    <td class="px-4 py-3 text-sm">{{ $visitor->browser ?? 'Unknown' }} / {{ $visitor->os ?? 'Unknown' }}</td>
    <td class="px-4 py-3">
        @if($visitor->network_type == 'wifi')
            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded"><i class="fas fa-wifi"></i> WiFi</span>
        @elseif($visitor->network_type == 'cellular')
            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded"><i class="fas fa-signal"></i> Cellular</span>
        @else
            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">Unknown</span>
        @endif
    </td>
    <td class="px-4 py-3 text-sm text-gray-500">{{ $visitor->created_at ? $visitor->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center py-8 text-gray-500">No visitors yet</td>
</tr>
@endforelse