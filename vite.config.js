import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        // The Laravel plugin only serves dev/build, and refuses to start its dev server under CI.
        ...(process.env.VITEST
            ? []
            : [
                  laravel({
                      input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/reports/main.tsx'],
                      refresh: true,
                  }),
              ]),
        react(),
    ],
    test: {
        environment: 'jsdom',
        include: ['resources/js/**/*.test.{ts,tsx}'],
        setupFiles: ['resources/js/test-setup.ts'],
    },
});
