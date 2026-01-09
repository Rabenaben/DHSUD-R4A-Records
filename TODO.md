# TODO: Unify HOA and REM Add Record Modals

## Tasks
- [x] Modify `resources/views/components/add-record-modal.blade.php` to accept a `type` prop ('hoa' or 'rem') and conditionally render fields based on the type.
- [x] Update `resources/views/hoa_records/hoa.blade.php` to use `<x-add-record-modal type="hoa" :provinces="$provinces" />`.
- [x] Update `resources/views/rem_records/rem.blade.php` to use `<x-add-record-modal type="rem" :provinces="$provinces" />`.
- [x] Delete `resources/views/components/add-rem-record-modal.blade.php` as it will be replaced by the unified component.
- [ ] Test the modals to ensure functionality for both HOA and REM types.
