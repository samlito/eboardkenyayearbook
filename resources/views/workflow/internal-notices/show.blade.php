@extends('admin_dashboard')
@section('admin')

<div class="content">
    <div class="container-fluid">

        {{-- Print Button Bar --}}
        <div class="row mb-3 d-print-none">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>
                    <button type="button" class="btn btn-sm btn-primary" onclick="window.print();">
                        <i class="mdi mdi-printer-outline"></i> Print Notice
                    </button>
                </div>
            </div>
        </div>

        {{-- Official Notice Document --}}
        <div class="card" id="notice-document">
            <div class="card-body p-4 p-md-5" style="max-width: 800px; margin: 0 auto; font-family: 'Times New Roman', Times, serif !important;">

                {{-- Letterhead --}}
                @include('workflow.components.letterhead')

                {{-- Reference and Date --}}
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <p class="mb-1" style="font-size: 13px;">
                            <strong>Our Ref:</strong> {{ $notice->reference_number ?? '-' }}
                        </p>
                    </div>
                    <div class="text-end">
                        <p class="mb-1" style="font-size: 13px;">
                            <strong>Date:</strong> {{ $notice->reference_date ? \Carbon\Carbon::parse($notice->reference_date)->format('d F Y') : now()->format('d F Y') }}
                        </p>
                    </div>
                </div>

                {{-- Addressee --}}
                <div class="mb-4" style="font-size: 13px; line-height: 1.8;">
                    <p class="mb-0">All Members,</p>
                    <p class="mb-0">{{ $notice->meeting->boardCommittee->name ?? 'Board Committee' }}</p>
                    <p class="mb-0">{{ setting('institution_name', 'eBoard System') }}</p>
                </div>

                {{-- Subject --}}
                <div class="text-center mb-4">
                    <p class="mb-0" style="font-size: 14px; font-weight: bold; text-decoration: underline;">
                        RE: {{ strtoupper($notice->subject ?? '-') }}
                    </p>
                </div>

                {{-- Body Content --}}
                <div class="mb-4" style="font-size: 13px; line-height: 1.8; text-align: justify;">
                    {!! $notice->body !!}
                </div>

                {{-- Agenda Items --}}
                @php
                    $agendaItems = is_array($notice->agenda_items) ? $notice->agenda_items : json_decode($notice->agenda_items, true);
                @endphp
                @if($agendaItems && count($agendaItems) > 0)
                    <div class="mb-4">
                        <p class="mb-2" style="font-size: 13px; font-weight: bold;">AGENDA:</p>
                        <ol style="font-size: 13px; line-height: 1.8; padding-left: 20px;">
                            @foreach($agendaItems as $item)
                                <li class="mb-1">{{ $item }}</li>
                            @endforeach
                        </ol>
                    </div>
                @endif

                {{-- Meeting Details --}}
                <div class="mb-4 p-3 rounded" style="background: #f8f9fa; font-size: 13px; line-height: 1.8;">
                    <p class="mb-1"><strong>Date:</strong> {{ $notice->meeting_date ? \Carbon\Carbon::parse($notice->meeting_date)->format('l, d F Y') : '-' }}</p>
                    <p class="mb-1"><strong>Time:</strong> {{ $notice->meeting_time ? \Carbon\Carbon::parse($notice->meeting_time)->format('h:i A') : '-' }}</p>
                    <p class="mb-0"><strong>Venue:</strong> {{ $notice->meeting_venue ?? $notice->meeting->venue ?? '-' }}</p>
                </div>

                {{-- Closing --}}
                <div class="mb-4" style="font-size: 13px; line-height: 1.8;">
                    <p>Your attendance is highly appreciated.</p>
                </div>

                {{-- Approver Signature Block --}}
                @php
                    $auditCommitteeIds = \App\Models\BoardCommittee::where('is_audit_committee', true)->pluck('id')->toArray();
                    $isAuditCommittee = $notice->meeting && in_array($notice->meeting->board_committee_id, $auditCommitteeIds);
                    $approverRoleFull = $isAuditCommittee ? 'Deputy Director Internal Audit' : setting('signatory_title_ceo', 'Chief Executive Officer');
                @endphp
                <div class="mt-5 mb-4">
                    @if(in_array($notice->status, ['approved', 'special_approved']) && $notice->ceo_signature)
                        <div class="mb-2">
                            <img src="{{ $notice->ceo_signature }}" alt="Approver Signature"
                                 style="max-height: 60px; max-width: 200px;">
                        </div>
                    @else
                        <div style="height: 60px; border-bottom: 1px solid #ccc; width: 200px;"></div>
                    @endif
                    <p class="mb-0 mt-2" style="font-size: 13px; font-weight: bold;">
                        {{ $notice->approver->name ?? $notice->meeting->initiatedBy->name ?? $approverRoleFull }}
                    </p>
                    <p class="mb-0" style="font-size: 12px; color: #666;">
                        {{ $notice->status === 'special_approved' ? 'Special Approval' : $approverRoleFull }}
                    </p>
                    <p class="mb-0" style="font-size: 12px; color: #666;">
                        {{ setting('institution_name', 'eBoard System') }}
                    </p>
                </div>

                {{-- Footer with KEBS Badge --}}
                <div class="separator"></div>
                <div class="d-flex align-items-center justify-content-between" style="font-size: 11px; color: #999;">
                    <div>
                        <p class="mb-0">ISO 9001:2015 Certified</p>
                    </div>
                    <div class="text-center">
                        <img src="{{ asset('backend/assets/images/kebs-badge.png') }}" alt="KEBS Badge"
                             style="max-height: 40px; opacity: 0.7;"
                             onerror="this.style.display='none'">
                    </div>
                    <div class="text-end">
                        <p class="mb-0">Ref: {{ $notice->reference_number ?? '-' }}</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
    @media print {
        .left-side-menu, .navbar-custom, .footer, .d-print-none { display: none !important; }
        .content-page { margin-left: 0 !important; padding-top: 0 !important; }
        .content { padding: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .card-body { padding: 0 !important; }
        body { background: #fff !important; }
        #notice-document { break-inside: avoid; }
    }
</style>
@endpush
