@extends('layout')

@section('content')
<x-page-header title="Log Aktivitas" subtitle="Jejak audit seluruh proses aplikasi." />
<div class="table-wrap">
<div class="table-scroll table-scroll-skeleton">
<table class="table">
    <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Deskripsi</th><th>IP</th><th>Metadata</th></tr></thead>
    <tbody>
    @forelse($logs as $log)
        @php
            $metadata = $log->metadata;
            if (is_string($metadata)) {
                $metadataDecoded = json_decode($metadata, true);
                $metadataJsonValid = json_last_error() === JSON_ERROR_NONE;
            } else {
                $metadataDecoded = $metadata;
                $metadataJsonValid = true;
            }

            $metadataPretty = null;
            if (!empty($metadataDecoded) || $metadataDecoded === '0' || $metadataDecoded === 0 || $metadataDecoded === false) {
                $metadataPretty = json_encode(
                    $metadataDecoded,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                );
            }
        @endphp

        <tr>
            <td>{{ $log->created_at }}</td>
            <td>{{ $log->user?->name ?? '-' }}</td>
            <td>{{ $log->action }}</td>
            <td class="min-w-0">{{ $log->description }}</td>
            <td class="whitespace-nowrap">{{ $log->ip_address }}</td>

            <td class="max-w-90">
                @if (empty($metadata) && empty($metadataDecoded))
                    <span class="text-slate-400">-</span>
                @elseif (is_string($metadata) && !$metadataJsonValid)
                    <pre class="whitespace-pre-wrap wrap-break-word font-mono text-xs bg-slate-50 border border-slate-200 rounded p-2 max-h-45 overflow-auto">{{ $metadata }}</pre>
                @else
                    <pre class="whitespace-pre-wrap wrap-break-word font-mono text-xs bg-slate-50 border border-slate-200 rounded p-2 max-h-45 overflow-auto">
{{ $metadataPretty }}
                    </pre>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="hidden"></td></tr>
    @endforelse
    </tbody>
</table>
</div>
</div>

@if($logs->isEmpty())
    <div class="mt-4">
        <x-empty-state title="Belum ada log aktivitas" message="Jejak audit seluruh proses aplikasi belum tersedia." />
    </div>
@endif

<div class="mt-4">{{ $logs->links() }}</div>
@endsection
