const TOKEN_KEY = "www_token";

const isLogin = () => {
    console.log("www_token", localStorage.getItem(TOKEN_KEY));
    return !!localStorage.getItem(TOKEN_KEY);
};

const getToken = () => {
    return localStorage.getItem(TOKEN_KEY);
};

const setToken = (token: string) => {
    localStorage.setItem(TOKEN_KEY, token);
};

const clearToken = () => {
    localStorage.removeItem(TOKEN_KEY);
};

export { isLogin, getToken, setToken, clearToken };
