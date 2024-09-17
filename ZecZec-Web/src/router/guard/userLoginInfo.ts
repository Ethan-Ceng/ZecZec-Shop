import type {Router, LocationQueryRaw} from 'vue-router';

import {useUserStore} from '@/store';
import {isLogin} from '@/utils/auth';

export default function setupUserLoginInfoGuard(router: Router) {
  router.beforeEach(async (to, from, next) => {
    const userStore = useUserStore();
    if (isLogin()) {
      if (userStore?.userDetail?.user_id) {
        next();
      } else {
        try {
          await userStore.fetchUserInfo();
          next();
          return
        } catch (error) {
          await userStore.logout();
          // 判斷頁面是否需要許可權
          if (['Message', 'ProjectComments', 'Buy', 'BuyOrder', 'OrderDetail', 'Profile', 'Account', 'MessageNew'].includes(to.name)) {
            next({
              name: 'login',
              query: {
                redirect: to.name,
                ...to.query,
              } as LocationQueryRaw,
            });
            return
          }
        }
      }
    }
    next();
    // if (to.name === 'login') {
    //   next();
    //   return;
    // }
    // next({
    //   name: 'login',
    //   query: {
    //     redirect: to.name,
    //     ...to.query,
    //   } as LocationQueryRaw,
    // });

  });
}
