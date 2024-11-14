import {defineConfig, loadEnv} from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import path, {resolve} from 'path';
import {homedir} from 'os';

const laravelInputs = [];
const themeAppJsFiles = [];
const excludedThemeDirs = ['vendor'];

// adding theme files
const themes = fs.readdirSync('resources/views', {withFileTypes: true})
	.filter(dirent => dirent.isDirectory() && !excludedThemeDirs.includes(dirent.name))
	.map(dirent => dirent.name);

themes.forEach(theme => {
	const themeDashboardScssPath = `resources/views/${theme}/scss/dashboard.scss`;
	const themeLPScssPath = `resources/views/${theme}/scss/landing-page.scss`;
	const themeAppJsPath = `resources/views/${theme}/js/app.js`;
	const chatbotAppJsPath = `resources/views/${theme}/js/chatbotApp.js`;

	fs.existsSync(themeDashboardScssPath) && laravelInputs.push(themeDashboardScssPath);
	fs.existsSync(themeLPScssPath) && laravelInputs.push(themeLPScssPath);
	if(fs.existsSync(themeAppJsPath)) {
		laravelInputs.push(themeAppJsPath);
		themeAppJsFiles.push(themeAppJsPath);
	}
	fs.existsSync(chatbotAppJsPath) && laravelInputs.push(chatbotAppJsPath);
});

// laravelInputs.push('resources/views/default/js/chatbot/index.js');
laravelInputs.push('resources/views/default/js/chatbotApp.js');

export default ({mode}) => {
	// Load app-level env vars to node-level env vars.
	process.env = {...process.env, ...loadEnv(mode, process.cwd())};

	return defineConfig({
		server: detectServerConfig(process.env.VITE_APP_DOMAIN || 'magicai.test'),
		plugins: [
			laravel({
				input: laravelInputs,
				refresh: ['app/**/*.php', 'resources/views/**/*.php'],
			}),
		],
		build: {
			rollupOptions: {
				output: {
					entryFileNames: 'assets/[name]-[hash].js',
					chunkFileNames: 'assets/[name]-[hash].js',
					assetFileNames: 'assets/[name]-[hash].[ext]',
					// manualChunks: {
					// All files will be bundled into a single file
					//     'app': themeAppJsFiles
					// }
				}
			}
		},
		resolve: {
			alias: {
				'@': '/resources/js',
				'@public': '/public',
				'@themeAssets': '/public/themes',
				'~nodeModules': path.resolve(__dirname, 'node_modules'),
				'~vendor': path.resolve(__dirname, 'vendor'),
			}
		}
	});
};

function detectServerConfig(domain) {
	let keyPath = resolve(homedir(), `.config/valet/Certificates/${domain}.key`);
	let certPath = resolve(homedir(), `.config/valet/Certificates/${domain}.crt`);

	if (!fs.existsSync(keyPath)) {
		return {};
	}

	if (!fs.existsSync(certPath)) {
		return {};
	}

	return {
		hmr: {
			host: domain,
		},
		host: domain,
		https: {
			key: fs.readFileSync(keyPath),
			cert: fs.readFileSync(certPath),
		},
	};
}
