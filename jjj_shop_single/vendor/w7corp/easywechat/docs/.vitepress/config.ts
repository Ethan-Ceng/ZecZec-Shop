import path from 'path'
import versions from './versions'

const latest = versions[0]

const nav = [
  {
    text: '首頁',
    link: '/'
  },
  {
    text: '文件',
    activeMatch: `^/([0-9]\.x)/`,
    items: versions.map((version) => ({
      text: version,
      link: `/${version}/`
    }))
  },
  {
    text: '影片',
    link: 'https://wiki.w7.cc/college/collectiondetail/3'
  },
  {
    text: '討論',
    link: 'https://github.com/w7corp/easywechat/discussions'
  },
  {
    text: '贊助',
    link: 'https://github.com/sponsors/overtrue'
  }
]

export const sidebar = versions.reduce(
  (sidebars, version) => ({
    ...sidebars,
    [`/${version}/`]: require(path.join(
      __dirname,
      `../src/${version}/sidebar`
    ))
  }),
  {}
)

export default {
  lang: 'zh-CN',
  title: 'EasyWeChat',
  description: '一個 PHP 微信開發 SDK',
  srcDir: 'src',
  srcExclude: [],
  scrollOffset: 'header',

  head: [
    ['link', { rel: 'icon', href: '/favicon.svg' }],
    ['meta', { name: 'twitter:site', content: '@easywechat' }],
    ['meta', { name: 'twitter:card', content: 'summary' }],
    [
      'meta',
      {
        name: 'twitter:image',
        content: 'https://easywechat/logo.png'
      }
    ]
  ],

  themeConfig: {
    nav,
    sidebar,

    logo: '/logo-icon.svg',

    algolia: {
      indexName: 'easywechat',
      appId: 'X3KJL5SQXD',
      apiKey: '5c5ba71b35c48411f245bef4c695fc36'
      // searchParameters: {
      //   facetFilters: ['version:v3']
      // }
    },

    // carbonAds: {
    //   code: '',
    //   placement: ''
    // },

    socialLinks: [
      { icon: 'github', link: 'https://github.com/w7corp/easywechat' },
      { icon: 'twitter', link: 'https://twitter.com/overtrue' }
    ],

    editLink: {
      pattern:
        'https://github.com/w7corp/EasyWeChat/edit/6.x/docs/src/:path',
      text: '幫助我們改善此頁面！'
    },

    license: {
      text: 'MIT License',
      link: 'https://opensource.org/licenses/MIT'
    },
    copyright: `Copyright © 2013-${new Date().getFullYear()} 微擎 <a class="ml-4" href="https://beian.miit.gov.cn/" target="_blank">皖ICP備19002904號-6</a>`
  },

  vite: {
    define: {
      __VUE_OPTIONS_API__: false
    },
    optimizeDeps: {
      include: ['gsap', 'dynamics.js'],
      exclude: []
    },
    // @ts-ignore
    ssr: {
      external: []
    },
    server: {
      host: true,
      fs: {
        // for when developing with locally linked theme
        allow: ['../..']
      }
    },
    build: {
      minify: 'terser',
      chunkSizeWarningLimit: Infinity
    },
    json: {
      stringify: true
    }
  },

  vue: {
    reactivityTransform: true
  }
}
