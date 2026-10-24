# Domain renewal / transfer separation

- Run the normal `wp payam acf-safe-sync` workflow with its database backup before applying this schema.
- `field_6aa5337fa0da1` / `whmcs_renew_price` remains unchanged in identity and holds the renewal price only.
- The old `field_6aa5336ba0da0` definition was retired. Existing post meta was deliberately retained.
- Read-time compatibility migration: use a numeric renewal value (including zero); only when absent, use the legacy transfer post meta. Conflicting historical values preserve renewal as the displayed price and keep transfer in storage for recovery.
- Catalog sync now uses a new field key, `field_payam_whmcs_transfer_price`, for the actual one-year transfer price. The name `whmcs_transfer_price` reuses preserved legacy meta, but ACF must be safely synchronized before enabling the plugin. Registration, renewal, and transfer now display separately. Existing unsynchronized records retain the old renewal read fallback until their first successful sync.
- Rollback: restore the prior JSON definitions and helper/template together, then use safe sync. Historical transfer data remains available; the backup covers subsequent edits.
