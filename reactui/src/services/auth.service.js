import axios from "axios";
import { API_URL } from './../configs';
import authHeader from "./auth-header";

class AuthService {
  checkLoginStatus() {
    return axios
      .get(API_URL + "checkLogin", { headers: authHeader() })
      .then((response) => {
        return response.data;
      });
  }
  login(email, password) {
    return axios
      .post(API_URL + "login", { email, password })
      .then((response) => {
        if (response.data.data && response.data.data.token) {
          localStorage.setItem("user", JSON.stringify(response.data.data));
        }

        return response.data;
      });
  }

  logout() {
    localStorage.removeItem("user");
    return axios
      .get(API_URL + "logout")
      .then((response) => {

        return response.data;
      });
  }

  register(name, email, password) {
    return axios.post(API_URL + "register", {
      name,
      email,
      password,
    }).then((response) => {
      if (response.data.data && response.data.data.token) {
        localStorage.setItem("user", JSON.stringify(response.data.data));
      }

      return response.data;
    });
  }
}

export default new AuthService();
