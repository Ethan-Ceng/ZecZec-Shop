import { createRouter, createWebHistory } from 'vue-router';

import { REDIRECT_MAIN, NOT_FOUND_ROUTE } from './routes/base';
export const DEFAULT_LAYOUT = () => import('@/layout/default-layout.vue');
import createRouteGuard from './guard';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/login/index.vue'),
      meta: {
        title: '登入',
      },
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/views/register/index.vue'),
      meta: {
        title: '註冊',
      },
    },
    {
      path: '/password',
      name: 'password',
      component: () => import('@/views/login/password.vue'),
      meta: {
        title: '忘記密碼',
      },
    },
    {
      path: '/reset_password',
      name: 'resetPassword',
      component: () => import('@/views/login/reset_password.vue'),
      meta: {
        title: '重設密碼',
      },
    },
    {
      path: '/message',
      name: 'Message',
      component: () => import('@/views/message/index.vue'),
      meta: {
        title: '訊息',
      },
    },
    {
      path: '/',
      name: 'Home',
      component: DEFAULT_LAYOUT,
      children: [
        {
          path: '/',
          name: 'HomeIndex',
          component: () => import('@/views/home/index.vue'),
          meta: {
            title: '首頁',
          },
        },
        {
          path: 'category',
          name: 'Category',
          component: () => import('@/views/category/index.vue'),
          meta: {
            title: '分類',
          },
        },
        {
          path: 'project/:id',
          name: 'Project',
          component: () => import('@/views/project/index.vue'),
          meta: {
            title: '專案',
          },
        },
        {
          path: 'project/:id/faqs',
          name: 'ProjectFAQS',
          component: () => import('@/views/project/faqs.vue'),
          meta: {
            title: '常見問答',
          },
        },
        {
          path: 'project/:id/comments',
          name: 'ProjectComments',
          component: () => import('@/views/project/comments.vue'),
          meta: {
            title: '留言',
          },
        },
        {
          path: 'order/:id', // 產品ID 進入選擇SKU頁面
          name: 'Order',
          component: () => import('@/views/order/index.vue'),
          meta: {
            title: '下單',
          },
        },
        {
          path: 'order/:id/:skuId',
          name: 'OrderConfirm',
          component: () => import('@/views/order/confirm.vue'),
          meta: {
            title: '下單',
          },
        },
        {
          path: 'order/detail/:id',
          name: 'OrderDetail',
          component: () => import('@/views/order/detail.vue'),
          meta: {
            title: '訂單詳情',
          },
        },
        {
          path: 'profile',
          name: 'Profile',
          component: () => import('@/views/profile/index.vue'),
          meta: {
            title: '個人頁面',
          },
        },
        {
          path: 'account',
          name: 'Account',
          component: () => import('@/views/account/index.vue'),
          meta: {
            title: '個人中心',
          },
        },
        {
          path: 'message/new',
          name: 'MessageNew',
          component: () => import('@/views/message/new.vue'),
          meta: {
            title: '傳送訊息',
          },
        },
        {
          path: 'agreement',
          name: 'Agreement',
          component: () => import('@/views/agreement/index.vue'),
          meta: {
            title: '服務條款',
          },
        },
        {
          path: 'privacy',
          name: 'Privacy',
          component: () => import('@/views/privacy/index.vue'),
          meta: {
            title: '隱私權政策',
          },
        },
        {
          path: 'faq',
          name: 'Faq',
          component: () => import('@/views/faq/index.vue'),
          meta: {
            title: '常見問答',
          },
        },
        {
          path: 'about',
          name: 'About',
          component: () => import('@/views/about/index.vue'),
          meta: {
            title: '關於我們',
          },
        },
      ],
    },
    REDIRECT_MAIN,
    NOT_FOUND_ROUTE,
  ],
  scrollBehavior() {
    return { top: 0 };
  },
});

createRouteGuard(router);

export default router;
