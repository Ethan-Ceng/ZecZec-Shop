import type { Router, LocationQueryRaw } from "vue-router";

import { useUserStore } from "@/store";
import { isLogin } from "@/utils/auth";

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
                } catch (error) {
                    console.error("Failed to fetch user info:", error);
                    await userStore.logout();
                    next({
                        name: "login",
                        query: {
                            redirect: to.fullPath,
                        },
                    });
                }
            }
        } else {
            // 如果用戶未登入，檢查是否需要權限
            if (
                [
                    "Message",
                    "ProjectComments",
                    "Buy",
                    "BuyOrder",
                    "OrderDetail",
                    "Profile",
                    "Account",
                    "MessageNew",
                ].includes(to.name)
            ) {
                next({
                    name: "login",
                    query: {
                        redirect: to.fullPath,
                    },
                });
            } else {
                next();
            }
        }
    });
}
