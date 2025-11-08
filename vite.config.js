import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  build: {
    outDir: '_production',
    emptyOutDir: false, // Don't clear the directory (in case CSS is there)
    rollupOptions: {
      input: resolve(__dirname, 'src/scripts/main.js'),
      output: {
        entryFileNames: 'main.js',
        format: 'iife', // Immediately Invoked Function Expression for browser compatibility
      },
    },
  },
});
