import { createHash } from 'node:crypto';
import { mkdir, readFile, readdir, rename, writeFile } from 'node:fs/promises';
import path from 'node:path';

const root = path.resolve('acf-json');
const source = path.join(root, 'acf-json-totality.json');
const backupDir = path.join(root, 'backups');
const metadataDir = path.join(root, 'metadata');

function fail(message) {
  throw new Error(message);
}

function collectFields(fields, seen, sourceFile) {
  for (const field of fields ?? []) {
    if (!field || typeof field !== 'object') continue;
    if (!field.key) fail(`Missing field key in ${sourceFile}`);
    if (seen.has(field.key)) fail(`Duplicate field key ${field.key} in ${sourceFile}`);
    seen.add(field.key);
    collectFields(field.sub_fields, seen, sourceFile);
    const layouts = Array.isArray(field.layouts) ? field.layouts : Object.values(field.layouts ?? {});
    collectFields(layouts, seen, sourceFile);
  }
}

async function canonicalFiles() {
  return (await readdir(root, { withFileTypes: true }))
    .filter((entry) => entry.isFile() && /^(group_|ui_)[a-zA-Z0-9_-]+\.json$/.test(entry.name))
    .map((entry) => entry.name)
    .sort();
}

async function validate() {
  const files = await canonicalFiles();
  if (!files.length) fail('No canonical ACF JSON files found.');

  const itemKeys = new Set();
  const fieldKeys = new Set();
  const hashes = [];

  for (const file of files) {
    const raw = await readFile(path.join(root, file), 'utf8');
    const item = JSON.parse(raw);
    if (Array.isArray(item)) fail(`${file} must contain one ACF item, not an export array.`);
    if (!item.key || `${item.key}.json` !== file) fail(`Key/filename mismatch in ${file}`);
    if (itemKeys.has(item.key)) fail(`Duplicate ACF item key ${item.key}`);
    itemKeys.add(item.key);
    collectFields(item.fields, fieldKeys, file);
    hashes.push(createHash('sha256').update(raw).digest('hex'));
  }

  const manifest = {
    schema_version: 1,
    minimum_acf_version: '6.8.0',
    files,
    checksum: createHash('sha256').update(hashes.join('\n')).digest('hex'),
  };
  await mkdir(metadataDir, { recursive: true });
  await writeFile(path.join(metadataDir, 'manifest.json'), `${JSON.stringify(manifest, null, 2)}\n`);
  console.log(`Validated ${files.length} ACF items and ${fieldKeys.size} field keys.`);
}

async function prepare() {
  const raw = await readFile(source, 'utf8');
  const items = JSON.parse(raw);
  if (!Array.isArray(items) || !items.length) fail('The totality export must be a non-empty JSON array.');

  const modified = Math.floor(Date.now() / 1000);
  await mkdir(backupDir, { recursive: true });

  for (const item of items) {
    if (!item?.key || !/^(group_|ui_)[a-zA-Z0-9_-]+$/.test(item.key)) fail(`Invalid ACF item key: ${item?.key}`);
    item.modified = Number.isInteger(item.modified) ? item.modified : modified;
    await writeFile(path.join(root, `${item.key}.json`), `${JSON.stringify(item, null, 4)}\n`);
  }

  await rename(source, path.join(backupDir, 'acf-json-totality.snapshot.json'));
  await validate();
}

const command = process.argv[2] ?? 'validate';
if (command === 'prepare') await prepare();
else if (command === 'validate') await validate();
else fail(`Unknown command: ${command}`);
