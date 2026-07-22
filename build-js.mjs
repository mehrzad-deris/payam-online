import { build } from 'esbuild';
import { readdir } from 'node:fs/promises';
import path from 'node:path';

const sourceDirectory = path.resolve('assets/js');

async function findJavaScriptFiles(directory) {
    const entries = await readdir(directory, {
        withFileTypes: true,
    });

    const files = await Promise.all(
        entries.map(async (entry) => {
            const absolutePath = path.join(directory, entry.name);

            if (entry.isDirectory()) {
                return findJavaScriptFiles(absolutePath);
            }

            if (
                entry.isFile() &&
                entry.name.endsWith('.js') &&
                !entry.name.endsWith('.min.js')
            ) {
                return [absolutePath];
            }

            return [];
        })
    );

    return files.flat();
}

const entryPoints = await findJavaScriptFiles(sourceDirectory);

await Promise.all(
    entryPoints.map((entryPoint) =>
        build({
            entryPoints: [entryPoint],
            outfile: entryPoint.replace(/\.js$/, '.min.js'),
            bundle: true,
            minify: true,
            sourcemap: false,
            target: ['es2018'],
            logLevel: 'info',
        })
    )
);