@php $isAudit = old('is_audit_committee', $committee->is_audit_committee ?? false); @endphp
<div class="mb-3 p-3 rounded-3" style="background: #fff8e6; border: 1px solid #fde3a7;">
    <input type="hidden" name="is_audit_committee" value="0">
    <div class="form-check form-switch mb-1">
        <input class="form-check-input" type="checkbox" role="switch" id="is_audit_committee"
               name="is_audit_committee" value="1" @checked($isAudit)>
        <label class="form-check-label fw-semibold" for="is_audit_committee">
            <i class="mdi mdi-shield-check-outline me-1" style="color: #d97706;"></i>This is an Audit Committee
        </label>
    </div>
    <small class="text-muted d-block" style="padding-left: 2.5em;">
        Meetings of this committee are run by the DD Internal Audit, who also approves their notices, board papers
        and agenda changes in place of the CEO. The Corporation Secretary cannot initiate these meetings.
    </small>
</div>
