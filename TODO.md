# TODO - Fix status dropdown/editability

- [ ] Update `resources/js/record-utils.js`:
  - [ ] Split editability rules for `status` vs `hoa-status`
  - [ ] Ensure `hoa-status` is not governed by ON-SHELF/UNAVAILABLE rule intended for record status
  - [ ] Dynamically filter record `#status` dropdown options when record is not editable (hide BORROWED/ARCHIVED when appropriate)
- [ ] Verify HOA modal:
  - [ ] ON-SHELF/UNAVAILABLE records allow changing `hoa-status`
  - [ ] BORROWED/ARCHIVED records keep `hoa-status` disabled
  - [ ] BORROWED option no longer visible in status dropdown when edits are not allowed
- [ ] (Optional) Verify REM modal status behavior remains correct

