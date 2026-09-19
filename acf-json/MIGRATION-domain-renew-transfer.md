# Domain renewal / transfer consolidation

- Run the normal `wp payam acf-safe-sync` workflow with its database backup before applying this schema.
- `field_6aa5337fa0da1` / `whmcs_renew_price` remains unchanged in identity and is now the single editable renewal / transfer price.
- The separate `field_6aa5336ba0da0` / `whmcs_transfer_price` definition is retired. Existing post meta is deliberately retained, not deleted or overwritten.
- Read-time compatibility migration: use a numeric renewal value (including zero); only when absent, use the legacy transfer post meta. Conflicting historical values preserve renewal as the displayed price and keep transfer in storage for recovery.
- New edits and future synchronization must write the shared price to `whmcs_renew_price`; templates must not display a separate transfer value.
- Rollback: restore the prior JSON definitions and helper/template together, then use safe sync. Historical transfer data remains available; the backup covers subsequent edits.
