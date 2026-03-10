import axios from "axios";

const API_BASE = "/api/v1";
const TOKEN_KEY = "gbms_token";
const USER_KEY = "gbms_user";

export const getToken = () => sessionStorage.getItem(TOKEN_KEY);

export const getUser = () => {
    const raw = sessionStorage.getItem(USER_KEY);
    return raw ? JSON.parse(raw) : null;
};

export const isLoggedIn = () => !!getToken();

export async function login(email, password) {
    const res = await axios.post(`${API_BASE}/login`, { email, password });
    sessionStorage.setItem(TOKEN_KEY, res.data.token);
    sessionStorage.setItem(USER_KEY, JSON.stringify(res.data.user));
    return res.data.user;
}

export async function logout() {
    const token = getToken();
    if (token) {
        try {
            await axios.post(
                `${API_BASE}/logout`,
                {},
                {
                    headers: { Authorization: `Bearer ${token}` },
                },
            );
        } catch (_) {}
    }
    sessionStorage.removeItem(TOKEN_KEY);
    sessionStorage.removeItem(USER_KEY);
}

export function authHeaders() {
    const token = getToken();
    return token ? { Authorization: `Bearer ${token}` } : {};
}
