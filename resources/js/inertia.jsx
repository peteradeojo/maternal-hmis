import './bootstrap';

import * as React from 'react';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';

import { Provider } from 'react-redux';

// import { store } from './store';
// import { ToastProvider } from './components/Toast';

createInertiaApp({
	resolve: (name) => {
		const pages = import.meta.glob('./pages/**/*.jsx');
		const page = pages[`./pages/${name}.jsx`]();
		return page;
	},
	setup({ el, App, props }) {
		console.log(el);
		createRoot(el).render(
			<App {...props} />
		);
	},
});
