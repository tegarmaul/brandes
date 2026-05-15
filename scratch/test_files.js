import fs from 'fs';
import path from 'path';

function getFiles(dir, files_ = []) {
    if (!fs.existsSync(dir)) return files_;
    const files = fs.readdirSync(dir);
    for (const i in files) {
        const name = path.join(dir, files[i]);
        if (fs.statSync(name).isDirectory()) {
            getFiles(name, files_);
        } else {
            if (name.endsWith('.css') || name.endsWith('.js')) {
                files_.push(name.replace(/\\/g, '/'));
            }
        }
    }
    return files_;
}

try {
    const cssFiles = getFiles('resources/css');
    const jsFiles = getFiles('resources/js');
    console.log('CSS Files:', cssFiles.length);
    console.log('JS Files:', jsFiles.length);
    console.log('Total:', cssFiles.length + jsFiles.length);
} catch (e) {
    console.error('Error:', e);
}
