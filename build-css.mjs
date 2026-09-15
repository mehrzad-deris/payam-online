import { readdir, writeFile } from 'node:fs/promises';
import { extname, basename, join } from 'node:path';
import * as sass from 'sass';

const root = join(process.cwd(), 'assets', 'styles', 'scss');
const production = process.argv[2] === 'production';

const entries = async (directory) => {
    const found = [];
    for (const entry of await readdir(directory, { withFileTypes: true })) {
        const path = join(directory, entry.name);
        if (entry.isDirectory()) found.push(...await entries(path));
        else if (extname(entry.name) === '.scss' && !basename(entry.name).startsWith('_')) found.push(path);
    }
    return found;
};

for (const source of await entries(root)) {
    const css = sass.compile(source, {
        style: production ? 'compressed' : 'expanded',
        sourceMap: false,
        loadPaths: [root],
    }).css;
    const target = source.replace(/\.scss$/, production ? '.min.css' : '.css');
    await writeFile(target, css);

    // Tailwind imports the readable app.css intermediary in both environments.
    if (production && basename(source) === 'app.scss') {
        await writeFile(source.replace(/\.scss$/, '.css'), css);
    }
}

