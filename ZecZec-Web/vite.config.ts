import path from "path";

import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import { createSvgIconsPlugin } from "vite-plugin-svg-icons";

// Element Plus按需匯入
import AutoImport from "unplugin-auto-import/vite";
import Components from "unplugin-vue-components/vite";
import { ElementPlusResolver } from "unplugin-vue-components/resolvers";

// https://vitejs.dev/config/
export default defineConfig({
  base: process.env.NODE_ENV === "production" ? "/" : "/",
  server: {
    host: "0.0.0.0",
    port: 3000,
    open: true,
    proxy: {
      "/index.php/api": {
        target: "https://www.genentech.icu/",
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/index\.php\/api/, "/index.php/api"),
      },
    },
  },
  resolve: {
    alias: {
      "@": "/src",
    },
  },
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `@use "@/assets/style/element.scss" as *;`,
      },
    },
  },
  plugins: [
    vue(),
    createSvgIconsPlugin({
      // 指定需要快取的圖示資料夾
      iconDirs: [path.resolve(process.cwd(), "src/assets/svg")],
      // 指定symbolId格式
      symbolId: "icon-[dir]-[name]",
    }),
    AutoImport({
      resolvers: [ElementPlusResolver()],
    }),
    Components({
      resolvers: [
        // 配置Element Plus採用saas樣式配色系統
        ElementPlusResolver({ importStyle: "sass" }),
      ],
    }),
  ],
});
