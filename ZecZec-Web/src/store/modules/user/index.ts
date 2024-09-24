import { defineStore } from "pinia";
import {
    login as userLogin,
    logout as userLogout,
    getUserInfo,
    LoginData,
    getAddress,
} from "@/api/user";
import { setToken, clearToken, getToken } from "@/utils/auth";
import { UserState } from "./types";
import useAppStore from "../app";
import { ElMessage } from "element-plus";

const useUserStore = defineStore("user", {
    state: (): UserState => ({
        token: getToken(),
        userInfo: {},
        address: {},
        coupon: 0,
        getPhone: false,
        invitation: {},
        orderCount: {},
        setting: {},
        sign: {},
    }),

    getters: {
        userDetail(state: UserState): UserState {
            return { ...state.userInfo };
        },
    },

    actions: {
        // Set user's information
        setInfo(partial: Partial<UserState>) {
            this.$patch(partial);
        },

        // Reset user's information
        resetInfo() {
            this.$reset();
        },

        // Get user's information
        async fetchUserInfo() {
            try {
                const { data } = await getUserInfo();
                console.log("getdetail:", data);
                this.setInfo(data);
            } catch (err) {
                throw err;
            }
        },

        async fetchUserAddress(addressId) {
            try {
                const { data } = await getAddress(addressId);
                console.log(data);
                this.$patch({ address: data });
            } catch (err) {
                throw err;
            }
        },

        // Login
        async login(loginForm: LoginData) {
            try {
                return new Promise((resolve, reject) => {
                    userLogin(loginForm)
                        .then(({ data }) => {
                            console.log(data);
                            this.token = data.token;
                            // this.setInfo(data.user_info || {})
                            setToken(data.token);
                            resolve(data);
                        })
                        .catch((error) => {
                            reject(error);
                        });
                });
                // const res = await userLogin(loginForm);
                // this.token = res.data.token;
                // setToken(res.data.token);
            } catch (err) {
                clearToken();
                throw err;
            }
        },
        logoutCallBack() {
            this.resetInfo();
            clearToken();
            window.location.href = "/login";
        },
        // Logout
        async logout() {
            try {
                await userLogout();
            } finally {
                this.logoutCallBack();
            }
        },
    },
});

export default useUserStore;
